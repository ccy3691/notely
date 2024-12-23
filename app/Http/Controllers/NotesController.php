<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotesController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $user = auth()->user();

        return inertia('Notes/Index', [
            'search_token' => $user->meilisearch_token
        ]);
    }

    public function create()
    {
        return inertia('Notes/Create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        $note = new Note();
        $note->category = $request->category;
        $note->content = "{\"ops\":[{\"insert\":\"\\n\"}]}";
        $note->content_html = "";
        $note->title = $request->title;
        $note->user_id = auth()->id(); // Associate with logged-in user
        $note->save();

        return to_route('notes.edit', $note->id);
    }

    public function edit(Note $note)
    {
        // Return the note to the view
        return inertia('Notes/Edit', [
            'note' => $note
        ]);
    }

    public function update(Request $request, Note $note)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required', // The content will be handled as a Delta object
            'category' => 'required|string'
        ]);

        // Update the note with the new values
        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'content_html' => $note->getContentHtmlAttribute(),
            'category' => $request->category
        ]);

        // Return success response with updated note via Inertia
        return redirect()->route('notes.edit', ['note' => $note->id])
        ->with('success', 'Note updated successfully');
    }
}
