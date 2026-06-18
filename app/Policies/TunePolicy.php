<?php

namespace App\Policies;

use App\Models\Tune;
use App\Models\User;

class TunePolicy
{
    /**
     * Any user can view tunes.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Any user can view a tune.
     */
    public function view(?User $user, Tune $tune): bool
    {
        return true;
    }

    /**
     * Any authenticated user can create a tune.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Tunes cannot be updated directly.
     */
    public function update(User $user, Tune $tune): bool
    {
        return false;
    }

    /**
     * The uploader can delete a tune only if it has no settings. Admin can always delete.
     */
    public function delete(User $user, Tune $tune): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $tune->settings()->count() === 0;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tune $tune): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tune $tune): bool
    {
        return false;
    }
}
