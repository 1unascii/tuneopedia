// abc_playback.js
// Plays notes as they are typed into any textarea with id="abc" and class="edit-setting-abc".
// Depends on jquery-turtle.js for $(element).play().
// Uses delegated events so it works with dynamically loaded textareas.

$(document).ready(function () {

    var letters = /^[a-zA-Z]+$/;
    var sharps = [], flats = [];

    var nineCharsAgo = '', eightCharsAgo = '', sevenCharsAgo = '', sixCharsAgo = '';
    var fiveCharsAgo = '', fourCharsAgo = '', threeCharsAgo = '';
    var charBeforeLast = '', lastChar = '', nextChar = '', charAfterNext = '';
    var threeCharsAhead = '';

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

    function getCaretPosition(selector) {
        var caretPos = 0;
        if (document.selection) {
            selector.focus();
            var sel = document.selection.createRange();
            sel.moveStart('character', -selector.value.length);
            caretPos = sel.text.length;
        } else if (selector.selectionStart || selector.selectionStart == '0') {
            caretPos = selector.selectionStart;
        }
        return caretPos;
    }

    function returnChar(selector, caretStart, caretEnd) {
        return selector.substring(caretStart, caretEnd);
    }

    function findSurroundingChars() {
        var selector = document.getElementById('abc');
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

    //takes a list of notes that should be modified depending on the key
    function accidentalNotes(accidentals, modifierString, keyPress) {
        for (var i = 0; i < accidentals.length; i++) {
            if (accidentals[i] === keyPress) return modifierString + keyPress;
        }
        return keyPress;
    }

    // ── Mode change: reload key options from forms/mode_options/ ─────────────
    $(document).on('change', '#tune_mode_input.edit-mode-select', function () {
        var id = $(this).find('option:selected').attr('id');
        var currentKeyId = $('#key').find('option:selected').attr('id') || '';
        switch (id) {
            case 'maj':
                $('#key').load('forms/mode_options/major.php?id=' + currentKeyId, function () { $('#tune_mode_input').focus(); });
            break;
            case 'min':
                $('#key').load('forms/mode_options/minor.php?id=' + currentKeyId, function () { $('#tune_mode_input').focus(); });
            break;
            case 'dor':
                $('#key').load('forms/mode_options/dorian.php?id=' + currentKeyId, function () { $('#tune_mode_input').focus(); });
            break;
            case 'mix':
                $('#key').load('forms/mode_options/mixolydian.php?id=' + currentKeyId, function () { $('#tune_mode_input').focus(); });
            break;
        }
    });

    // ── Main keypress handler ─────────────────────────────────────────────────
    $(document).on('keypress', '#abc.edit-setting-abc', function (event) {
        sharps = [];
        flats = [];
        findSurroundingChars();
        var key = $('#key').val();
        var c = event.which;
        var keyPress = String.fromCharCode(c);

        if (keyPress == '^' || keyPress == '_' || keyPress == '=') {
            //Double Accidental with octave modifier
            if (nextChar == keyPress && (threeCharsAhead == ',' || threeCharsAhead == '\'')) {
                $(this).play(keyPress + nextChar + charAfterNext + threeCharsAhead);
            //Double Accidental without octave modifier
            } else if (nextChar == keyPress) {
                $(this).play(keyPress + nextChar + charAfterNext);
            //An accidental sandwiched between an accidental and a note with octave modifier
            } else if ((lastChar == '^' || lastChar == '_') && nextChar.match(letters) && (charAfterNext == ',' || charAfterNext == '\'')) {
                $(this).play(lastChar + keyPress + nextChar + charAfterNext);
            //Accidental sandwiched between accidental and a note
            } else if ((lastChar == '^' || lastChar == '_') && nextChar.match(letters)) {
                $(this).play(lastChar + keyPress + nextChar);
            //Accidental added before a letter with octave modifier
            } else if (nextChar.match(letters) && (charAfterNext == ',' || charAfterNext == '\'')) {
                $(this).play(keyPress + nextChar + charAfterNext);
            //Accidental added before a letter without octave modifier
            } else if (nextChar.match(letters)) {
                $(this).play(keyPress + nextChar);
            }

        } else if (lastChar == '^' || lastChar == '_') {
            if (charBeforeLast == lastChar) {
                $(this).play(charBeforeLast + lastChar + keyPress);//double accidental
            } else {
                $(this).play(lastChar + keyPress);//accidental note
            }

        } else if (keyPress == ',' || keyPress == '\'') {
            sharps = [];
            sharps.push('F', 'f');
            flats = [];
            flats.push('B', 'b');
            if (charBeforeLast == '^' || charBeforeLast == '_' || charBeforeLast == '=') {//if the user modified the note

                if (threeCharsAgo == charBeforeLast) {//just how modified is this note anyway?
                    if (nextChar == ',' || nextChar == '\'') {
                        $(this).play(threeCharsAgo + charBeforeLast + lastChar + keyPress + nextChar);
                    } else {
                        $(this).play(threeCharsAgo + charBeforeLast + lastChar + keyPress);
                    }
                } else {//ok it's only a single accidental
                    $(this).play(charBeforeLast + lastChar + keyPress);
                }

            } else if (lastChar == ',' || lastChar == '\'') {

                if (sixCharsAgo == fiveCharsAgo) {
                    if (sixCharsAgo == '^' || sixCharsAgo == '_' || sixCharsAgo == '=') {
                        $(this).play(sixCharsAgo + fiveCharsAgo + fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);
                    }
                }
                if (fiveCharsAgo == fourCharsAgo) {
                    if (fiveCharsAgo == '^' || fiveCharsAgo == '_' || fiveCharsAgo == '=') {
                        $(this).play(fiveCharsAgo + fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);
                    }
                } else if (fourCharsAgo == threeCharsAgo) {
                    if (fourCharsAgo == '^' || fourCharsAgo == '_' || fourCharsAgo == '=') {
                        $(this).play(fourCharsAgo + threeCharsAgo + charBeforeLast + lastChar + keyPress);
                    }
                }

            } else {//first octave or special character after a letter note

                var innerArrayIndex = sharpKeysArray.findIndex(function (innerArr) {
                    return innerArr.indexOf(key) !== -1;
                });

                if (innerArrayIndex > 0) {
                    for (var j = 0; j < innerArrayIndex; j++) {
                        sharps.push(sharpsToPush[j]);
                    }
                }

                $(this).play(accidentalNotes(sharps, '^', lastChar + keyPress));
            }

        } else {//if keyPress was not a modifier AND last key press also was not a modifier

            var innerSharpKeysArrayIndex = sharpKeysArray.findIndex(function (innerArr) {
                return innerArr.indexOf(key) !== -1;
            });

            var innerFlatKeysArrayIndex = flatKeysArray.findIndex(function (innerArr) {
                return innerArr.indexOf(key) !== -1;
            });

            if (innerSharpKeysArrayIndex > 0) {
                for (var i = 0; i < innerSharpKeysArrayIndex; i++) {
                    sharps.push(sharpsToPush[i]);
                }
            } else if (innerFlatKeysArrayIndex > 0) {
                for (var i = 0; i < innerFlatKeysArrayIndex; i++) {
                    flats.push(flatsToPush[i]);
                }
            }

            if (sharps.join('').indexOf(keyPress) > -1) {
                $(this).play('^' + keyPress);
            } else if (flats.join('').indexOf(keyPress) > -1) {
                $(this).play('_' + keyPress);
            } else {
                $(this).play(keyPress);
            }
        }
    });

    //insures that the global character values are updated on every click in the abc text area
    $(document).on('click', '#abc.edit-setting-abc', function () {
        findSurroundingChars();
    });

});
