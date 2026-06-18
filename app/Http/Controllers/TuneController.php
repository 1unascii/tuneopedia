<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Instrument;
use App\Models\Tune;
use App\Models\TuneType;
use Illuminate\Http\Request;

class TuneController extends Controller
{
    /**
     * Display all tunes grouped by tune type in a tabbed interface.
     * Optionally filters out tunes with no settings via query parameter.
     * Removes empty tune types from the results.
     */
    public function index()
    {
        $tuneTypes = TuneType::whereHas('tunes', fn ($q) => $q->has('settings'))
            ->with(['tunes' => function ($q) {
                $q->has('settings')
                    ->withCount('settings')
                    ->with('topSetting')
                    ->orderBy('name');
            }])->get();

        return view('tunes.index', ['tuneTypes' => $tuneTypes]);
    }

    /**
     * Display a tune with all its settings, ordered by total votes descending.
     */
    public function show(Tune $tune)
    {
        $tune->load(['tuneType', 'settings' => function ($query) {
            $query->withSum('votes', 'vote_value')->orderByDesc('votes_sum_vote_value');
        }]);

        return view('tunes.show', ['tune' => $tune]);
    }

    /**
     * Show form for creating a new tune with its first setting.
     */
    public function create()
    {
        return view('tunes.create', [
            'tuneTypes' => TuneType::orderBy('name')->get(),
            'instruments' => Instrument::all(),
            'albums' => Album::with('tracks')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new tune and its first setting.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tune_type' => 'required|string|max:255',
            'composer' => 'nullable|string|max:255',
            'tune_origin' => 'nullable|string|max:255',
            'tune_source' => 'nullable|string',
        ]);

        $tuneType = TuneType::firstOrCreate(['name' => $validated['tune_type']]);

        $tune = Tune::create([
            'name' => $validated['name'],
            'tune_type_id' => $tuneType->id,
            'composer' => $validated['composer'] ?? null,
            'origin' => $validated['tune_origin'] ?? null,
            'source' => $validated['tune_source'] ?? null,
        ]);

        // Delegate first setting creation to SettingController
        app(SettingController::class)->store($request, $tune);

        return redirect()->route('tunes.show', $tune)->with('success', 'Tune created successfully');
    }

    /**
     * Delete a tune and all its settings.
     */
    public function destroy(Tune $tune)
    {
        $this->authorize('delete', $tune);

        $tune->settings()->delete();
        $tune->delete();

        return redirect()->route('tunes.index')->with('success', 'Tune and all its settings deleted successfully');
    }
}
