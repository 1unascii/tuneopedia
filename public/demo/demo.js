// ══════════════════════════════════════════════════════════════════════════════
// Tuneopedia ABC Editor Demo — Self-contained
// ══════════════════════════════════════════════════════════════════════════════

// ── Key signature lookup tables ─────────────────────────────────────────────

var sharpsToPush = [["Ff"],["Cc"],["Gg"],["Dd"],["Aa"],["Ee"],["Bb"]];
var flatsToPush  = [["B","b"],["E","e"],["A","a"],["D","d"],["G","g"],["C","c"],["F","f"]];

var sharpKeysArray = [
    ["C","D dorian","G Mixolydian","A minor"],
    ["G","A dorian","D Mixolydian","E minor"],
    ["D","E dorian","A Mixolydian","B minor"],
    ["A","B dorian","E Mixolydian","F# minor"],
    ["E","F# dorian","B Mixolydian","C# minor"],
    ["B","C# dorian","F# Mixolydian","G# minor"],
    ["F#","G# dorian","C# Mixolydian","D# minor"],
    ["C#","D# dorian","G# Mixolydian","A# minor"]
];

var flatKeysArray = [
    ["C","D dorian","G Mixolydian","A minor"],
    ["F","G dorian","C Mixolydian","D minor"],
    ["Bb","C dorian","F Mixolydian","G minor"],
    ["Eb","F dorian","Bb Mixolydian","C minor"],
    ["Ab","Bb dorian","Eb Mixolydian","F minor"],
    ["Db","Eb dorian","Ab Mixolydian","Bb minor"],
    ["Gb","Ab dorian","Db Mixolydian","Eb minor"],
    ["Cb","Db dorian","Gb Mixolydian","Ab minor"]
];

// Key options per mode
var keyOptions = {
    maj: [
        {id:'f',text:'F'},{id:'c',text:'C'},{id:'g',text:'G'},{id:'d',text:'D'},
        {id:'a',text:'A'},{id:'e',text:'E'},{id:'b',text:'B'},
        {id:'fsharp',text:'F#'},{id:'csharp',text:'C#'},
        {id:'bb',text:'Bb'},{id:'eb',text:'Eb'},{id:'ab',text:'Ab'},{id:'db',text:'Db'}
    ],
    min: [
        {id:'f',text:'F minor'},{id:'c',text:'C minor'},{id:'g',text:'G minor'},{id:'d',text:'D minor'},
        {id:'a',text:'A minor'},{id:'e',text:'E minor'},{id:'b',text:'B minor'},
        {id:'fsharp',text:'F# minor'},{id:'csharp',text:'C# minor'},
        {id:'bb',text:'Bb minor'},{id:'eb',text:'Eb minor'},{id:'ab',text:'Ab minor'}
    ],
    dor: [
        {id:'f',text:'F dorian'},{id:'c',text:'C dorian'},{id:'g',text:'G dorian'},{id:'d',text:'D dorian'},
        {id:'a',text:'A dorian'},{id:'e',text:'E dorian'},{id:'b',text:'B dorian'},
        {id:'fsharp',text:'F# dorian'},{id:'csharp',text:'C# dorian'},
        {id:'bb',text:'Bb dorian'},{id:'eb',text:'Eb dorian'},{id:'ab',text:'Ab dorian'},{id:'db',text:'Db dorian'}
    ],
    mix: [
        {id:'f',text:'F Mixolydian'},{id:'c',text:'C Mixolydian'},{id:'g',text:'G Mixolydian'},{id:'d',text:'D Mixolydian'},
        {id:'a',text:'A Mixolydian'},{id:'e',text:'E Mixolydian'},{id:'b',text:'B Mixolydian'},
        {id:'fsharp',text:'F# Mixolydian'},{id:'csharp',text:'C# Mixolydian'},
        {id:'bb',text:'Bb Mixolydian'},{id:'eb',text:'Eb Mixolydian'},{id:'ab',text:'Ab Mixolydian'},
        {id:'db',text:'Db Mixolydian'},{id:'gb',text:'Gb Mixolydian'}
    ]
};

// ── Tablature presets ───────────────────────────────────────────────────────

