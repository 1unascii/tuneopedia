/**
 * tune_form.js
 *
 * Alpine.js component for the tune form (create tune, add setting, edit setting).
 * Handles:
 *   - Real-time ABC notation rendering via abcjs
 *   - Per-keystroke note playback with key-signature awareness
 *   - Play selection (highlight text → play just that excerpt)
 *   - Full MIDI player with real-time volume control
 *   - Circular knob UI components (tempo, volume, MIDI volume)
 */

// ═══════════════════════════════════════════════════════════════════════════════
// KEY SIGNATURE LOOKUP TABLES
// ═══════════════════════════════════════════════════════════════════════════════

// Order in which sharps are added: F C G D A E B
const sharpsToPush = [['F','f'],['C','c'],['G','g'],['D','d'],['A','a'],['E','e'],['B','b']];
// Order in which flats are added: B E A D G C F
const flatsToPush = [['B','b'],['E','e'],['A','a'],['D','d'],['G','g'],['C','c'],['F','f']];

// Each row = keys sharing that many sharps (row index = number of sharps)
const sharpKeysArray = [
    ['C','D dorian','G Mixolydian','A minor'],
    ['G','A dorian','D Mixolydian','E minor'],
    ['D','E dorian','A Mixolydian','B minor'],
    ['A','B dorian','E Mixolydian','F# minor'],
    ['E','F# dorian','B Mixolydian','C# minor'],
    ['B','C# dorian','F# Mixolydian','G# minor'],
    ['F#','G# dorian','C# Mixolydian','D# minor'],
    ['C#','D# dorian','G# Mixolydian','A# minor']
];

// Each row = keys sharing that many flats (row index = number of flats)
const flatKeysArray = [
    ['C','D dorian','G Mixolydian','A minor'],
    ['F','G dorian','C Mixolydian','D minor'],
    ['Bb','C dorian','F Mixolydian','G minor'],
    ['Eb','F dorian','Bb Mixolydian','C minor'],
    ['Ab','Bb dorian','Eb Mixolydian','F minor'],
    ['Db','Eb dorian','Ab Mixolydian','Bb minor'],
    ['Gb','Ab dorian','Db Mixolydian','Eb minor'],
    ['Cb','Db dorian','Gb Mixolydian','Ab minor']
];

// Key options grouped by mode for the key dropdown
const keysByMode = {
    major: ['C','G','D','A','E','B','F#','C#','F','Bb','Eb','Ab','Db','Gb','Cb'],
    minor: ['A minor','E minor','B minor','F# minor','C# minor','G# minor','D# minor','A# minor','D minor','G minor','C minor','F minor','Bb minor','Eb minor','Ab minor'],
    dorian: ['D dorian','A dorian','E dorian','B dorian','F# dorian','C# dorian','G# dorian','D# dorian','G dorian','C dorian','F dorian','Bb dorian','Eb dorian','Ab dorian','Db dorian'],
    mixolydian: ['G Mixolydian','D Mixolydian','A Mixolydian','E Mixolydian','B Mixolydian','F# Mixolydian','C# Mixolydian','G# Mixolydian','C Mixolydian','F Mixolydian','Bb Mixolydian','Eb Mixolydian','Ab Mixolydian','Db Mixolydian','Gb Mixolydian']
};

// ═══════════════════════════════════════════════════════════════════════════════
// KEY-SPECIFIC PLAYBACK
// Given a key and a typed character, return the ABC note with accidentals applied
// ═══════════════════════════════════════════════════════════════════════════════

function keySpecificPlayback(key, keyPress) {
    let sharps = [];
    let flats = [];

    const sharpKeyIndex = sharpKeysArray.findIndex(row => row.indexOf(key) !== -1);
    const flatKeyIndex = flatKeysArray.findIndex(row => row.indexOf(key) !== -1);

    if (sharpKeyIndex > 0) {
        for (let i = 0; i < sharpKeyIndex; i++) sharps.push(...sharpsToPush[i]);
    } else if (flatKeyIndex > 0) {
        for (let i = 0; i < flatKeyIndex; i++) flats.push(...flatsToPush[i]);
    }

    if (sharps.indexOf(keyPress) > -1) return '^' + keyPress;
    if (flats.indexOf(keyPress) > -1) return '_' + keyPress;
    return keyPress;
}

// ═══════════════════════════════════════════════════════════════════════════════
// ABC TO MIDI CONVERSION
// Converts an ABC note string (e.g. '^F', 'd,', '=G') to a MIDI pitch number
// ═══════════════════════════════════════════════════════════════════════════════

