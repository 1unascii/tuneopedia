<?php
// Expects $setting and $tuneTypes to be set by the including script.

// ── Parse key_signature into mode + base key ──────────────────────────────────
// Handles ABC formats: "D", "Edor", "D dorian", "Gmix", "Amin", "A minor", etc.
function editFormParseKey($keySig) {
    $keySig = trim($keySig);
    if (preg_match('/^([A-G][b#]?)\s*dor(ian)?$/i', $keySig, $m)) {
        return ['mode' => 'dor', 'base' => $m[1], 'display' => $m[1] . ' dorian'];
    }
    if (preg_match('/^([A-G][b#]?)\s*mix(olydian)?$/i', $keySig, $m)) {
        return ['mode' => 'mix', 'base' => $m[1], 'display' => $m[1] . ' Mixolydian'];
    }
    if (preg_match('/^([A-G][b#]?)\s*(min(or)?|m)$/i', $keySig, $m)) {
        return ['mode' => 'min', 'base' => $m[1], 'display' => $m[1] . ' minor'];
    }
    if (preg_match('/^([A-G][b#]?)\s*maj(or)?$/i', $keySig, $m)) {
        return ['mode' => 'maj', 'base' => $m[1], 'display' => $m[1]];
    }
    // Plain key (no mode suffix) = major
    return ['mode' => 'maj', 'base' => $keySig, 'display' => $keySig];
}

// ── Render <option> elements for the key select ───────────────────────────────
// Option text matches what abc_playback.js's sharpKeysArray / flatKeysArray expect.
function editFormKeyOptions($mode, $selectedDisplay) {
    $keys = [
        'maj' => ['F','C','G','D','A','E','B','F#','C#','Bb','Eb','Ab','Db','Gb','Cb'],
        'min' => ['F','C','G','D','A','E','B','F#','C#','Bb','Eb','Ab'],
        'dor' => ['F','C','G','D','A','E','B','F#','C#','Bb','Eb','Ab','Db'],
        'mix' => ['F','C','G','D','A','E','B','F#','C#','Bb','Eb','Ab','Db','Gb'],
    ];
    $suffix = ['maj' => '', 'min' => ' minor', 'dor' => ' dorian', 'mix' => ' Mixolydian'];
    $idMap  = ['F#'=>'fsharp','C#'=>'csharp','Bb'=>'bb','Eb'=>'eb',
               'Ab'=>'ab','Db'=>'db','Gb'=>'gb','Cb'=>'cb'];

    $html = '';
    foreach ($keys[$mode] as $base) {
        $display = $base . $suffix[$mode];
        $id      = $idMap[$base] ?? strtolower($base);
        $sel     = ($display === $selectedDisplay) ? ' selected' : '';
        $html   .= "<option id=\"{$id}\"{$sel}>{$display}</option>\n";
    }
    return $html;
}

$parsed      = editFormParseKey($setting['key_signature'] ?? '');
$currentMode = $parsed['mode'];
$currentDisp = $parsed['display'];

$modeLabels  = ['maj' => 'Major/Ionian', 'min' => 'Minor', 'dor' => 'Dorian', 'mix' => 'Mixolydian'];
?>
<form class="edit-setting-form" data-setting-id="<?= (int)$settingId ?>">
    <input type="hidden" name="setting_id" value="<?= (int)$settingId ?>">

    <div class="edit-field">
        <label>Title</label>
        <input type="text" name="tune_name"
               value="<?= htmlspecialchars($setting['tune_name']) ?>">
    </div>

    <div class="edit-field">
        <label>Tune Type</label>
        <select name="tune_type">
            <?php foreach ($tuneTypes as $tt): ?>
            <option value="<?= htmlspecialchars($tt['name']) ?>"
                    <?= strtolower($tt['name']) === strtolower((string)$setting['tune_type_name']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($tt['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Time Signature</label>
        <select name="time_signature">
            <?php foreach (['4/4','6/8','9/8','12/8','3/4','2/4'] as $m): ?>
            <option value="<?= $m ?>"
                    <?= $setting['time_signature'] === $m ? 'selected' : '' ?>><?= $m ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Default Note Length</label>
        <select name="default_note_length">
            <?php foreach (['1/4','1/8','1/16'] as $nl): ?>
            <option value="<?= $nl ?>"
                    <?= $setting['default_note_length'] === $nl ? 'selected' : '' ?>><?= $nl ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Mode</label>
        <select id="tune_mode_input" class="edit-mode-select">
            <?php foreach ($modeLabels as $id => $label): ?>
            <option id="<?= $id ?>"<?= $currentMode === $id ? ' selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Key</label>
        <select id="key" name="key_signature">
            <?= editFormKeyOptions($currentMode, $currentDisp) ?>
        </select>
    </div>

    <div class="edit-field edit-field-wide">
        <label>ABC Notation</label>
        <textarea id="abc" class="edit-setting-abc"
                  name="abc_transcription"
                  rows="10"><?= htmlspecialchars($setting['abc_transcription']) ?></textarea>
    </div>

    <div class="edit-form-actions">
        <button type="submit" class="edit-save-btn">Save</button>
        <button type="button" class="edit-cancel-btn">Cancel</button>
        <span class="edit-error"></span>
    </div>
</form>
