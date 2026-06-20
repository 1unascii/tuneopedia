@props(['mode', 'tune' => null, 'setting' => null, 'tuneTypes' => [], 'instruments' => [], 'albums' => []])

{{--
    Tune Form Component
    Modes: 'create-tune', 'add-setting', 'edit-setting'

    create-tune: All fields for tune + first setting
    add-setting: Setting fields only (tune already exists)
    edit-setting: Pre-populated setting fields for editing

    Top fields: Title, Tune Type, Default Note Length, Mode, Key, Instrument
    Additional details: hidden behind a collapsible group
--}}

<div x-data="tuneForm({
    mode: '{{ $mode }}',
    tuneId: {{ $tune?->id ?? 'null' }},
    settingId: {{ $setting?->id ?? 'null' }},
    title: '{{ addslashes($setting->name ?? $tune->name ?? '') }}',
    tuneType: '{{ $tune->tuneType->name ?? '' }}',
    timeSignature: '{{ $setting->time_signature ?? '4/4' }}',
    defaultNoteLength: '{{ $setting->default_note_length ?? '1/8' }}',
    keySignature: '{{ $setting->key_signature ?? 'G' }}',
    abc: {{ json_encode($setting->abc_transcription ?? '') }},
    instrumentId: {{ $setting->instrument_id ?? 1 }},
    tempo: {{ $setting->tempo ?? 120 }},
})" class="tune-form">

    @if($mode === 'create-tune')
        <form method="POST" action="{{ route('tunes.store') }}" @submit.prevent="submitForm">
    @elseif($mode === 'add-setting')
        <form method="POST" action="{{ route('settings.store', $tune) }}" @submit.prevent="submitForm">
    @else
        <form method="POST" action="{{ route('settings.update', $setting) }}" @submit.prevent="submitForm">
            @method('PUT')
    @endif
        @csrf

        {{-- ═══ PRIMARY FIELDS (always visible) ═══ --}}
        <div class="tune-form-grid">
            {{-- Title --}}
            <div class="tune-form-field">
                <label for="tune_title">Title</label>
                <input type="text" id="tune_title" name="name" x-model="title"
                    @input="renderAbc()" placeholder="Tune title" class="input input-bordered w-full">
            </div>

            {{-- Tune Type (only for create-tune) --}}
            @if($mode === 'create-tune')
            <div class="tune-form-field">
                <label for="tune_type">Tune Type</label>
                <select id="tune_type" name="tune_type" x-model="tuneType" @change="renderAbc()" class="select select-bordered w-full">
                    <option value="">Select type...</option>
                    @foreach($tuneTypes as $type)
                        <option value="{{ $type->name }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Time Signature --}}
            <div class="tune-form-field">
                <label for="metre">Time Signature</label>
                <select id="metre" name="time_signature" x-model="timeSignature" @change="renderAbc()" class="select select-bordered w-full">
                    <option value="4/4">4/4</option>
                    <option value="6/8">6/8</option>
                    <option value="9/8">9/8</option>
                    <option value="12/8">12/8</option>
                    <option value="3/4">3/4</option>
                    <option value="2/4">2/4</option>
                </select>
            </div>

            {{-- Default Note Length --}}
            <div class="tune-form-field">
                <label for="default_note_length">Default Note Length</label>
                <select id="default_note_length" name="default_note_length" x-model="defaultNoteLength" @change="renderAbc()" class="select select-bordered w-full">
                    <option value="1/4">1/4</option>
                    <option value="1/8">1/8</option>
                    <option value="1/16">1/16</option>
                </select>
            </div>

            {{-- Mode --}}
            <div class="tune-form-field">
                <label for="tune_mode_input">Mode</label>
                <select id="tune_mode_input" x-model="mode_select" @change="updateKeyOptions()" class="select select-bordered w-full">
                    <option value="major">Major / Ionian</option>
                    <option value="minor">Minor</option>
                    <option value="dorian">Dorian</option>
                    <option value="mixolydian">Mixolydian</option>
                </select>
            </div>

            {{-- Key --}}
            <div class="tune-form-field">
                <label for="key">Key</label>
                <select id="key" name="key_signature" x-model="keySignature" @change="renderAbc()" class="select select-bordered w-full">
                    <template x-for="k in keyOptions" :key="k">
                        <option :value="k" x-text="k" :selected="k === keySignature"></option>
                    </template>
                </select>
            </div>

            {{-- Instrument --}}
            <div class="tune-form-field">
                <label for="playback-instrument">Instrument</label>
                <select id="playback-instrument" name="instrument_id" x-model="instrumentId" @change="renderAbc()" class="select select-bordered w-full">
                    @foreach($instruments as $instrument)
                        <option value="{{ $instrument->id }}" data-midi="{{ $instrument->midi_program }}">{{ $instrument->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- ═══ ABC NOTATION ═══ --}}
        <div class="tune-form-field-wide mt-4">
            <label for="abc">ABC Notation</label>
            <textarea id="abc" name="abc_transcription" x-model="abc" rows="10"
                @keypress="handleKeyPress($event)"
                @input="renderAbc()"
                @select="handleSelection()"
                @click="handleSelection()"
                spellcheck="false"
                class="textarea textarea-bordered w-full font-mono"
                placeholder="Enter ABC notation here..."></textarea>
        </div>

        {{-- Hidden tempo input so it saves to DB --}}
        <input type="hidden" name="tempo" :value="tempo">

        <div x-data="{ detailsOpen: false }">

        {{-- ═══ PLAYBACK CONTROLS ═══ --}}
        <div class="tune-form-playback mt-4">
            {{-- Tempo Knob --}}
            <div class="knob-container">
                <label>Tempo</label>
                <div class="circular-knob" x-data="circularKnob({
                    value: tempo, min: 40, max: 300, step: 5,
                    onChange(v) { $dispatch('tempo-change', { value: v }) }
                })" @tempo-change.window="tempo = $event.detail.value; renderAbc()">
                    <canvas x-ref="knobCanvas" width="64" height="64"
                        @mousedown="startDrag($event)"
                        @touchstart.prevent="startDrag($event)"></canvas>
                    <span class="knob-value" x-text="value"></span>
                </div>
            </div>

            {{-- Volume Knob (per-keystroke playback) --}}
            <div class="knob-container">
                <label>Volume</label>
                <div class="circular-knob" x-data="circularKnob({
                    value: 50, min: 0, max: 100, step: 1,
                    onChange(v) { $dispatch('volume-change', { value: v }) }
                })" @volume-change.window="playbackVolume = $event.detail.value">
                    <canvas x-ref="knobCanvas" width="64" height="64"
                        @mousedown="startDrag($event)"
                        @touchstart.prevent="startDrag($event)"></canvas>
                    <span class="knob-value" x-text="value"></span>
                </div>
            </div>

            {{-- Play Selection Button --}}
            <div class="play-selection-container">
                <button type="button" x-show="hasSelection" @click="playSelection()"
                    class="btn btn-sm btn-primary">
                    Play Selection
                </button>
            </div>
        </div>

        {{-- ═══ TABLATURE CONTROLS ═══ --}}
        <div class="flex items-center gap-3 mt-4">
            <label class="flex items-center gap-1 cursor-pointer text-sm">
                <input type="checkbox" class="checkbox checkbox-sm"
                    x-model="showTablature" @change="renderAbc()">
                Tablature
            </label>
            <select class="select select-bordered select-sm"
                x-model="tabInstrument" @change="frettedDrone = false; renderAbc()"
                x-show="showTablature">
                <option value="fiddle">Fiddle</option>
                <option value="mandolin">Mandolin</option>
                <option value="guitar">Guitar</option>
                <option value="fiveString">Five String</option>
                <option value="banjoOpenG">Banjo — Open G (gDGBD)</option>
                <option value="banjoDoubleC">Banjo — Double C (gCGCD)</option>
                <option value="banjoSawmill">Banjo — Sawmill (gDGCD)</option>
                <option value="banjoOpenD">Banjo — Open D (f#DF#AD)</option>
                <option value="banjoOpenC">Banjo — Open C (gCGCE)</option>
                <option value="banjoGMinor">Banjo — G Minor (gDGBbD)</option>
                <option value="banjoDADE">Banjo — D-A-D-E (aDADE)</option>
                <option value="custom">Custom</option>
                <option value="customBanjo">Custom Banjo</option>
            </select>
            <label class="flex items-center gap-1 cursor-pointer text-sm"
                x-show="showTablature && (tabInstrument.startsWith('banjo') || tabInstrument.startsWith('customBanjo'))">
                <input type="checkbox" class="checkbox checkbox-sm"
                    x-model="frettedDrone" @change="renderAbc()">
                Allow fretted drone string
            </label>
            <label class="flex items-center gap-1 text-sm"
                x-show="showTablature && (tabInstrument === 'custom' || tabInstrument.startsWith('customBanjo'))">
                Strings:
                <input type="number" min="2" max="12"
                    class="input input-bordered input-sm w-16"
                    x-model.number="customStrings"
                    @change="updateCustomStrings()">
            </label>
            <label class="flex items-center gap-1 text-sm"
                x-show="showTablature && (tabInstrument === 'custom' || tabInstrument.startsWith('customBanjo'))">
                Tuning:
                <input type="text" placeholder="e.g. G,DAe"
                    class="input input-bordered input-sm w-32"
                    x-model="customTuning"
                    @input.debounce.500ms="renderAbc()"
                    :placeholder="tabInstrument.startsWith('customBanjo') ? 'e.g. gDGBd (drone first)' : 'e.g. G,DAe'"
            </label>
        </div>

        {{-- ═══ SHEET MUSIC CANVAS ═══ --}}
        <div id="canvas" class="tune-form-canvas mt-4"></div>

        {{-- ═══ MIDI PLAYER ═══ --}}
        <div class="tune-form-midi mt-4">
            <div class="flex items-center gap-4">
                <div id="midi-player" class="flex-1"></div>
                <div class="knob-container">
                    <label>MIDI Vol</label>
                    <div class="circular-knob" x-data="circularKnob({
                        value: 25, min: 0, max: 100, step: 1,
                        onChange(v) { $dispatch('midi-volume-change', { value: v }) }
                    })" @midi-volume-change.window="updateMidiVolume($event.detail.value)">
                        <canvas x-ref="knobCanvas" width="64" height="64"
                            @mousedown="startDrag($event)"
                            @touchstart.prevent="startDrag($event)"></canvas>
                        <span class="knob-value" x-text="value"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ COLLAPSIBLE ADDITIONAL DETAILS ═══ --}}
        <div x-show="detailsOpen" x-collapse class="mt-4">
            <div class="tune-form-grid">
                @if($mode === 'create-tune')
                {{-- Composer --}}
                <div class="tune-form-field">
                    <label for="composer">Composer</label>
                    <input type="text" id="composer" name="composer" value="{{ $tune->composer ?? '' }}"
                        placeholder="Traditional" class="input input-bordered w-full">
                </div>

                {{-- Tune Origin --}}
                <div class="tune-form-field">
                    <label for="tune_origin">Tune Origin</label>
                    <input type="text" id="tune_origin" name="tune_origin" value="{{ $tune->origin ?? '' }}"
                        placeholder="e.g. Ireland" class="input input-bordered w-full">
                </div>

                {{-- Tune Source --}}
                <div class="tune-form-field">
                    <label for="tune_source">Tune Source</label>
                    <input type="text" id="tune_source" name="tune_source" value="{{ $tune->source ?? '' }}"
                        placeholder="Where did you learn it?" class="input input-bordered w-full">
                </div>
                @endif

                {{-- Parts --}}
                <div class="tune-form-field">
                    <label for="parts">Parts</label>
                    <input type="text" id="parts" name="parts" value="{{ $setting->parts ?? '' }}"
                        placeholder="e.g. AABB" class="input input-bordered w-full">
                </div>

                {{-- Area --}}
                <div class="tune-form-field">
                    <label for="area">Area</label>
                    <input type="text" id="area" name="area" value="{{ $setting->area ?? '' }}"
                        placeholder="e.g. Sliabh Luachra" class="input input-bordered w-full">
                </div>

                {{-- Setting Origin --}}
                <div class="tune-form-field">
                    <label for="origin">Origin</label>
                    <input type="text" id="origin" name="origin" value="{{ $setting->origin ?? '' }}"
                        placeholder="e.g. County Clare" class="input input-bordered w-full">
                </div>

                {{-- Source --}}
                <div class="tune-form-field">
                    <label for="source">Source</label>
                    <input type="text" id="source" name="source" value="{{ $setting->source ?? '' }}"
                        placeholder="Where this setting came from" class="input input-bordered w-full">
                </div>

                {{-- Book --}}
                <div class="tune-form-field">
                    <label for="book">Book</label>
                    <input type="text" id="book" name="book" value="{{ $setting->book ?? '' }}"
                        placeholder="e.g. O'Neill's 1001" class="input input-bordered w-full">
                </div>

                {{-- Transcription Credit --}}
                <div class="tune-form-field">
                    <label for="transcription_credit">Transcription Credit</label>
                    <input type="text" id="transcription_credit" name="transcription_credit" value="{{ $setting->transcription_credit ?? '' }}"
                        placeholder="Who transcribed it" class="input input-bordered w-full">
                </div>

                {{-- Discography + Track --}}
                </div>
                <script>
                    window.__albumTracks = @json($albums->mapWithKeys(fn($a) => [$a->name => $a->tracks->map(fn($t) => ['id' => $t->id, 'label' => $t->track_number . ' - ' . $t->name])]));
                </script>
                <div class="tune-form-grid" x-data="{
                    albumSelected: '{{ addslashes($setting->discography ?? '') }}',
                    trackId: '{{ $setting->track_id ?? '' }}',
                    get tracks() { return (window.__albumTracks && window.__albumTracks[this.albumSelected]) || []; }
                }">
                    <div class="tune-form-field">
                        <label for="discography">Discography</label>
                        <select id="discography" name="discography" x-model="albumSelected" @change="trackId = ''" class="select select-bordered w-full">
                            <option value="">Select album...</option>
                            @foreach($albums as $album)
                                <option value="{{ $album->name }}">{{ $album->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="tune-form-field" x-show="albumSelected" x-cloak>
                        <label for="track">Track</label>
                        <select id="track" name="track_id" x-model="trackId" class="select select-bordered w-full">
                            <option value="">Select track...</option>
                            <template x-for="track in tracks" :key="track.id">
                                <option :value="track.id" x-text="track.label"></option>
                            </template>
                        </select>
                    </div>
            </div>

            {{-- Notes --}}
            <div class="tune-form-field-wide mt-3">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                    class="textarea textarea-bordered w-full"
                    placeholder="Any additional notes...">{{ $setting->notes ?? '' }}</textarea>
            </div>

            {{-- History --}}
            <div class="tune-form-field-wide mt-3">
                <label for="history">History</label>
                <textarea id="history" name="history" rows="3"
                    class="textarea textarea-bordered w-full"
                    placeholder="Historical context...">{{ $setting->history ?? '' }}</textarea>
            </div>

            {{-- Lyrics --}}
            <div class="tune-form-field-wide mt-3">
                <label for="lyrics">Lyrics</label>
                <textarea id="lyrics" name="lyrics" rows="3"
                    class="textarea textarea-bordered w-full"
                    placeholder="Song lyrics if applicable...">{{ $setting->lyrics ?? '' }}</textarea>
            </div>
        </div>

        {{-- ═══ SUBMIT ═══ --}}
        <div class="mt-6 flex items-center gap-4">
            <button type="button" @click="detailsOpen = !detailsOpen"
                class="btn btn-sm btn-outline gap-2">
                <i class="fa-solid fa-chevron-right transition-transform" :class="detailsOpen && 'rotate-90'"></i>
                <span x-text="detailsOpen ? 'Hide Details' : 'More Details'"></span>
            </button>
            <button type="submit" class="btn btn-primary">
                @if($mode === 'create-tune')
                    Save Tune
                @elseif($mode === 'add-setting')
                    Add Setting
                @else
                    Update Setting
                @endif
            </button>
        </div>
        </div> {{-- closes x-data detailsOpen --}}
    </form>
</div>