function abcToMidi(abc) {
    const noteMap = { 'C': 60, 'D': 62, 'E': 64, 'F': 65, 'G': 67, 'A': 69, 'B': 71 };
    let i = 0;
    let acc = 0;

    // Parse accidental prefixes
    while (i < abc.length) {
        if (abc[i] === '^') { acc++; i++; }
        else if (abc[i] === '_') { acc--; i++; }
        else if (abc[i] === '=') { i++; }
        else break;
    }

    if (i >= abc.length) return 60; // fallback to middle C

    const noteCh = abc[i];
    const isLower = (noteCh === noteCh.toLowerCase());
    const base = noteMap[noteCh.toUpperCase()];
    if (base === undefined) return 60;

    let pitch = isLower ? base + 12 : base;
    pitch += acc;
    i++;

    // Parse octave modifiers
    while (i < abc.length) {
        if (abc[i] === ',') { pitch -= 12; i++; }
        else if (abc[i] === "'") { pitch += 12; i++; }
        else break;
    }

    return pitch;
}

// ═══════════════════════════════════════════════════════════════════════════════
// NOTE PLAYBACK
// Uses ABCJS synth to play a single note with current instrument/volume/tempo
// ═══════════════════════════════════════════════════════════════════════════════

const abcSilentChars = /^[tTHLMvuz|:~.\/<>!+\[\](){} 0-9\-\t\n\r]$/;

function playNote(abcNoteStr, volume, tempo, instrument) {
    if (!abcNoteStr || !window.ABCJS || !window.ABCJS.synth || !window.ABCJS.synth.playEvent) return;

    const noteChar = abcNoteStr.replace(/[\^_=]/g, '').charAt(0);
    if (/^[A-Ga-g]$/.test(noteChar)) {
        // Valid note — fall through to play
    } else if (abcSilentChars.test(noteChar)) {
        return; // Silent syntax character
    } else {
        // Unrecognized — play a thud
        window.ABCJS.synth.playEvent(
            [{ pitch: 36, duration: 0.08, volume: 30, instrument: 115 }],
            null, 500
        );
        return;
    }

    const pitch = abcToMidi(abcNoteStr);
    const midiVolume = Math.round((volume || 50) * 1.27); // 0-100 → 0-127
    const millisecondsPerMeasure = (60 / (tempo || 120)) * 4 * 1000;

    window.ABCJS.synth.playEvent(
        [{ pitch: pitch, duration: 0.25, volume: midiVolume, instrument: instrument || 0 }],
        null,
        millisecondsPerMeasure
    );
}

// ═══════════════════════════════════════════════════════════════════════════════
// CIRCULAR KNOB COMPONENT (Alpine)
// Replaces jQuery Knob with a canvas-based circular dial
// ═══════════════════════════════════════════════════════════════════════════════

window.circularKnob = function(config) {
    return {
        value: config.value || 0,
        min: config.min || 0,
        max: config.max || 100,
        step: config.step || 1,
        dragging: false,
        lastAngle: 0,

        init() {
            this.draw();
            // Global mouse/touch listeners for dragging
            document.addEventListener('mousemove', (e) => this.onDrag(e));
            document.addEventListener('mouseup', () => this.stopDrag());
            document.addEventListener('touchmove', (e) => this.onDrag(e));
            document.addEventListener('touchend', () => this.stopDrag());
        },

        // Get angle (in radians) of pointer relative to knob center
        getAngle(e) {
            const canvas = this.$refs.knobCanvas;
            const rect = canvas.getBoundingClientRect();
            const cx = rect.left + rect.width / 2;
            const cy = rect.top + rect.height / 2;
            const clientX = e.clientX || (e.touches && e.touches[0].clientX) || 0;
            const clientY = e.clientY || (e.touches && e.touches[0].clientY) || 0;
            return Math.atan2(clientY - cy, clientX - cx);
        },

        startDrag(e) {
            this.dragging = true;
            this.lastAngle = this.getAngle(e);
        },

        onDrag(e) {
            if (!this.dragging) return;
            const angle = this.getAngle(e);
            // Calculate angular difference
            let delta = angle - this.lastAngle;
            // Normalize to [-PI, PI] to handle wrapping
            if (delta > Math.PI) delta -= 2 * Math.PI;
            if (delta < -Math.PI) delta += 2 * Math.PI;
            this.lastAngle = angle;

            // Convert angular delta to value change
            // Full knob sweep is 270 degrees (1.5*PI radians)
            const range = this.max - this.min;
            const valueChange = (delta / (1.5 * Math.PI)) * range;
            let newValue = this.value + valueChange;
            newValue = Math.round(newValue / this.step) * this.step;
            newValue = Math.max(this.min, Math.min(this.max, newValue));

            if (newValue !== this.value) {
                this.value = newValue;
                this.draw();
                if (config.onChange) config.onChange(this.value);
            }
        },

        stopDrag() {
            this.dragging = false;
        },

        draw() {
            const canvas = this.$refs.knobCanvas;
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const size = canvas.width;
            const center = size / 2;
            const radius = (size / 2) - 8;

            // Clear
            ctx.clearRect(0, 0, size, size);

            // Background arc (full range)
            const startAngle = 0.75 * Math.PI; // 135 degrees
            const endAngle = 2.25 * Math.PI;   // 405 degrees (full sweep = 270 degrees)
            ctx.beginPath();
            ctx.arc(center, center, radius, startAngle, endAngle);
            ctx.strokeStyle = '#333';
            ctx.lineWidth = 8;
            ctx.lineCap = 'round';
            ctx.stroke();

            // Foreground arc (current value)
            const pct = (this.value - this.min) / (this.max - this.min);
            const valueAngle = startAngle + (pct * (endAngle - startAngle));
            ctx.beginPath();
            ctx.arc(center, center, radius, startAngle, valueAngle);
            ctx.strokeStyle = '#59b4d4';
            ctx.lineWidth = 8;
            ctx.lineCap = 'round';
            ctx.stroke();

        }
    };
};

