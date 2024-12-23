<?php

namespace App\Http\Controllers;

use App\Models\Flashcard;
use App\Models\FlashcardTask;
use Illuminate\Http\Request;
use App\Jobs\GenerateFlashcardsJob;

class FlashcardController extends Controller
{
    public function index()
    {
        // Get all flashcard jobs for the authenticated user
        $jobs = FlashcardTask::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return inertia('Flashcards/Index', [
            'jobs' => $jobs,
        ]);
    }

    // Store a new flashcard for the authenticated user
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'category' => 'required|string',
            'content' => 'required|string',
        ]);

        // Create a new flashcard for the authenticated user
        $flashcard = new Flashcard();
        $flashcard->category = $request->category;
        $flashcard->content = $request->content;
        $flashcard->user_id = auth()->id();  // Automatically associate with logged-in user
        $flashcard->save();

        // Redirect or return a response
        return redirect()->route('flashcards.index');
    }

    public function createFlashcardJob(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'count' => 'required|integer|min:1',
            'dryRun' => 'boolean'
        ]);
        

        $job = FlashcardTask::create([
            'category' => $request->category,
            'total_requested' => $request->count,
            'user_id' => auth()->id()
        ]);

        GenerateFlashcardsJob::dispatch($job->id, $request->dryRun);

        return back()->with('success', 'Note updated successfully');
    }

    public function getFlashcardJobProgress($jobId)
    {
        $job = FlashcardTask::find($jobId);

        if (!$job) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        return response()->json([
            'status' => $job->status,
            'total_requested' => $job->total_requested,
            'total_processed' => $job->total_processed,
        ]);
    }

    public function show($id)
    {
        // Get the flashcard job
        $job = FlashcardTask::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Get all flashcards for this job
        $flashcards = Flashcard::where('flashcard_job_id', $id)->get();

        return inertia('Flashcards/Show', [
            'job' => $job,
            'flashcards' => $flashcards,
        ]);
    }
}