var tablaturePresets = {
    'fiddle':       { instrument: 'fiddle' },
    'mandolin':     { instrument: 'mandolin' },
    'guitar':       { instrument: 'guitar' },
    'banjo-open-g': { instrument: 'banjo', tuning: ['D','G','B','d','g'], label: 'Banjo Open G (gDGBD)' },
    'banjo-double-c':{ instrument: 'banjo', tuning: ['C','G','c','d','g'], label: 'Banjo Double C (gCGCD)' },
    'banjo-sawmill': { instrument: 'banjo', tuning: ['D','G','c','d','g'], label: 'Banjo Sawmill (gDGCD)' }
};

// ══════════════════════════════════════════════════════════════════════════════

$(document).ready(function() {

    // ── Init knobs ──────────────────────────────────────────────────────────

    $('.playback-dial').knob({ 'release': function(v) {} });

    // ── Populate key dropdown ───────────────────────────────────────────────

    function populateKeys(mode, keepId) {
        var $key = $('#key');
        var opts = keyOptions[mode] || keyOptions.maj;
        $key.empty();
        opts.forEach(function(o) {
            var $opt = $('<option>').attr('id', o.id).text(o.text);
            if (o.id === keepId) $opt.attr('selected', true);
            $key.append($opt);
        });
    }

    populateKeys('maj', 'g');

    $('#tune_mode_input').on('change', function() {
        var mode = $(this).val();
        var currentId = $('#key option:selected').attr('id') || '';
        populateKeys(mode, currentId);
        renderAbc();
    });

    // ── Tablature params ────────────────────────────────────────────────────

    function getTablatureParams() {
        var val = $('#tablature-instrument').val();
        if (!val) return {};
        var tab = tablaturePresets[val];
        if (!tab) return {};
        var entry = { instrument: tab.instrument };
        if (tab.tuning) entry.tuning = tab.tuning;
        if (tab.label) entry.label = tab.label;
        return { tablature: [entry], visualTranspose: 0 };
    }

    // ── Build ABC string from form ──────────────────────────────────────────

    var synthControl = null;

    function buildAbcString() {
        var midiProgram = parseInt($('#playback-instrument option:selected').data('midi')) || 0;
        return 'X:1\n' +
            'T:' + ($('#tune_title').val() || '') + '\n' +
            'R:' + ($('#tune_type').val() || '') + '\n' +
            'M:' + ($('#metre').val() || '4/4') + '\n' +
            'L:1/8\n' +
            'Q:1/4=' + (parseInt($('#playback-tempo').val()) || 120) + '\n' +
            '%%MIDI program ' + midiProgram + '\n' +
            'K:' + ($('#key').val() || 'C') + '\n' +
            ($('#abc').val() || '');
    }

    // ── Render ABC notation ─────────────────────────────────────────────────

    function renderAbc() {
        var abcString = buildAbcString();
        var params = getTablatureParams();
        params.add_classes = true;
        params.dragging = true;
        params.clickListener = function(abcelem, tuneNumber, classes, analysis, drag, mouseEvent) {
            if (!drag || drag.step === 0) return;
            var newNote = handleNoteDrag(abcString, abcelem, drag.step);
            if (newNote) playNote(newNote);
        };

        var visualObj = ABCJS.renderAbc('canvas', abcString, params);

        if (visualObj && visualObj[0]) {
            // Init MIDI player
            if (!synthControl) {
                var cursor = new CursorControl('canvas');
                synthControl = new ABCJS.synth.SynthController();
                synthControl.load('#midi-player', cursor, {
                    displayLoop: true,
                    displayRestart: true,
                    displayPlay: true,
                    displayProgress: true,
                    displayWarp: true
                });
            }

            synthControl.setTune(visualObj[0], false).catch(function(e) {});
            // Force full re-init so instrument change takes effect on next play
            if (synthControl.midiBuffer) {
                try { synthControl.midiBuffer.stop(); } catch(e) {}
                synthControl.midiBuffer = null;
            }
            synthControl.isLoaded = false;
        }
    }

    // ── Cursor control for MIDI playback (highlight only, no cursor line) ───

    function CursorControl(notationId) {
        var self = this;
        self.notationId = notationId;

        self.onStart = function() {};

        self.onEvent = function(ev) {
            if (ev.measureStart && ev.left === null) return;
            var lastSelection = document.querySelectorAll('svg .highlight');
            for (var k = 0; k < lastSelection.length; k++)
                lastSelection[k].classList.remove('highlight');
            for (var i = 0; i < ev.elements.length; i++) {
                var note = ev.elements[i];
                for (var j = 0; j < note.length; j++) {
                    note[j].classList.add('highlight');
                }
            }
        };

        self.onFinished = function() {
            var els = document.querySelectorAll('svg .highlight');
            for (var i = 0; i < els.length; i++) els[i].classList.remove('highlight');
        };
    }

    // ── Note dragging ───────────────────────────────────────────────────────

    var noteScale = ['C,','D,','E,','F,','G,','A,','B,','C','D','E','F','G','A','B','c','d','e','f','g','a','b',"c'","d'","e'","f'","g'","a'","b'"];

    function abcNoteToIndex(note) {
        var clean = note.replace(/[\^_=]/g, '');
        for (var i = 0; i < noteScale.length; i++) {
            if (noteScale[i] === clean) return i;
        }
        return -1;
    }

    function shiftAbcNote(noteToken, steps) {
        var acc = '';
        var rest = noteToken;
        while (rest.length > 0 && (rest[0] === '^' || rest[0] === '_' || rest[0] === '=')) {
            acc += rest[0];
            rest = rest.substring(1);
        }

        var noteMatch = rest.match(/^([A-Ga-g][,']*)(.*)$/);
        if (!noteMatch) return noteToken;

        var notePart = noteMatch[1];
        var suffix = noteMatch[2];

        var idx = abcNoteToIndex(notePart);
        if (idx === -1) return noteToken;

        var newIdx = idx - steps;
        if (newIdx < 0 || newIdx >= noteScale.length) return noteToken;

        return acc + noteScale[newIdx] + suffix;
    }

    function handleNoteDrag(abcString, abcelem, steps) {
        // Calculate where the body starts in the full ABC string
        var kPos = abcString.indexOf('\nK:');
        if (kPos === -1) return null;
        var bodyStart = abcString.indexOf('\n', kPos + 1);
        if (bodyStart === -1) return null;
        bodyStart += 1;

        var start = abcelem.startChar;
        var end = abcelem.endChar;

        if (start < bodyStart) return null;

        var oldToken = abcString.substring(start, end);
        var newToken;

        if (oldToken.indexOf('[') !== -1) {
            newToken = oldToken.replace(/(\^{0,2}|_{0,2}|=?)([A-Ga-g][,']*[0-9\/]*)/g, function(match) {
                return shiftAbcNote(match, steps);
            });
        } else {
            newToken = shiftAbcNote(oldToken, steps);
        }

        if (newToken === oldToken) return null;

        // Work directly with the textarea body
        var $textarea = $('#abc');
        var body = $textarea.val();

        var bodyOffset = start - bodyStart;
        var bodyEndOffset = end - bodyStart;
        var newBody = body.substring(0, bodyOffset) + newToken + body.substring(bodyEndOffset);

        $textarea.val(newBody);
        renderAbc();

        var noteOnly = newToken.match(/^(\^{0,2}|_{0,2}|=?)([A-Ga-g][,']*)/);
        return noteOnly ? noteOnly[0] : null;
    }

    // ── Re-render on any form change ────────────────────────────────────────

    $('#tune_title, #tune_type, #metre, #key').on('change', renderAbc);
    $('#key').on('click', renderAbc);
    $('#abc').on('change keyup', renderAbc);
    $('#tablature-instrument').on('change', renderAbc);
    $('#playback-instrument').on('change', renderAbc);

    // ── Key-specific playback ───────────────────────────────────────────────

    function keySpecificPlayback(key, keyPress) {
        var sharps = [], flats = [];
        var sharpIdx = sharpKeysArray.findIndex(function(row) { return row.indexOf(key) !== -1; });
        var flatIdx = flatKeysArray.findIndex(function(row) { return row.indexOf(key) !== -1; });

        if (sharpIdx > 0) {
            for (var i = 0; i < sharpIdx; i++) sharps.push(sharpsToPush[i]);
        } else if (flatIdx > 0) {
            for (var i = 0; i < flatIdx; i++) flats.push(flatsToPush[i]);
        }

        if (sharps.join('').indexOf(keyPress) > -1) return '^' + keyPress;
        if (flats.join('').indexOf(keyPress) > -1) return '_' + keyPress;
        return keyPress;
    }

    // ── ABC to MIDI pitch ───────────────────────────────────────────────────

    function abcToMidi(abc) {
        var noteMap = { 'C': 60, 'D': 62, 'E': 64, 'F': 65, 'G': 67, 'A': 69, 'B': 71 };
        var i = 0, acc = 0;
        while (i < abc.length) {
            if (abc[i] === '^') { acc++; i++; }
            else if (abc[i] === '_') { acc--; i++; }
            else if (abc[i] === '=') { i++; }
            else break;
        }
        if (i >= abc.length) return 60;
        var ch = abc[i];
        var isLower = (ch === ch.toLowerCase());
        var base = noteMap[ch.toUpperCase()];
        if (base === undefined) return 60;
        var pitch = isLower ? base + 12 : base;
        pitch += acc;
        i++;
        while (i < abc.length) {
            if (abc[i] === ',') { pitch -= 12; i++; }
            else if (abc[i] === "'") { pitch += 12; i++; }
            else break;
        }
        return pitch;
    }

    // ── Note playback ───────────────────────────────────────────────────────

    var abcSilentChars = /^[tTHLMvuz|:~.\/<>!+\[\](){} 0-9\-\t\n\r]$/;

    function playThud() {
        if (!ABCJS || !ABCJS.synth || !ABCJS.synth.playEvent) return;
        ABCJS.synth.playEvent(
            [{ pitch: 36, duration: 0.08, volume: 30, instrument: 115 }],
            null, 500
        );
    }

    function playNote(abcNoteStr) {
        if (!abcNoteStr || !ABCJS || !ABCJS.synth || !ABCJS.synth.playEvent) return;
        var noteChar = abcNoteStr.replace(/[\^_=]/g, '').charAt(0);
        if (/^[A-Ga-g]$/.test(noteChar)) { /* valid note */ }
        else if (abcSilentChars.test(noteChar)) { return; }
        else { playThud(); return; }

        var pitch = abcToMidi(abcNoteStr);
        var volume = Math.round((parseInt($('#playback-volume').val()) || 50) * 1.27);
        var instrument = parseInt($('#playback-instrument option:selected').data('midi')) || 0;
        var tempo = parseInt($('#playback-tempo').val()) || 120;
        var ms = (60 / tempo) * 4 * 1000;

        ABCJS.synth.playEvent(
            [{ pitch: pitch, duration: 0.25, volume: volume, instrument: instrument }],
            null, ms
        );
    }

    // ── Caret tracking for keystroke context ─────────────────────────────────

    var lastChar = '', charBeforeLast = '', nextChar = '', charAfterNext = '', threeCharsAhead = '';
    var threeCharsAgo = '', fourCharsAgo = '', fiveCharsAgo = '', sixCharsAgo = '';
    var letters = /^[a-zA-Z]+$/;

    function getCaretPosition(ctrl) {
        if (ctrl.selectionStart || ctrl.selectionStart === 0) return ctrl.selectionStart;
        return 0;
    }

    function findSurroundingChars() {
        var el = document.getElementById('abc');
        if (!el) return;
        var p = getCaretPosition(el), v = el.value;
        var ch = function(s, e) { return v.substring(s, e); };
        sixCharsAgo    = ch(p-6, p-5);
        fiveCharsAgo   = ch(p-5, p-4);
        fourCharsAgo   = ch(p-4, p-3);
        threeCharsAgo  = ch(p-3, p-2);
        charBeforeLast = ch(p-2, p-1);
        lastChar       = ch(p-1, p);
        nextChar       = ch(p, p+1);
        charAfterNext  = ch(p+1, p+2);
        threeCharsAhead = ch(p+2, p+3);
    }

    // ── Keystroke playback ──────────────────────────────────────────────────

    $(document).on('keypress', '#abc', function(event) {
        findSurroundingChars();
        var key = $('#key').val();
        var keyPress = String.fromCharCode(event.which);

        if (keyPress === '^' || keyPress === '_' || keyPress === '=') {
            if (nextChar === keyPress && (threeCharsAhead === ',' || threeCharsAhead === "'")) {
                playNote(keyPress + nextChar + charAfterNext + threeCharsAhead);
            } else if (nextChar === keyPress) {
                playNote(keyPress + nextChar + charAfterNext);
            } else if ((lastChar === '^' || lastChar === '_') && nextChar.match(letters) && (charAfterNext === ',' || charAfterNext === "'")) {
                playNote(lastChar + keyPress + nextChar + charAfterNext);
            } else if ((lastChar === '^' || lastChar === '_') && nextChar.match(letters)) {
                playNote(lastChar + keyPress + nextChar);
            } else if (nextChar.match(letters) && (charAfterNext === ',' || charAfterNext === "'")) {
                playNote(keyPress + nextChar + charAfterNext);
            } else if (nextChar.match(letters)) {
                playNote(keyPress + nextChar);
            }
        } else if (lastChar === '^' || lastChar === '_') {
            if (charBeforeLast === lastChar) {
                playNote(charBeforeLast + lastChar + keyPress);
            } else {
                playNote(lastChar + keyPress);
            }
        } else if (keyPress === ',' || keyPress === "'") {
            if (charBeforeLast === '^' || charBeforeLast === '_' || charBeforeLast === '=') {
                if (threeCharsAgo === charBeforeLast) {
                    playNote(threeCharsAgo + charBeforeLast + lastChar + keyPress);
                } else {
                    playNote(charBeforeLast + lastChar + keyPress);
                }
            } else if (lastChar === ',' || lastChar === "'") {
                // already played
            } else {
                var sharps = [];
                var sharpKeyIndex = sharpKeysArray.findIndex(function(row) { return row.indexOf(key) !== -1; });
                if (sharpKeyIndex > 0) {
                    for (var i = 0; i < sharpKeyIndex; i++) sharps.push(sharpsToPush[i]);
                }
                if (sharps.join('').indexOf(lastChar) > -1) {
                    playNote('^' + lastChar + keyPress);
                } else {
                    playNote(lastChar + keyPress);
                }
            }
        } else {
            playNote(keySpecificPlayback(key, keyPress));
        }
    });

    $(document).on('click', '#abc', function() { findSurroundingChars(); });

    // ── Play Selection ──────────────────────────────────────────────────────

    $(document).on('select keyup mouseup', '#abc', function() {
        var text = '';
        if (window.getSelection) text = window.getSelection().toString();
        if (text) {
            if (!$('#play').length) {
                $('#play_selection').html("<input type='button' id='play' value='Play Selection'/>");
            }
        } else {
            $('#play_selection').empty();
        }
    });

    $(document).on('click', '#play', function() {
        var text = '';
        if (window.getSelection) text = window.getSelection().toString();
        if (!text) return;

        var key = $('#key').val();
        var metre = $('#metre').val() || '4/4';
        var tempo = parseInt($('#playback-tempo').val()) || 120;
        var abcStr = 'X:1\nM:' + metre + '\nL:1/8\nQ:1/4=' + tempo + '\nK:' + key + '\n' + text;
        var vis = ABCJS.renderAbc('*', abcStr);
        if (vis && vis[0]) {
            var volume = ((parseInt($('#playback-volume').val()) || 50) / 100) * 3;
            var instrument = parseInt($('#playback-instrument option:selected').data('midi')) || 0;
            var synth = new ABCJS.synth.CreateSynth();
            synth.init({ visualObj: vis[0], options: { soundFontVolumeMultiplier: volume, program: instrument } })
                .then(function() { return synth.prime(); })
                .then(function() { synth.start(); });
        }
        $('#play').fadeOut(250, function() { $(this).remove(); });
    });

    // ── Initial render ──────────────────────────────────────────────────────

    renderAbc();

});