// ═══════════════════════════════════════════════════════════════════════════════
// MAIN TUNE FORM COMPONENT (Alpine)
// ═══════════════════════════════════════════════════════════════════════════════

window.tuneForm = function(config) {
    return {
        // Form state
        mode: config.mode,
        tuneId: config.tuneId,
        settingId: config.settingId,
        title: config.title || '',
        tuneType: config.tuneType || '',
        timeSignature: config.timeSignature || '4/4',
        defaultNoteLength: config.defaultNoteLength || '1/8',
        keySignature: config.keySignature || 'G',
        abc: config.abc || '',
        instrumentId: config.instrumentId || 1,
        tempo: config.tempo || 120,

        // Playback state
        playbackVolume: 50,
        midiVolume: 25,
        hasSelection: false,
        selectedText: '',

        // Mode/key dropdown state
        mode_select: 'major',
        keyOptions: keysByMode.major,

        // MIDI player state
        synthControl: null,
        gainNode: null,

        // ─── Initialization ─────────────────────────────────────────────
        init() {
            // Determine initial mode from the key signature
            this.mode_select = this.detectMode(this.keySignature);
            this.keyOptions = keysByMode[this.mode_select];

            // Initial render after DOM is ready
            this.$nextTick(() => this.renderAbc());
        },

        // ─── Detect mode from key signature string ──────────────────────
        detectMode(key) {
            if (key.includes('minor')) return 'minor';
            if (key.includes('dorian')) return 'dorian';
            if (key.includes('Mixolydian')) return 'mixolydian';
            return 'major';
        },

        // ─── Update key dropdown when mode changes ──────────────────────
        updateKeyOptions() {
            this.keyOptions = keysByMode[this.mode_select];
            // Default to first key in the new mode
            this.keySignature = this.keyOptions[0];
            this.renderAbc();
        },

        // ─── Get selected MIDI program from instrument dropdown ─────────
        getMidiProgram() {
            const select = document.getElementById('playback-instrument');
            if (!select) return 0;
            const option = select.selectedOptions[0];
            return parseInt(option?.dataset?.midi) || 0;
        },

        // ─── Build full ABC string with headers ─────────────────────────
        buildAbcString() {
            const midiProgram = this.getMidiProgram();
            return 'X:1\n' +
                'T:' + this.title + '\n' +
                (this.tuneType ? 'R:' + this.tuneType + '\n' : '') +
                'M:' + this.timeSignature + '\n' +
                'L:' + this.defaultNoteLength + '\n' +
                'Q:1/4=' + this.tempo + '\n' +
                '%%MIDI program ' + midiProgram + '\n' +
                'K:' + this.keySignature + '\n' +
                this.abc;
        },

        // ─── Render ABC notation to sheet music and update MIDI player ──
        renderAbc() {
            if (!window.ABCJS) return;
            const abcString = this.buildAbcString();

            // Render sheet music to canvas div
            window.ABCJS.renderAbc('canvas', abcString, { add_classes: true });

            // Initialize or update MIDI player
            this.updateMidiPlayer(abcString);
        },

        // ─── Initialize and update MIDI synth player ────────────────────
        updateMidiPlayer(abcString) {
            if (!window.ABCJS || !window.ABCJS.synth) return;
            const playerEl = document.getElementById('midi-player');
            if (!playerEl) return;

            // Create synth controller on first call
            if (!this.synthControl) {
                this.synthControl = new window.ABCJS.synth.SynthController();
                this.synthControl.load('#midi-player', null, {
                    displayLoop: true,
                    displayRestart: true,
                    displayPlay: true,
                    displayProgress: true,
                    displayWarp: true
                });
            }

            // Update the tune in the player
            const vis = window.ABCJS.renderAbc('*', abcString);
            if (vis && vis[0]) {
                this.synthControl.setTune(vis[0], false).catch(() => {});
            }
        },

        // ─── MIDI volume control (real-time gain adjustment) ────────────
        updateMidiVolume(val) {
            if (!this.synthControl || !this.synthControl.midiBuffer) return;
            const ctx = window.ABCJS.synth.activeAudioContext();
            if (!ctx) return;

            if (!this.gainNode) {
                this.gainNode = ctx.createGain();
                this.gainNode.connect(ctx.destination);
            }
            this.gainNode.gain.value = val / 100;

            // Route audio through gain node
            if (this.synthControl.midiBuffer.directSource) {
                this.synthControl.midiBuffer.directSource.forEach(source => {
                    try { source.disconnect(); source.connect(this.gainNode); } catch(e) {}
                });
            }
        },

        // ─── Per-keystroke note playback ────────────────────────────────
        handleKeyPress(event) {
            const keyPress = String.fromCharCode(event.which);
            const textarea = document.getElementById('abc');
            if (!textarea) return;

            const pos = textarea.selectionStart;
            const val = textarea.value;

            // Characters surrounding cursor
            const lastChar = val.substring(pos - 1, pos);
            const charBeforeLast = val.substring(pos - 2, pos - 1);
            const nextChar = val.substring(pos, pos + 1);
            const charAfterNext = val.substring(pos + 1, pos + 2);
            const threeCharsAhead = val.substring(pos + 2, pos + 3);
            const letters = /^[a-zA-Z]$/;
            const key = this.keySignature;
            const midiProgram = this.getMidiProgram();

            if (keyPress === '^' || keyPress === '_' || keyPress === '=') {
                // Accidental prefix — play the note it modifies
                if (nextChar === keyPress && letters.test(charAfterNext)) {
                    playNote(keyPress + nextChar + charAfterNext, this.playbackVolume, this.tempo, midiProgram);
                } else if (nextChar.match(letters)) {
                    if (charAfterNext === ',' || charAfterNext === "'") {
                        playNote(keyPress + nextChar + charAfterNext, this.playbackVolume, this.tempo, midiProgram);
                    } else {
                        playNote(keyPress + nextChar, this.playbackVolume, this.tempo, midiProgram);
                    }
                }

            } else if (lastChar === '^' || lastChar === '_') {
                // Note typed after accidental prefix
                if (charBeforeLast === lastChar) {
                    playNote(charBeforeLast + lastChar + keyPress, this.playbackVolume, this.tempo, midiProgram);
                } else {
                    playNote(lastChar + keyPress, this.playbackVolume, this.tempo, midiProgram);
                }

            } else if (keyPress === ',' || keyPress === "'") {
                // Octave modifier — reconstruct full note string
                if (lastChar === '^' || lastChar === '_' || lastChar === '=') {
                    playNote(charBeforeLast + lastChar + keyPress, this.playbackVolume, this.tempo, midiProgram);
                } else if (lastChar.match(letters)) {
                    playNote(keySpecificPlayback(key, lastChar) + keyPress, this.playbackVolume, this.tempo, midiProgram);
                }

            } else {
                // Plain note letter — apply key signature accidentals
                playNote(keySpecificPlayback(key, keyPress), this.playbackVolume, this.tempo, midiProgram);
            }
        },

        // ─── Handle text selection for play selection feature ────────────
        handleSelection() {
            const textarea = document.getElementById('abc');
            if (!textarea) return;
            const text = textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
            this.hasSelection = text.length > 0;
            this.selectedText = text;
        },

        // ─── Play the selected ABC text ─────────────────────────────────
        playSelection() {
            if (!this.selectedText || !window.ABCJS) return;

            const selectionAbc = 'X:1\nM:' + this.timeSignature +
                '\nL:' + this.defaultNoteLength +
                '\nQ:1/4=' + this.tempo +
                '\nK:' + this.keySignature + '\n' + this.selectedText;

            const visualObj = window.ABCJS.renderAbc('*', selectionAbc);
            if (visualObj && visualObj[0]) {
                const volume = (this.playbackVolume / 100) * 3;
                const instrument = this.getMidiProgram();
                const synth = new window.ABCJS.synth.CreateSynth();
                synth.init({
                    visualObj: visualObj[0],
                    options: { soundFontVolumeMultiplier: volume, program: instrument }
                }).then(() => synth.prime()).then(() => synth.start());
            }

            this.hasSelection = false;
            this.selectedText = '';
        },

        // ─── Form submission ────────────────────────────────────────────
        submitForm() {
            this.$el.closest('form').submit();
        }
    };
};
