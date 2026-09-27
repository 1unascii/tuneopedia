/**
 * ABC Notation Renderer
 * =====================
 * Renders ABC notation on show pages (tunes, settings) with optional tablature.
 *
 * For each .abc-notation[data-abc] element, injects controls:
 *   - Tablature checkbox: toggles tablature display
 *   - Instrument dropdown: fiddle, mandolin, guitar, banjo tunings, custom
 *   - Fretted drone checkbox: (banjo only) unlocks 5th string from open-only
 *   - Strings / Tuning inputs: (custom only) define custom instrument tuning
 *
 * Tuning format is ABC notation: uppercase = lower octave, lowercase = upper,
 * comma = octave down, apostrophe = octave up, ^ = sharp, _ = flat.
 * For custom banjo, user enters drone string first (e.g. "gDGBd") and it gets
 * moved to the end internally since abcjs expects ascending pitch order.
 */
import abcjs from 'abcjs';

// Instrument options shown in the dropdown. Values must match pluginTab keys
// in abcjs/src/tablatures/abc_tablatures.js. The _fretted variants and
// customBanjo_fretted are hidden — toggled via the drone checkbox instead.
var tabInstruments = [
    { value: 'fiddle', label: 'Fiddle' },
    { value: 'mandolin', label: 'Mandolin' },
    { value: 'guitar', label: 'Guitar' },
    { value: 'fiveString', label: 'Five String' },
    { value: 'banjoOpenG', label: 'Banjo — Open G (gDGBD)' },
    { value: 'banjoDoubleC', label: 'Banjo — Double C (gCGCD)' },
    { value: 'banjoSawmill', label: 'Banjo — Sawmill (gDGCD)' },
    { value: 'banjoOpenD', label: 'Banjo — Open D (f#DF#AD)' },
    { value: 'banjoOpenC', label: 'Banjo — Open C (gCGCE)' },
    { value: 'banjoGMinor', label: 'Banjo — G Minor (gDGBbD)' },
    { value: 'banjoDADE', label: 'Banjo — D-A-D-E (aDADE)' },
    { value: 'custom', label: 'Custom' },
    { value: 'customBanjo', label: 'Custom Banjo' },
];

function isBanjoInstrument(value) {
    return value.indexOf('banjo') === 0 || value.indexOf('customBanjo') === 0;
}

function isCustomInstrument(value) {
    return value === 'custom' || value.indexOf('customBanjo') === 0;
}

// Parse a tuning string like "DGBdg" or "E,A,DGBe" into an array of ABC note strings.
// Each note: optional accidental (^ _ = ^^  __), letter (A-G/a-g), optional octave (, ')
function parseTuning(str) {
    var notes = [];
    var i = 0;
    while (i < str.length) {
        var note = '';
        if (i < str.length && (str[i] === '^' || str[i] === '_' || str[i] === '=')) {
            note += str[i]; i++;
            if (i < str.length && str[i] === note[0]) { note += str[i]; i++; }
        }
        if (i < str.length && /[A-Ga-g]/.test(str[i])) {
            note += str[i]; i++;
        } else { i++; continue; }
        while (i < str.length && (str[i] === ',' || str[i] === "'")) {
            note += str[i]; i++;
        }
        notes.push(note);
    }
    return notes;
}

