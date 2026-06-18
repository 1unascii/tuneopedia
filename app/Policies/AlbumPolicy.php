<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;

class AlbumPolicy
{
    /**
     * Any authenticated user can view albums.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Any authenticated user can view an album.
     */
    public function view(User $user, Album $album): bool
    {
        return true;
    }

    /**
     * Any authenticated user can create an album.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the album creator can update it.
     */
    public function update(User $user, Album $album): bool
    {
        return $album->user_id === $user->id;
    }

    /**
     * Only the album creator or an admin can delete it.
     */
    public function delete(User $user, Album $album): bool
    {
        return $album->user_id === $user->id || $user->is_admin;
    }

    /**
     * Only the album creator can add tracks to it.
     */
    public function addTrack(User $user, Album $album): bool
    {
        return $album->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Album $album): bool
    {
        return $album->user_id === $user->id;
    }

    /**
     * Only the album creator or an admin can force delete an album.
     */
    public function forceDelete(User $user, Album $album): bool
    {
        return $album->user_id === $user->id || $user->is_admin;
    }
}
