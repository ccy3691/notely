<?php

namespace App\Jobs;

use App\Models\Flashcard;
use App\Models\FlashcardTask;
use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use EchoLabs\Prism\Prism;
use EchoLabs\Prism\Enums\Provider;
use EchoLabs\Prism\Schema\ObjectSchema;
use EchoLabs\Prism\Schema\StringSchema;

class GenerateFlashcardsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobID;
    protected $dryRun;

    public function __construct(int $jobID, bool $dryRun)
    {
        $this->jobID = $jobID;
        $this->dryRun = $dryRun;
    }

    public function handle()
    {
        try {
            $job = FlashcardTask::where('id', $this->jobID)->first();
            if (!$job) {
                return;
            }
            // Fetch notes for the category
            $notes = Note::where('category', $job->category)->where('user_id', $job->user_id)->get()->pluck('content_html');


            if (empty($notes)) {
                return;
            }

            $job->update(['status' => 'processing']);


            // Generate ChatGPT prompt
            $prompt = "Create {$job->total_requested} flashcards for the category {$job->category} based on the following content:\n\n{$notes}";


            $schema = new ObjectSchema(
                name: 'flashcards',
                description: 'A structured flashcard response',
                properties: [
                    new StringSchema('question', 'the flashcard question'),
                    new StringSchema('answer', 'the flashcard answer'),
                ],
                requiredFields: ['question', 'answer']
            );
            $chatResponse = null;
            // Call ChatGPT
            if(!$this->dryRun){
                $chatResponse = Prism::structured()
                    ->using('openai', 'gpt-4o-mini')
                    ->withSchema($schema)
                    ->withPrompt($prompt)
                    ->generate();
            } else {
                $chatResponse = (object)[
                    'structured' => [
                        'flashcards' => [
                            [
                                'question' => 'What is the primary function of the nervous system in the human body?',
                                'answer' => 'The nervous system is important in helping to maintain homeostasis (balance) in the human body.'
                            ],
                            [
                                'question' => 'What are neurons and their role in the nervous system?',
                                'answer' => 'Neurons, also called nerve cells, are the basic elements of the nervous system responsible for conducting nerve impulses.'
                            ],
                            [
                                'question' => 'What are the three types of neurons and their functions?',
                                'answer' => '1. Efferent (Motor) neurons convey information from the CNS to muscles and glands. 2. Afferent (Sensory) neurons carry information from sensory receptors to the CNS. 3. Interneurons carry and process sensory information.'
                            ],
                            [
                                'question' => 'What two major components make up the nervous system?',
                                'answer' => 'The two major components of the nervous system are the Central Nervous System (CNS), which includes the brain and spinal cord.'
                            ],
                            [
                                'question' => 'What are neuroglial cells and their function in the nervous system?',
                                'answer' => 'Neuroglial cells provide structural and functional support to neurons in the nervous system.'
                            ]
                        ]
                    ]
                ];
            }

            $response = $chatResponse->structured;

            foreach ($response['flashcards'] as $card) {
                Flashcard::create([
                    'category' => $job->category,
                    'question' => $card['question'],
                    'answer' => $card['answer'],
                    'flashcard_job_id' => $job->id
                ]);

                $job->increment('total_processed');
                
                if($this->dryRun){
                    sleep(20);
                }
            }

            $job->update(['status' => 'finished']);

        } catch (\Exception $e) {
            $job->update(['status' => 'failed']);
            throw $e;
        }
    }
}
