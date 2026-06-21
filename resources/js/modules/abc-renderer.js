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

        abcjs.renderAbc(el, el.dataset.abc, { responsive: 'resize' });

        function rerender() {
            var options = { responsive: 'resize' };
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
                if (isCustom && tuningInput.value.trim()) {
                    var parsed = parseTuning(tuningInput.value.trim());
                    if (parsed.length >= 2) {
                        if (instrumentBase.indexOf('customBanjo') === 0) {
                            parsed.push(parsed.shift());
                        }
                        tabConfig.tuning = parsed;
                    }
                }
                // If no valid custom tuning was set, don't render tablature at all
                // to prevent abcjs from crashing on invalid/partial input
                if (isCustom && !tabConfig.tuning) {
                    options.tablature = undefined;
                    abcjs.renderAbc(el, el.dataset.abc, options);
                    return;
                }

                options.tablature = [tabConfig];
            } else {
                select.style.display = 'none';
                droneLabel.style.display = 'none';
                tuningLabel.style.display = 'none';
            }
            try {
                el.innerHTML = '';
                abcjs.renderAbc(el, el.dataset.abc, options);
            } catch (e) {
                console.error('Tablature render error:', e);
                el.innerHTML = '';
                abcjs.renderAbc(el, el.dataset.abc, { responsive: 'resize' });
            }
        }

        checkbox.addEventListener('change', rerender);
        select.addEventListener('change', rerender);
        droneCheckbox.addEventListener('change', rerender);
        // Only re-render custom tuning on blur (tab/click away) or Enter key,
        // not on every keystroke — repeated rapid renders with changing tunings
        // can crash abcjs due to accumulated internal state.
        tuningInput.addEventListener('change', rerender);
        tuningInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); rerender(); }
        });
    });
}

renderAbcNotation();
document.addEventListener('turbo:load', renderAbcNotation);
