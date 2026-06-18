<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AlbumController extends Controller
{
    /**
     * Display all albums.
     */
    public function index()
    {
        $albums = Album::withCount('tracks')->orderBy('name')->get();

        return view('albums.index', ['albums' => $albums]);
    }

    /**
     * Show form for creating a new album.
     */
    public function create()
    {
        return view('albums.create');
    }

    /**
     * Store a new album.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'cover_art' => 'nullable|file|max:10240',
            'tracks' => 'nullable|array',
            'tracks.*.name' => 'required|string|max:255',
            'tracks.*.track_number' => 'required|integer|min:1|max:99',
        ]);

        $data = [
            'name' => $request->input('name'),
            'artist' => $request->input('artist'),
            'user_id' => auth()->id(),
        ];

        if ($request->hasFile('cover_art')) {
            $file = $request->file('cover_art');
            $ext = $file->getClientOriginalExtension();
            $filename = Str::random(32) . '.' . $ext;
            $file->move(public_path('images/album_covers'), $filename);
            $data['cover_art'] = 'images/album_covers/' . $filename;
        }

        $album = Album::create($data);

        // Delegate track creation to TrackController
        if ($request->has('tracks')) {
            $trackController = app(TrackController::class);
            foreach ($request->input('tracks') as $track) {
                if (!empty($track['name'])) {
                    $trackRequest = new Request($track);
                    $trackController->store($trackRequest, $album);
                }
            }
        }

        return redirect()->route('albums.index')->with('success', 'Album created successfully');
    }

    /**
     * Display a single album with its tracks.
     */
    public function show(Album $album)
    {
        $album->load(['tracks' => fn ($q) => $q->orderBy('track_number')]);

        return view('albums.show', ['album' => $album]);
    }

    /**
     * Show form for editing an album.
     */
    public function edit(Album $album)
    {
        $this->authorize('update', $album);

        return view('albums.edit', ['album' => $album]);
    }

    /**
     * Update an album.
     */
    public function update(Request $request, Album $album)
    {
        $this->authorize('update', $album);

        $request->validate([
            'name' => 'required|string|max:255',
            'artist' => 'nullable|string|max:255',
            'cover_art' => 'nullable|file|max:10240',
        ]);

        $data = [
            'name' => $request->input('name'),
            'artist' => $request->input('artist'),
        ];

        if ($request->hasFile('cover_art')) {
            // Delete old cover if exists
            if ($album->cover_art && file_exists(public_path($album->cover_art))) {
                unlink(public_path($album->cover_art));
            }
            $ext = $request->file('cover_art')->getClientOriginalExtension();
            $filename = Str::random(32) . '.' . $ext;
            $request->file('cover_art')->move(public_path('images/album_covers'), $filename);
            $data['cover_art'] = 'images/album_covers/' . $filename;
        }

        $album->update($data);

        return redirect()->route('albums.show', $album)->with('success', 'Album updated successfully');
    }

    /**
     * Delete an album and its tracks.
     */
    public function destroy(Album $album)
    {
        $this->authorize('delete', $album);

        // Delete cover image file
        if ($album->cover_art && file_exists(public_path($album->cover_art))) {
            unlink(public_path($album->cover_art));
        }

        $album->tracks()->delete();
        $album->delete();

        return redirect()->route('albums.index')->with('success', 'Album deleted successfully');
    }
}
