<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Composer;
use App\Models\Setting;
use App\Models\Tune;
use App\Models\TuneType;
use App\Services\AbcParser;
use Illuminate\Http\Request;

/**
 * Handles collection listing, display, and creation.
 *
 * The index shows all collections with search and pagination.
 * The show page displays a collection's tunes using x-tune-list.
 * The create/store flow allows users to import tunes from ABC files or pasted text.
 */
class CollectionController extends Controller
{
    /**
     * Display collections in a paginated, searchable list.
     */
    public function index(Request $request)
    {
        $query = Collection::withCount('tunes');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('shared')) {
            if ($request->boolean('shared')) {
                $query->where('is_shared', true);
            } else {
                $query->where('user_id', auth()->id());
            }
        } else {
            $query->where(function ($q) {
                $q->where('is_shared', true);
                if (auth()->check()) {
                    $q->orWhere('user_id', auth()->id());
                }
            });
        }

        $collections = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('collections.index', ['collections' => $collections]);
    }

    /**
     * Display a single collection's tunes grouped by tune type.
     */
    public function show(Collection $collection)
    {
        $tuneTypes = $this->getTuneTypesForCollection($collection);

        return view('collections.show', [
            'collection' => $collection,
            'tuneTypes' => $tuneTypes,
        ]);
    }

    /**
     * Show the form for creating a new collection.
     */
    public function create()
    {
        return view('collections.create');
    }

    /**
     * Store a new collection from ABC text or uploaded files.
     *
     * Flow:
     *   1. Validate inputs (name required, abc text or file required)
     *   2. Collect ABC text from file uploads and/or pasted text
     *   3. Parse ABC text into individual tunes using AbcParser
     *   4. For each parsed tune:
     *      a. Find existing tune by name + tune type, or create a new one
     *      b. Create a setting with the ABC notation if tune is new
     *      c. Attach the tune to the collection with a position
     *   5. Redirect to the collection show page with results
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_shared' => 'nullable',
            'abc_text' => 'nullable|string',
            'abc_files' => 'nullable|array',
            'abc_files.*' => 'file',
        ]);

        // Collect ABC text from uploads and/or paste
        $abcText = '';

        if ($request->hasFile('abc_files')) {
            foreach ($request->file('abc_files') as $file) {
                $abcText .= file_get_contents($file->getRealPath()) . "\n";
            }
        }

        if ($request->filled('abc_text')) {
            $abcText .= $request->input('abc_text');
        }

        if (empty(trim($abcText))) {
            return back()->withErrors(['abc_text' => 'Please upload an ABC file or paste ABC notation.'])->withInput();
        }

        // Parse the ABC text into individual tunes
        $parser = new AbcParser();
        $parsedTunes = $parser->parse($abcText);

        if (empty($parsedTunes)) {
            return back()->withErrors(['abc_text' => 'No valid tunes found in the ABC text.'])->withInput();
        }

        // Create the collection
        $collection = Collection::create([
            'name' => $request->input('name'),
            'author' => $request->input('author'),
            'description' => $request->input('description'),
            'user_id' => auth()->id(),
            'is_shared' => $request->has('is_shared'),
        ]);

        // Process each parsed tune — find or create, then attach to collection
        $results = ['created' => 0, 'existing' => 0, 'skipped' => 0];
        $position = 1;

        foreach ($parsedTunes as $tuneData) {
            // Skip tunes without valid ABC notation (must contain barlines)
            if (! $tuneData['abc_body'] || ! str_contains($tuneData['abc_body'], '|')) {
                $results['skipped']++;
                continue;
            }

            // Find or create the tune type from the R: header
            $tuneType = null;
            if ($tuneData['type']) {
                $typeName = ucfirst(strtolower(trim($tuneData['type'])));
                $tuneType = TuneType::firstOrCreate(['name' => $typeName]);
            }

            // Check if this tune already exists in the DB
            $existingTune = Tune::where('name', $tuneData['name'])
                ->when($tuneType, fn ($q) => $q->where('tune_type_id', $tuneType->id))
                ->first();

            if ($existingTune) {
                $tune = $existingTune;
                $results['existing']++;
            } else {
                // Find or create the composer from the C: header
                $composer = null;
                if ($tuneData['composer']) {
                    $composer = Composer::firstOrCreate(['name' => trim($tuneData['composer'])]);
                }

                // Create the tune
                $tune = Tune::create([
                    'name' => $tuneData['name'],
                    'tune_type_id' => $tuneType?->id,
                    'composer_id' => $composer?->id,
                    'origin' => $tuneData['origin'],
                    'source' => $tuneData['source'],
                ]);

                // Create its first setting using the parsed ABC data
                Setting::create([
                    'tune_id' => $tune->id,
                    'user_id' => auth()->id(),
                    'name' => $tuneData['name'],
                    'time_signature' => $tuneData['time_signature'] ?? '4/4',
                    'default_note_length' => $tuneData['default_note_length'] ?? '1/8',
                    'key_signature' => $tuneData['key_signature'],
                    'abc_transcription' => $tuneData['abc_body'],
                    'source' => $tuneData['source'],
                    'book' => $tuneData['book'],
                    'transcription_credit' => $tuneData['transcription_credit'],
                    'notes' => $tuneData['notes'],
                    'history' => $tuneData['history'],
                    'origin' => $tuneData['origin'],
                    'area' => $tuneData['area'],
                    'parts' => $tuneData['parts'],
                    'lyrics' => $tuneData['lyrics'],
                    'discography' => $tuneData['discography'],
                ]);

                $results['created']++;
            }

            // Attach tune to collection with sequential position
            $collection->tunes()->attach($tune->id, ['position' => $position]);
            $position++;
        }

        $message = "Collection created: {$results['created']} new tunes, {$results['existing']} existing, {$results['skipped']} skipped.";

        return redirect()->route('collections.show', $collection)->with('success', $message);
    }

    /**
     * Get tune types with their tunes for a given collection.
     */
    private function getTuneTypesForCollection(Collection $collection)
    {
        $tuneIds = $collection->tunes()->pluck('tunes.id');

        return TuneType::whereHas('tunes', fn ($q) => $q->whereIn('tunes.id', $tuneIds)->has('settings'))
            ->with(['tunes' => function ($q) use ($tuneIds) {
                $q->whereIn('tunes.id', $tuneIds)
                    ->has('settings')
                    ->withCount('settings')
                    ->with('topSetting')
                    ->orderBy('name');
            }])->get();
    }
}
