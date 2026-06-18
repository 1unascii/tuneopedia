<?php

namespace App\Policies;

use App\Models\DiscussionThread;
use App\Models\User;

class DiscussionThreadPolicy
{
    /**
     * CAN USER VEIW ANY THREAD?.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * CAN USER VEIW THIS THREAD?.
     */
    public function view(User $user, DiscussionThread $discussionThread): bool
    {
        return true;
    }

    /**
     * CAN USER CREATE A NEW THREAD?.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * CAN USER UPDATE THIS THREAD?.
     */
    public function update(User $user, DiscussionThread $discussionThread): bool
    {
        return $discussionThread->user()->is($user);
    }

    /**
     * CAN USER DELETE THIS THREAD?.
     */
    public function delete(User $user, DiscussionThread $discussionThread): bool
    {
        return $discussionThread->user()->is($user) || $user->is_admin;
    }

    /**
     * CAN USER RESTORE THIS THREAD?.
     */
    public function restore(User $user, DiscussionThread $discussionThread): bool
    {
        return $discussionThread->user()->is($user);
    }

    /**
     * CAN USER FORCE DELETE THIS THREAD?.
     */
    public function forceDelete(User $user, DiscussionThread $discussionThread): bool
    {
        return $user->is_admin;
    }
}
