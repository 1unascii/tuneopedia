<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    /**
     * Display all tracks for an album.
     */
    public function index(Album $album)
    {
        $tracks = $album->tracks()->orderBy('track_number')->get();

        return view('tracks.index', ['album' => $album, 'tracks' => $tracks]);
    }

    /**
     * Show form for adding a track to an album.
     */
    public function create(Album $album)
    {
        $this->authorize('addTrack', $album);

        return view('tracks.create', ['album' => $album]);
    }

    /**
     * Store a new track for an album.
     */
    public function store(Request $request, Album $album)
    {
        $this->authorize('addTrack', $album);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'track_number' => 'required|integer|min:1|max:99',
        ]);

        $album->tracks()->create($validated);

        return redirect()->route('albums.show', $album)->with('success', 'Track added successfully');
    }

    /**
     * Display a single track.
     */
    public function show(Album $album, Track $track)
    {
        return view('tracks.show', ['album' => $album, 'track' => $track]);
    }

    /**
     * Show form for editing a track.
     */
    public function edit(Album $album, Track $track)
    {
        $this->authorize('update', $album);

        return view('tracks.edit', ['album' => $album, 'track' => $track]);
    }

    /**
     * Update a track.
     */
    public function update(Request $request, Album $album, Track $track)
    {
        $this->authorize('update', $album);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'track_number' => 'required|integer|min:1|max:99',
        ]);

        $track->update($validated);

        return redirect()->route('albums.show', $album)->with('success', 'Track updated successfully');
    }

    /**
     * Delete a track.
     */
    public function destroy(Album $album, Track $track)
    {
        $this->authorize('delete', $album);

        $track->delete();

        return redirect()->route('albums.show', $album)->with('success', 'Track deleted successfully');
    }
}
