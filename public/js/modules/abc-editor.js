
// ── Key lookup tables (global so keySpecificPlayback is testable without a DOM) ──
// Order in which sharps are added as the key gains more accidentals: F C G D A E B
var sharpsToPushGlobal = [["Ff"],["Cc"],["Gg"],["Dd"],["Aa"],["Ee"],["Bb"]];
// Order in which flats are added: B E A D G C F
var flatsToPushGlobal  = [["B","b"],["E","e"],["A","a"],["D","d"],["G","g"],["C","c"],["F","f"]];

// Each row is a group of key names that share the same number of sharps.
// Row index = number of sharps in the key signature.
var sharpKeysArrayGlobal = [
    ["C","D dorian","G Mixolydian","A minor"],
    ["G","A dorian","D Mixolydian","E minor"],
    ["D","E dorian","A Mixolydian","B minor"],
    ["A","B dorian","E Mixolydian","F# minor"],
    ["E","F# dorian","B Mixolydian","C# minor"],
    ["B","C# dorian","F# Mixolydian","G# minor"],
    ["F#","G# dorian","C# Mixolydian","D# minor"],
    ["C#","D# dorian","G# Mixolydian","A# minor"]
];
// Each row is a group of key names that share the same number of flats.
// Row index = number of flats in the key signature.
var flatKeysArrayGlobal = [
    ["C","D dorian","G Mixolydian","A minor"],
    ["F","G dorian","C Mixolydian","D minor"],
    ["Bb","C dorian","F Mixolydian","G minor"],
    ["Eb","F dorian","Bb Mixolydian","C minor"],
    ["Ab","Bb dorian","Eb Mixolydian","F minor"],
    ["Db","Eb dorian","Ab Mixolydian","Bb minor"],
    ["Gb","Ab dorian","Db Mixolydian","Eb minor"],
    ["Cb","Db dorian","Gb Mixolydian","Ab minor"]
];

// Given a key name (e.g. "G", "D dorian") and a single typed note character,
// returns the ABC note string to play — prepending ^ or _ if the note is
// accidental in that key, or returning the character unchanged if it is natural.
//
// How it works:
//   1. Looks up the key in sharpKeysArrayGlobal / flatKeysArrayGlobal to find its
//      row index.  Index 0 = C major (no accidentals).  Index N = N accidentals.
//   2. Pushes the first N entries from sharpsToPushGlobal (or flatsToPushGlobal)
//      into a local list.  e.g. G major (index 1) → sharps = [["Ff"]]
//   3. Checks whether keyPress appears in the joined accidental string and
//      prepends ^ or _ accordingly.
function keySpecificPlayback(key, keyPress) {
    var sharps = [];
    var flats  = [];

    // Find this key's row in the sharp-keys table (returns -1 if not found)
    var sharpKeyIndex = sharpKeysArrayGlobal.findIndex(function (row) {
        return row.indexOf(key) !== -1;
    });

    // Find this key's row in the flat-keys table
    var flatKeyIndex = flatKeysArrayGlobal.findIndex(function (row) {
        return row.indexOf(key) !== -1;
    });

    if (sharpKeyIndex > 0) {
        // Accumulate one accidental group per degree of sharpness
        for (var sharpDegree = 0; sharpDegree < sharpKeyIndex; sharpDegree++) {
            sharps.push(sharpsToPushGlobal[sharpDegree]);
        }
    } else if (flatKeyIndex > 0) {
        // Accumulate one accidental group per degree of flatness
        for (var flatDegree = 0; flatDegree < flatKeyIndex; flatDegree++) {
            flats.push(flatsToPushGlobal[flatDegree]);
        }
    }

    if (sharps.join('').indexOf(keyPress) > -1) {
        return '^' + keyPress;
    } else if (flats.join('').indexOf(keyPress) > -1) {
        return '_' + keyPress;
    } else {
        return keyPress;
    }
}