function renderAbcNotation() {
    document.querySelectorAll('.abc-notation[data-abc]').forEach(function(el) {
        if (el.dataset.rendered) return;
        el.dataset.rendered = 'true';

        // Build tablature controls
        var controlsDiv = document.createElement('div');
        controlsDiv.className = 'tab-controls flex flex-wrap items-center gap-3 mb-2';

        var label = document.createElement('label');
        label.className = 'flex items-center gap-1 cursor-pointer text-sm';
        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'checkbox checkbox-sm';
        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(' Tablature'));

        var select = document.createElement('select');
        select.className = 'select select-bordered select-sm';
        select.style.display = 'none';
        for (var i = 0; i < tabInstruments.length; i++) {
            var opt = document.createElement('option');
            opt.value = tabInstruments[i].value;
            opt.textContent = tabInstruments[i].label;
            select.appendChild(opt);
        }

        var droneLabel = document.createElement('label');
        droneLabel.className = 'flex items-center gap-1 cursor-pointer text-sm';
        droneLabel.style.display = 'none';
        var droneCheckbox = document.createElement('input');
        droneCheckbox.type = 'checkbox';
        droneCheckbox.className = 'checkbox checkbox-sm';
        droneLabel.appendChild(droneCheckbox);
        droneLabel.appendChild(document.createTextNode(' Allow fretted drone string'));

        var tuningLabel = document.createElement('label');
        tuningLabel.className = 'flex items-center gap-1 text-sm';
        tuningLabel.style.display = 'none';
        tuningLabel.appendChild(document.createTextNode('Tuning: '));
        var tuningInput = document.createElement('input');
        tuningInput.type = 'text';
        tuningInput.placeholder = 'e.g. G,DAe';
        tuningInput.className = 'input input-bordered input-sm w-32';
        tuningLabel.appendChild(tuningInput);

        controlsDiv.appendChild(label);
        controlsDiv.appendChild(select);
        controlsDiv.appendChild(droneLabel);
        controlsDiv.appendChild(tuningLabel);
        el.parentNode.insertBefore(controlsDiv, el);

        // Find the MIDI instrument dropdown for this setting
        var settingId = el.dataset.settingId;
        var playerEl = document.getElementById('midi-player-' + settingId);
        var synthControl = null;
        var instrumentSelect = document.querySelector(
            '.setting-instrument[data-setting-id="' + settingId + '"]'
        );

        // Returns the ABC string with %%MIDI program inserted
        // based on the current instrument dropdown selection
        function getAbcWithMidi() {
            var abc = el.dataset.abc;
            if (instrumentSelect) {
                var midiProgram = instrumentSelect.value;
                abc = abc.replace(
                    /^(K:.*)$/m,
                    '%%MIDI program ' + midiProgram + '\n$1'
                );
            }
            return abc;
        }

        function initMidiPlayer(visObj) {
            if (!playerEl || !abcjs.synth) return;
            if (!synthControl) {
                synthControl = new abcjs.synth.SynthController();
                synthControl.load('#midi-player-' + settingId, null, {
                    displayLoop: true,
                    displayRestart: true,
                    displayPlay: true,
                    displayProgress: true,
                    displayWarp: true
                });
            }
            if (visObj && visObj[0]) {
                synthControl.setTune(visObj[0], false).catch(function() {});
            }
        }

        // Initial render with MIDI program from instrument dropdown
        var visualObj = abcjs.renderAbc(el, getAbcWithMidi(), {
            responsive: 'resize',
            add_classes: true
        });
        initMidiPlayer(visualObj);

        // Re-render when instrument select changes — rebuild the
        // synth controller so it loads the new instrument's soundfont
        if (instrumentSelect) {
            instrumentSelect.addEventListener('change', function() {
                if (synthControl) {
                    try { synthControl.pause(); } catch(e) {}
                }
                synthControl = null;
                playerEl.innerHTML = '';
                rerender();
            });
        }

        function rerender() {
            var options = { responsive: 'resize', add_classes: true };
            if (checkbox.checked) {
                select.style.display = '';
                var instrumentBase = select.value;
                var isBanjo = isBanjoInstrument(instrumentBase);
                var isCustom = isCustomInstrument(instrumentBase);

                droneLabel.style.display = isBanjo ? '' : 'none';
                if (!isBanjo) droneCheckbox.checked = false;

                tuningLabel.style.display = isCustom ? '' : 'none';
                tuningInput.placeholder = isBanjo && isCustom ? 'e.g. gDGBd (drone first)' : 'e.g. G,DAe';

                var instrument = instrumentBase;
                if (isBanjo && droneCheckbox.checked) {
                    instrument = instrument + '_fretted';
                }

                var tabConfig = { instrument: instrument };

                // For custom instruments, parse the user's tuning string and pass
                // it to abcjs. For custom banjo, the user writes the drone first
                // (e.g. "gDGBd") so we rotate it to the end for ascending order.
                // We only pass a custom tuning when it has at least 2 valid notes —
                // partial input (mid-typing/deleting) falls back to the default
                // tuning to avoid crashing abcjs.
                // Parse custom tuning if the user has typed something.
                // Need at least 2 valid notes to override the default tuning.
                // If fewer than 2, just use the default (don't kill tablature).
                if (isCustom && tuningInput.value.trim()) {
                    var parsed = parseTuning(tuningInput.value.trim());
                    if (parsed.length >= 2) {
                        if (instrumentBase.indexOf('customBanjo') === 0) {
                            parsed.push(parsed.shift());
                        }
                        tabConfig.tuning = parsed;
                    }
                }

                options.tablature = [tabConfig];
            } else {
                select.style.display = 'none';
                droneLabel.style.display = 'none';
                tuningLabel.style.display = 'none';
            }
            var abcString = getAbcWithMidi();
            try {
                el.innerHTML = '';
                // Render without tablature first to clear abcjs internal state,
                // then re-render with tablature. Prevents crashes when the number
                // of tablature strings changes between renders.
                if (options.tablature) {
                    abcjs.renderAbc(el, abcString, { responsive: 'resize' });
                    el.innerHTML = '';
                }
                visualObj = abcjs.renderAbc(el, abcString, options);
            } catch (e) {
                console.error('Tablature render error:', e);
                el.innerHTML = '';
                visualObj = abcjs.renderAbc(el, abcString, {
                    responsive: 'resize'
                });
            }
            initMidiPlayer(visualObj);
        }

        checkbox.addEventListener('change', rerender);
        select.addEventListener('change', rerender);
        droneCheckbox.addEventListener('change', rerender);
        var tuningDebounceTimer = null;
        tuningInput.addEventListener('input', function() {
            clearTimeout(tuningDebounceTimer);
            tuningDebounceTimer = setTimeout(rerender, 500);
        });
    });
}

renderAbcNotation();
document.addEventListener('turbo:load', renderAbcNotation);
