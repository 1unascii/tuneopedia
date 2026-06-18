<?php

namespace App\Http\Controllers;

use App\Models\Tune;

/**
 * Handles user favorites — a many-to-many relationship between users and tunes
 * stored in the `favorites` pivot table.
 *
 * The User model has a favoriteTunes() belongsToMany relationship that makes
 * attach/detach available for toggling favorites without needing a Favorite model.
 */
class FavoriteController extends Controller
{
    /**
     * Display the authenticated user's favorite tunes.
     *
     * Uses the favoriteTunes() relationship on the user to query through
     * the favorites pivot table. Eager-loads tuneType for display.
     * Orders by date_added (the pivot table's timestamp) descending.
     */
    public function index()
    {
        $favorites = auth()->user()->favoriteTunes()
            ->with('tuneType', 'topSetting')
            ->latest('favorites.date_added')
            ->get();

        return view('favorites.index', ['favorites' => $favorites]);
    }

    /**
     * Toggle a tune as favorite for the authenticated user.
     *
     * Checks if the user already has this tune favorited by querying the pivot.
     * - If it exists: detach() removes the row from the favorites table.
     * - If it doesn't: attach() inserts a new row into the favorites table.
     *
     * Uses back() to redirect to wherever the user came from (tunes index,
     * tune show page, favorites page, etc.) with a success toast.
     */
    public function toggle(Tune $tune)
    {
        $user = auth()->user();

        if ($user->favoriteTunes()->where('tune_id', $tune->id)->exists()) {
            $user->favoriteTunes()->detach($tune->id);
            $status = 'removed';
        } else {
            $user->favoriteTunes()->attach($tune->id);
            $status = 'added';
        }

        // Return JSON for fetch requests, redirect for regular form submissions
        if (request()->wantsJson()) {
            return response()->json(['status' => $status]);
        }

        return back()->with('success', $status === 'added' ? 'Added to favorites' : 'Removed from favorites');
    }
}
