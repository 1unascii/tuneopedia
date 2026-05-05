<?php
    session_start();
    include_once(__DIR__ . '/../../config/database.php');
    require_once(__DIR__ . '/../../models/Tune.php');
    $pdo = connect();
    $tune_types = Tune::getAllTypes($pdo);
    $composers  = Tune::getAllComposers($pdo);

    // Reuse key parsing helpers from the edit form
    if (!function_exists('editFormParseKey')) {
        function editFormParseKey($keySig) {
            $keySig = trim($keySig);
            if (preg_match('/^([A-G][b#]?)\s*dor(ian)?$/i', $keySig, $m))
                return ['mode' => 'dor', 'base' => $m[1], 'display' => $m[1] . ' dorian'];
            if (preg_match('/^([A-G][b#]?)\s*mix(olydian)?$/i', $keySig, $m))
                return ['mode' => 'mix', 'base' => $m[1], 'display' => $m[1] . ' Mixolydian'];
            if (preg_match('/^([A-G][b#]?)\s*(min(or)?|m)$/i', $keySig, $m))
                return ['mode' => 'min', 'base' => $m[1], 'display' => $m[1] . ' minor'];
            if (preg_match('/^([A-G][b#]?)\s*maj(or)?$/i', $keySig, $m))
                return ['mode' => 'maj', 'base' => $m[1], 'display' => $m[1]];
            return ['mode' => 'maj', 'base' => $keySig, 'display' => $keySig];
        }
    }

    if (!function_exists('editFormKeyOptions')) {
        function editFormKeyOptions($mode, $selectedDisplay) {
            $keys = [
                'maj' => ['F','C','G','D','A','E','B','F#','C#','Bb','Eb','Ab','Db'],
                'min' => ['F','C','G','D','A','E','B','F#','C#','Bb','Eb','Ab'],
                'dor' => ['F','C','G','D','A','E','B','F#','C#','Bb','Eb','Ab','Db'],
                'mix' => ['F','C','G','D','A','E','B','F#','C#','Bb','Eb','Ab','Db','Gb'],
            ];
            $suffix = ['maj' => '', 'min' => ' minor', 'dor' => ' dorian', 'mix' => ' Mixolydian'];
            $idMap  = ['F#'=>'fsharp','C#'=>'csharp','Bb'=>'bb','Eb'=>'eb',
                       'Ab'=>'ab', 'Db'=>'db', 'Gb'=>'gb'];
            $html = '';
            foreach ($keys[$mode] as $base) {
                $display = $base . $suffix[$mode];
                $id      = $idMap[$base] ?? strtolower($base);
                $sel     = ($display === $selectedDisplay) ? ' selected' : '';
                $html   .= "<option id=\"{$id}\"{$sel}>{$display}</option>\n";
            }
            return $html;
        }
    }

    $currentMode = 'maj';
    $currentDisp = 'D';
    $modeLabels  = ['maj' => 'Major/Ionian', 'min' => 'Minor', 'dor' => 'Dorian', 'mix' => 'Mixolydian'];
    $setting = ['instrument_id' => null, 'tempo' => 120];
?>
<link href="css/tune-page.css?v=11" rel="stylesheet" type="text/css"/>

<div id="form_wrapper">
<form id="form_for_new_tune" class="edit-setting-form">

    <div class="edit-field">
        <label>Title</label>
        <input type="text" id="tune_title" name="tune_title"
               <?php if (isset($_GET['title'])) { echo 'value="' . htmlspecialchars($_GET['title']) . '"'; } ?> />
    </div>

    <div class="edit-field">
        <label>Tune Type</label>
        <select id="tune_type" name="tune_type">
            <?php foreach ($tune_types as $row): ?>
            <option value="<?= htmlspecialchars($row['name']) ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Composer</label>
        <select id="composer" name="composer">
            <option value="">Traditional</option>
            <?php foreach ($composers as $row): ?>
            <option value="<?= htmlspecialchars($row['name']) ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Time Signature</label>
        <select id="metre" name="metre">
            <?php foreach (['4/4','6/8','9/8','12/8','3/4','2/4'] as $m): ?>
            <option value="<?= $m ?>"><?= $m ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Default Note Length</label>
        <select id="default_note_length" name="default_note_length">
            <?php foreach (['1/4','1/8','1/16'] as $nl): ?>
            <option value="<?= $nl ?>"<?= $nl === '1/8' ? ' selected' : '' ?>><?= $nl ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Mode</label>
        <select id="tune_mode_input">
            <?php foreach ($modeLabels as $id => $label): ?>
            <option id="<?= $id ?>"<?= $currentMode === $id ? ' selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Key</label>
        <select id="key" name="tune_key">
            <?= editFormKeyOptions($currentMode, $currentDisp) ?>
        </select>
    </div>

    <div class="edit-field edit-field-wide">
        <label>ABC Notation</label>
        <textarea id="abc" class="edit-setting-abc" name="tune_body" rows="10"></textarea>
    </div>

    <?php include __DIR__ . '/../partials/playback_controls.php'; ?>

    <div class="edit-form-actions">
        <input type="button" value="Save" id="save" class="edit-save-btn"/>
    </div>

</form>
</div>

<div id="canvas_wrapper">
    <div id="canvas"></div>
</div>

<div class="setting-midi-row">
    <span class="midi-volume-control">
        <label>Vol</label>
        <input type="text" id="add-tune-volume" class="playback-dial" value="25"
               data-min="0" data-max="100" data-step="1"
               data-width="35" data-height="35"
               data-fgColor="#59b4d4" data-bgColor="#333"
               data-inputColor="#ddd" data-font="monospace"
               data-thickness=".3" />
    </span>
    <div id="add-tune-midi-player"></div>
</div>

