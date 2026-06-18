<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Instrument;
use App\Models\Setting;
use App\Models\Tune;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a single setting with its ABC notation rendered.
     */
    public function show(Setting $setting)
    {
        $setting->load('tune.tuneType');

        return view('settings.show', ['setting' => $setting]);
    }

    /**
     * Show form for adding a new setting to an existing tune.
     */
    public function create(Tune $tune)
    {
        return view('settings.create', [
            'tune' => $tune,
            'instruments' => Instrument::all(),
            'albums' => Album::with('tracks')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new setting for a tune.
     */
    public function store(Request $request, Tune $tune)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'time_signature' => 'required|string|max:7',
            'default_note_length' => 'required|string|max:10',
            'key_signature' => 'required|string|max:50',
            'abc_transcription' => 'required|string',
            'instrument_id' => 'nullable|integer|exists:instruments,id',
            'tempo' => 'nullable|integer|min:40|max:300',
            'notes' => 'nullable|string',
            'source' => 'nullable|string',
            'origin' => 'nullable|string|max:255',
            'history' => 'nullable|string',
            'book' => 'nullable|string|max:255',
            'discography' => 'nullable|string|max:255',
            'transcription_credit' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'parts' => 'nullable|string|max:100',
            'lyrics' => 'nullable|string',
        ]);

        $tune->settings()->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('tunes.show', $tune)->with('success', 'Setting added successfully');
    }

    /**
     * Show form for editing an existing setting.
     */
    public function edit(Setting $setting)
    {
        $this->authorize('update', $setting);
        $setting->load('tune');

        return view('settings.edit', [
            'setting' => $setting,
            'tune' => $setting->tune,
            'instruments' => Instrument::all(),
            'albums' => Album::with('tracks')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update an existing setting.
     */
    public function update(Request $request, Setting $setting)
    {
        $this->authorize('update', $setting);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'time_signature' => 'required|string|max:7',
            'default_note_length' => 'required|string|max:10',
            'key_signature' => 'required|string|max:50',
            'abc_transcription' => 'required|string',
            'instrument_id' => 'nullable|integer|exists:instruments,id',
            'tempo' => 'nullable|integer|min:40|max:300',
            'notes' => 'nullable|string',
            'source' => 'nullable|string',
            'origin' => 'nullable|string|max:255',
            'history' => 'nullable|string',
            'book' => 'nullable|string|max:255',
            'discography' => 'nullable|string|max:255',
            'transcription_credit' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'parts' => 'nullable|string|max:100',
            'lyrics' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()->route('tunes.show', $setting->tune)->with('success', 'Setting updated successfully');
    }

    /**
     * Delete a setting. If it was the last setting on the tune, delete the tune too.
     */
    public function destroy(Setting $setting)
    {
        $this->authorize('delete', $setting);

        /** @var \App\Models\Tune $tune */
        $tune = $setting->tune;
        $setting->delete();

        // If that was the last setting, delete the tune
        if ($tune->settings()->count() === 0) {
            $tune->delete();

            return redirect()->route('tunes.index')->with('success', 'Setting and tune deleted successfully');
        }

        return redirect()->route('tunes.show', $tune)->with('success', 'Setting deleted successfully');
    }
}
