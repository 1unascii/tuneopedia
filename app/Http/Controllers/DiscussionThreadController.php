<?php

namespace App\Http\Controllers;

use App\Models\DiscussionThread;
use Illuminate\Http\Request;

class DiscussionThreadController extends Controller
{
    /**
     * Display form to edit a discussion thread.
     * Requires authorization — only the thread owner can edit.
     */
    public function edit(DiscussionThread $discussionThread)
    {
        $this->authorize('update', $discussionThread);

        return view('discussion-threads.edit', ['discussionThread' => $discussionThread]);
    }

    /**
     * Update a discussion thread's body.
     * Validates input and redirects back to the discussions list.
     */
    public function update(Request $request, DiscussionThread $discussionThread)
    {
        $this->authorize('update', $discussionThread);
        $validated = $request->validate([
            'body' => 'required|string|max:255',
        ], [
            'body.required' => 'The body is required.',
            'body.max' => 'The body must be less than 255 characters.',
        ]);

        $discussionThread->update($validated);

        return redirect('/discussion-threads')->with('success', 'Discussion thread updated successfully');
    }

    /**
     * Display the latest 50 discussion threads with their authors.
     */
    public function index()
    {
        $discussionThreads = DiscussionThread::with('user')
            ->latest()
            ->take(50)
            ->get();

        return view('discussion-threads.index', ['discussionThreads' => $discussionThreads]);
    }

    /**
     * Store a new discussion thread.
     * Creates the thread under the authenticated user via relationship.
     */
    public function store(Request $request)
    {
        $this->authorize('create', DiscussionThread::class);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ], [
            'title.required' => 'The title is required.',
            'title.max' => 'The title must be less than 255 characters.',
            'body.required' => 'The body is required.',
        ]);

        auth()->user()->discussionThreads()->create($validated);

        return redirect('/discussion-threads')->with('success', 'Discussion thread created successfully');
    }

    /**
     * Display a single discussion thread with its replies.
     */
    public function show(DiscussionThread $discussionThread)
    {
        $discussionThread->load(['user', 'discussionReplies.user']);

        return view('discussion-threads.show', ['discussionThread' => $discussionThread]);
    }

    /**
     * Delete a discussion thread.
     * Requires authorization — only the thread owner can delete.
     */
    public function destroy(DiscussionThread $discussionThread)
    {
        $this->authorize('delete', $discussionThread);
        $discussionThread->delete();

        return redirect('/discussion-threads')->with('success', 'Discussion thread deleted successfully');
    }
}
