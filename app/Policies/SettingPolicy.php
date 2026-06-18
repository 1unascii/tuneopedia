<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
{
    /**
     * Any user can view settings.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Any user can view a setting.
     */
    public function view(?User $user, Setting $setting): bool
    {
        return true;
    }

    /**
     * Any authenticated user can create a setting.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the setting owner can update it.
     */
    public function update(User $user, Setting $setting): bool
    {
        return $setting->user_id === $user->id;
    }

    /**
     * Only the setting owner or an admin can delete it.
     */
    public function delete(User $user, Setting $setting): bool
    {
        return $setting->user_id === $user->id || $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Setting $setting): bool
    {
        return $setting->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Setting $setting): bool
    {
        return false;
    }
}
