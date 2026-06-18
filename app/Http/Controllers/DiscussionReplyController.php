<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DiscussionReply;
use App\Models\DiscussionThread;
use Illuminate\Http\Request;

class DiscussionReplyController extends Controller
{
    /**
     * Store a new reply on a discussion thread.
     */
    public function store(Request $request, DiscussionThread $discussionThread)
    {
        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $discussionThread->discussionReplies()->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return redirect("/discussion-threads/{$discussionThread->id}")->with('success', 'Reply posted successfully');
    }

    /**
     * Show form for editing a reply.
     */
    public function edit(DiscussionReply $discussionReply)
    {
        $this->authorize('update', $discussionReply);
        $discussionReply->load('discussionThread');

        return view('discussion-replies.edit', ['reply' => $discussionReply]);
    }

    /**
     * Update a reply.
     */
    public function update(Request $request, DiscussionReply $discussionReply)
    {
        $this->authorize('update', $discussionReply);

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $discussionReply->update($validated);

        return redirect("/discussion-threads/{$discussionReply->discussion_thread_id}")->with('success', 'Reply updated successfully');
    }

    /**
     * Delete a reply.
     */
    public function destroy(DiscussionReply $discussionReply)
    {
        $this->authorize('delete', $discussionReply);

        $threadId = $discussionReply->discussion_thread_id;
        $discussionReply->delete();

        return redirect("/discussion-threads/{$threadId}")->with('success', 'Reply deleted successfully');
    }
}