$(document).ready(function(){
    // Guard: if the ABC editor form isn't present (e.g. this file is loaded by
    // a test page), skip the static add-tune form initialisation.
    // The delegated handlers below (keypress, click on #abc) will still be
    // registered so they work for dynamically-loaded edit forms.
    var hasStaticForm = !!document.getElementById('abc');

    // ── Shared state used by both static and delegated handlers ─────────────
    var selection = '';
    var sharps = [];
    var flats  = [];
    var nineCharsAgo    = '';
    var eightCharsAgo   = '';
    var sevenCharsAgo   = '';
    var sixCharsAgo     = '';
    var fiveCharsAgo    = '';
    var fourCharsAgo    = '';
    var threeCharsAgo   = '';
    var charBeforeLast  = '';
    var lastChar        = '';
    var nextChar        = '';
    var charAfterNext   = '';
    var threeCharsAhead = '';
    var letters = /^[a-zA-Z]+$/;
    var key = $('#key').val();

    // Builds the ABC header string that ABCJS needs to render sheet music.
    function build_abc_hdr(headers){
        var hdr = "";
        for(i = 0; i < headers.length; i++){
            if(headers[i].length > 1){
                hdr += headers[i][0] + headers[i][1] + "\n";
            }
        }
        return hdr;
    }

    // Returns the caret (cursor) position within a textarea element.
    function getCaretPosition(ctrl) {
        var CaretPos = 0;
        if (document.selection) { // IE
            ctrl.focus();
            var Sel = document.selection.createRange();
            Sel.moveStart('character', -ctrl.value.length);
            CaretPos = Sel.text.length;
        } else if (ctrl.selectionStart || ctrl.selectionStart == '0') {
            CaretPos = ctrl.selectionStart;
        }
        return (CaretPos);
    }

    // Returns the substring of selector between caretStart and caretEnd.
    function returnChar(selector, caretStart, caretEnd) {
        return selector.substring(caretStart, caretEnd);
    }

    // Reads the current caret position in #abc and populates the surrounding-character
    // variables used by the keypress handler to assemble the correct note string to play.
    function findSurroundingChars() {
        var selector = document.getElementById("abc");
        if (!selector) return;
        var caretPos = getCaretPosition(selector);
        nineCharsAgo    = returnChar(selector.value, caretPos - 9, caretPos - 8);
        eightCharsAgo   = returnChar(selector.value, caretPos - 8, caretPos - 7);
        sevenCharsAgo   = returnChar(selector.value, caretPos - 7, caretPos - 6);
        sixCharsAgo     = returnChar(selector.value, caretPos - 6, caretPos - 5);
        fiveCharsAgo    = returnChar(selector.value, caretPos - 5, caretPos - 4);
        fourCharsAgo    = returnChar(selector.value, caretPos - 4, caretPos - 3);
        threeCharsAgo   = returnChar(selector.value, caretPos - 3, caretPos - 2);
        charBeforeLast  = returnChar(selector.value, caretPos - 2, caretPos - 1);
        lastChar        = returnChar(selector.value, caretPos - 1, caretPos);
        nextChar        = returnChar(selector.value, caretPos,     caretPos + 1);
        charAfterNext   = returnChar(selector.value, caretPos + 1, caretPos + 2);
        threeCharsAhead = returnChar(selector.value, caretPos + 2, caretPos + 3);
    }

    // ── Play Selection helpers (shared by both static and dynamic forms) ────

    function applyKeyToSelection(abcString, key) {
        var chars  = abcString.split('');
        var result = '';
        for (var i = 0; i < chars.length; i++) {
            var ch = chars[i];
            if (ch.match(/[a-zA-Z]/) && chars[i - 1] !== '^' && chars[i - 1] !== '_' && chars[i - 1] !== '=') {
                result += keySpecificPlayback(key, ch);
            } else {
                result += ch;
            }
        }
        return result;
    }

    function getSelectionText() {
        var text = "";
        if (window.getSelection) {
            text = window.getSelection().toString();
        } else if (document.selection && document.selection.type != "Control") {
            text = document.selection.createRange().text;
        }
        return text !== "" ? text : false;
    }

    // ── Static add-tune form initialisation ──────────────────────────────────
    // Only runs when the add-tune form is present on page load.
    if (hasStaticForm) {
    var editor1 = document.getElementById("abc");
    editor1.spellcheck = false;

    // ── Mode and key change handlers ──────────────────────────────────────────

    // When the mode select changes on the static add-tune form, reload the key
    // options and re-render the notation.
    $('#tune_mode_input').change(function(){
        var id = $(this).find("option:selected").attr("id");
        var staticBase = (typeof APP_BASE !== 'undefined') ? APP_BASE + '/' : '';
        var modeMap = { 'maj': 'major', 'min': 'minor', 'dor': 'dorian', 'mix': 'mixolydian' };
        var mode = modeMap[id];
        if (mode) {
            $('#key').load(staticBase + 'fragment/mode-options/' + mode + '?id=' + $('#key').find("option:selected").attr("id"), function(){$('#tune_mode_input').focus();});
        }
        if($('#play').length){
            $('#play').remove();
        }
        start_new_abc();
    });

    // ── Sheet music rendering ─────────────────────────────────────────────────

    // Assembles the ABC header from the form fields and re-renders the sheet music.
    function start_new_abc(){
        var abc_code = "";
        var hdr_array = [   ["X:", 1], ["T:", $('#tune_title').val()], ["R:", $('#tune_type').val()],
                            ["M:", $('#metre').val()],["L:", "1/8"], ["K:", $('#key').val()]];
        var hdr = build_abc_hdr(hdr_array);
        abc_code = $('#abc').val();
        abc_editor = ABCJS.renderAbc("canvas", hdr + abc_code);
    }

    // Re-render whenever any header field or the ABC body changes.
    $('#tune_title').on('change', function(){ start_new_abc(); });
    $('#tune_type').on("change", function(){
        if($(this).val() != "Add another"){ start_new_abc(); }
    });
    $('#metre').change(function(){ start_new_abc(); });
    $('#tune_mode_input').on('change keyup paste mouseup', function(){ start_new_abc(); });
    $('#key').on('change mouseup', function(){ start_new_abc(); });
    $('#key').on('click', function(){
        if($('#play').length){
            $('#play').remove();
            selection = '';
        }
        start_new_abc();
    });
    $('#abc').on('change keyup', function(){ start_new_abc(); });

    // ── Save ──────────────────────────────────────────────────────────────────

    } // end if (hasStaticForm)

    // ── Save (delegated so it works for dynamically loaded forms) ─────────────

    $(document).on('click', '#save', function(){
        var tune_body = $('#abc').val().replace(/\n/g, '<br />');
        $.post("api/tunes", {
            "tune_title":    $("#tune_title").val(),
            "tune_type":     $("#tune_type").val(),
            "composer":      $("#composer").val(),
            "metre":         $('#metre').val(),
            "default_note_length": '1/8',
            "tune_key":      $('#key').val(),
            "tune_body":     tune_body
        }, function(data){
            $('<div class="alert-box">' + data + '</div>')
                .appendTo('#pop_up')
                .delay(1500)
                .fadeOut(300, function () {
                    $(this).remove();
                    if (data === 'Thank you. Your tune was submitted') {
                        location.reload();
                    }
                });
        });
    });

    // ── Play Selection (works on both add-tune and edit forms) ───────────────

    $(document).on('select keyup mouseup', '#abc', function () {
        key = $('#key').val();
        selection = getSelectionText();
        if (selection) {
            if (!$('#play').length) {
                $('#play_selection').html("<input type='button' id='play' value='Play Selection'/>");
            }
        } else {
            $('#play_selection').empty();
        }
    });

    $(document).on('click', '#play', function () {
        if (selection) {
            key = $('#key').val();
            var metre = $('#metre').val() || '4/4';
            var tempo = parseInt($('#playback-tempo').val()) || 120;
            var selectionAbc = 'X:1\nM:' + metre + '\nL:1/8\nQ:1/4=' + tempo + '\nK:' + key + '\n' + selection;
            var visualObj = ABCJS.renderAbc('*', selectionAbc);
            if (visualObj && visualObj[0]) {
                var volume = ((parseInt($('#playback-volume').val()) || 50) / 100) * 3;
                var instrument = parseInt($('#playback-instrument').find(':selected').data('midi')) || 0;
                var synth = new ABCJS.synth.CreateSynth();
                synth.init({ visualObj: visualObj[0], options: { soundFontVolumeMultiplier: volume, program: instrument } }).then(function () {
                    return synth.prime();
                }).then(function () {
                    synth.start();
                });
            }
            $('#play').fadeOut(250, function () {
                $(this).remove();
            });
        }
    });

    // ── Per-keystroke note playback ───────────────────────────────────────────
    // The old keyPress() function that previously handled the add-tune textarea
    // has been replaced by the delegated handler below, which uses the same logic
    // as the edit_setting.php inline editor.

    // Takes a list of accidental note letters and returns keyPress prefixed with
    // modifierString if it appears in that list, otherwise returns keyPress plain.
    // The octave parameter is unused at current call sites but kept for reference.
    function accidentalNotes(accidentals, modifierString, keyPress, octave){
        var accidental = false;
        for(var i = 0; i < accidentals.length; i++){
            if(accidentals[i] == keyPress){ accidental = true; }
        }
        if(accidental){
            return octave ? (modifierString + lastChar + keyPress) : (modifierString + keyPress);
        } else {
            return octave ? (lastChar + keyPress) : keyPress;
        }
    }

    // ── Playback controls ──────────────────────────────────────────────────────

    // Converts an ABC note string (e.g. '^F', 'd,', '=G', '^^c') to a MIDI pitch number.
    // ABC octave convention: C=48, D=50 ... B=59, c=60, d=62 ... b=71
    // Comma lowers by 12, apostrophe raises by 12.
    function abcToMidi(abc) {
        var noteMap = { 'C': 48, 'D': 50, 'E': 52, 'F': 53, 'G': 55, 'A': 57, 'B': 59 };
        var i = 0;
        var acc = 0;
        while (i < abc.length) {
            if (abc[i] === '^') { acc++; i++; }
            else if (abc[i] === '_') { acc--; i++; }
            else if (abc[i] === '=') { i++; }
            else { break; }
        }
        if (i >= abc.length) return 60; // fallback to middle C
        var noteCh = abc[i];
        var isLower = (noteCh === noteCh.toLowerCase());
        var base = noteMap[noteCh.toUpperCase()];
        if (base === undefined) return 60;
        var pitch = isLower ? base + 12 : base;
        pitch += acc;
        i++;
        while (i < abc.length) {
            if (abc[i] === ',') { pitch -= 12; i++; }
            else if (abc[i] === "'") { pitch += 12; i++; }
            else { break; }
        }
        return pitch;
    }

    // Plays a single ABC note string using abcjs synth.
    function playThud() {
        if (!ABCJS || !ABCJS.synth || !ABCJS.synth.playEvent) return;
        ABCJS.synth.playEvent(
            [{ pitch: 36, duration: 0.08, volume: 30, instrument: 115 }],
            null, 500
        );
    }

    // Characters that are valid ABC syntax but not playable notes
    var abcSilentChars = /^[tTHLMvuz|:~.\/<>!+\[\](){} 0-9\-\t\n\r]$/;

    function playNote(abcNoteStr) {
        if (!abcNoteStr || !ABCJS || !ABCJS.synth || !ABCJS.synth.playEvent) return;
        var noteChar = abcNoteStr.replace(/[\^_=]/g, '').charAt(0);
        if (/^[A-Ga-g]$/.test(noteChar)) { /* valid note, fall through */ }
        else if (abcSilentChars.test(noteChar)) { return; }
        else { playThud(); return; }
        var pitch = abcToMidi(abcNoteStr);
        var volume = 80;
        var $vol = $('#playback-volume');
        if ($vol.length) volume = Math.round((parseInt($vol.val()) || 50) * 1.27); // 0-100 → 0-127
        var tempo = 120;
        var $tempo = $('#playback-tempo');
        if ($tempo.length) tempo = parseInt($tempo.val()) || 120;
        var instrument = 0;
        var $inst = $('#playback-instrument');
        if ($inst.length) instrument = parseInt($inst.find(':selected').data('midi')) || 0;
        var millisecondsPerMeasure = (60 / tempo) * 4 * 1000;
        ABCJS.synth.playEvent(
            [{ pitch: pitch, duration: 0.25, volume: volume, instrument: instrument }],
            null,
            millisecondsPerMeasure
        );
    }

    window.playNote = playNote;

    // jQuery Knob displays its own value — no manual update handlers needed.

    // ── Delegated handlers (cover both static add-tune and dynamic edit forms) ─
    // Selector #abc matches both the static textarea in abc_editor.php and any
    // textarea with id="abc" injected by edit_setting.php.  Using document-level
    // delegation means it works for dynamically-loaded forms too.

    // Reload the key <select> when mode changes in a dynamically-loaded edit form.
    // The .edit-mode-select class distinguishes injected forms from the static one
    // (which is handled by the direct $('#tune_mode_input').change() above).
    var editorBase = (typeof APP_BASE !== 'undefined') ? APP_BASE + '/' : '';

    $(document).on('change', '#tune_mode_input.edit-mode-select', function () {
        var id = $(this).find('option:selected').attr('id');
        var currentKeyId = $('#key').find('option:selected').attr('id') || '';
        var modeMap = { 'maj': 'major', 'min': 'minor', 'dor': 'dorian', 'mix': 'mixolydian' };
        var mode = modeMap[id];
        if (mode) {
            $('#key').load(editorBase + 'fragment/mode-options/' + mode + '?id=' + currentKeyId, function () {
                $('#tune_mode_input').focus();
                // Trigger change on #key so the edit form re-renders notation
                $('#key').trigger('change');
            });
        }
    });

    // Play a note on every keystroke in any #abc textarea (static or dynamic).
    $(document).on('keypress', '#abc', function (event) {
        sharps = [];
        flats  = [];
        findSurroundingChars();
        var key      = $('#key').val();
        var charCode = event.which;
        var keyPress = String.fromCharCode(charCode);

        if (keyPress == '^' || keyPress == '_' || keyPress == '=') {
            // Double accidental with octave modifier (e.g. ^^G,)
            if (nextChar == keyPress && (threeCharsAhead == ',' || threeCharsAhead == '\'')) {
                playNote(keyPress + nextChar + charAfterNext + threeCharsAhead);
            // Double accidental without octave modifier (e.g. ^^G)
            } else if (nextChar == keyPress) {
                playNote(keyPress + nextChar + charAfterNext);
            // Accidental typed between an existing accidental and a note that has an octave modifier
            } else if ((lastChar == '^' || lastChar == '_') && nextChar.match(letters) && (charAfterNext == ',' || charAfterNext == '\'')) {
                playNote(lastChar + keyPress + nextChar + charAfterNext);
            // Accidental typed between an existing accidental and a plain note
            } else if ((lastChar == '^' || lastChar == '_') && nextChar.match(letters)) {
                playNote(lastChar + keyPress + nextChar);
            // Accidental typed before a note that already has an octave modifier
            } else if (nextChar.match(letters) && (charAfterNext == ',' || charAfterNext == '\'')) {
                playNote(keyPress + nextChar + charAfterNext);
            // Accidental typed before a plain note
            } else if (nextChar.match(letters)) {
                playNote(keyPress + nextChar);
            }

        } else if (lastChar == '^' || lastChar == '_') {
            // Note letter typed immediately after an accidental prefix
            if (charBeforeLast == lastChar) {
                playNote(charBeforeLast + lastChar + keyPress); // double accidental
            } else {
                playNote(lastChar + keyPress); // single accidental note
            }

        } else if (keyPress == ',' || keyPress == '\'') {
            // Octave modifier typed: rebuild the full note string including any preceding
            // accidental prefix so the correct pitch is sounded.
            sharps = [];
            sharps.push('F', 'f');
            flats = [];
            flats.push('B', 'b');

            if (charBeforeLast == '^' || charBeforeLast == '_' || charBeforeLast == '=') {
                // The note before the octave modifier had an accidental
                if (threeCharsAgo == charBeforeLast) {
                    // Double accidental (e.g. ^^G,)
                    if (nextChar == ',' || nextChar == '\'') {
                        playNote(threeCharsAgo + charBeforeLast + lastChar + keyPress + nextChar);
                    } else {
                        playNote(threeCharsAgo + charBeforeLast + lastChar + keyPress);
                    }
                } else {
                    // Single accidental (e.g. ^G,)
                    playNote(charBeforeLast + lastChar + keyPress);
                }

            } else if (lastChar == ',' || lastChar == '\'') {
                // A second octave modifier was typed (e.g. G,, or G'').
                // Walk far enough back to pick up any leading accidental prefix.
                if (sixCharsAgo == fiveCharsAgo) {
                    if (sixCharsAgo == '^' || sixCharsAgo == '_' || sixCharsAgo == '=') {
                        playNote(sixCharsAgo + fiveCharsAgo + fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);
                    }
                }
                if (fiveCharsAgo == fourCharsAgo) {
                    if (fiveCharsAgo == '^' || fiveCharsAgo == '_' || fiveCharsAgo == '=') {
                        playNote(fiveCharsAgo + fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);
                    }
                } else if (fourCharsAgo == threeCharsAgo) {
                    if (fourCharsAgo == '^' || fourCharsAgo == '_' || fourCharsAgo == '=') {
                        playNote(fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);
                    }
                }

            } else {
                // Plain note followed by its first octave modifier — apply key accidentals
                var sharpKeyIndex = sharpKeysArrayGlobal.findIndex(function (innerArr) {
                    return innerArr.indexOf(key) !== -1;
                });
                if (sharpKeyIndex > 0) {
                    for (var sharpDegree = 0; sharpDegree < sharpKeyIndex; sharpDegree++) {
                        sharps.push(sharpsToPushGlobal[sharpDegree]);
                    }
                }
                playNote(accidentalNotes(sharps, '^', lastChar + keyPress));
            }

        } else {
            // Plain note letter — delegate to keySpecificPlayback which applies
            // the key signature accidentals.
            playNote(keySpecificPlayback(key, keyPress));
        }
    });

    // Keep the surrounding-character context fresh whenever the caret moves by click.
    $(document).on('click', '#abc', function () {
        findSurroundingChars();
    });

});
