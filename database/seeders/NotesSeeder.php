<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Note;
use Faker\Factory as Faker;

class NotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        //Generate 50 notes with random words in the content
        for ($i = 0; $i < 50; $i++) {
            $cont = $this->generateRandomContent($faker);
            Note::create([
                'user_id' => 1, // Link to user 1
                'title' => 'Note ' . ($i + 1),
                'content' => "{\"ops\":[{\"insert\":\"" . $cont . "\"}]}", // Random content
                'content_html' => $cont,
                'category' => $i % 2 === 0 ? 'Work' : 'Personal', // Alternate categories
            ]);
        }
    }

    /**
     * Generate random content with words.
     *
     * @param Faker\Generator $faker
     * @return string
     */
    private function generateRandomContent($faker)
    {
        $words = [];

        // Generate 20 random words
        for ($i = 0; $i < 20; $i++) {
            $words[] = $faker->word();
        }

        // Join the words into a sentence and return it
        return implode(' ', $words) . '.';
    }
}
