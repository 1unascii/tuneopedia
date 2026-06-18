<?php

namespace App\Policies;

use App\Models\DiscussionReply;
use App\Models\User;

class DiscussionReplyPolicy
{
    /**
     * Any user can view replies.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Any user can view a reply.
     */
    public function view(?User $user, DiscussionReply $discussionReply): bool
    {
        return true;
    }

    /**
     * Any authenticated user can create a reply.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the reply owner or an admin can update it.
     */
    public function update(User $user, DiscussionReply $discussionReply): bool
    {
        return $discussionReply->user_id === $user->id || $user->is_admin;
    }

    /**
     * Only the reply owner or an admin can delete it.
     */
    public function delete(User $user, DiscussionReply $discussionReply): bool
    {
        return $discussionReply->user_id === $user->id || $user->is_admin;
    }

    /**
     * Only the reply owner can restore it.
     */
    public function restore(User $user, DiscussionReply $discussionReply): bool
    {
        return $discussionReply->user_id === $user->id;
    }

    /**
     * The reply owner or an admin can force delete.
     */
    public function forceDelete(User $user, DiscussionReply $discussionReply): bool
    {
        return $discussionReply->user_id === $user->id || $user->is_admin;
    }
}
