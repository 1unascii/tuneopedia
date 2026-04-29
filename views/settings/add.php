<?php
// Expects $tuneId and $tuneName to be set by the including script.
// Session and DB connection already started by the controller.
?>
<form class="add-setting-form edit-setting-form" data-tune-id="<?= (int)$tuneId ?>">
    <input type="hidden" name="tune_id" value="<?= (int)$tuneId ?>">

    <div class="edit-field">
        <label>Time Signature</label>
        <select name="metre">
            <?php foreach (['4/4','6/8','9/8','12/8','3/4','2/4'] as $m): ?>
            <option value="<?= $m ?>"><?= $m ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Mode</label>
        <select id="tune_mode_input" class="edit-mode-select">
            <option id="maj">Major</option>
            <option id="min">Minor</option>
            <option id="dor">Dorian</option>
            <option id="mix">Mixolydian</option>
        </select>
    </div>

    <div class="edit-field">
        <label>Key</label>
        <select id="key" name="tune_key">
            <option id="c">C</option>
            <option id="g">G</option>
            <option id="d" selected>D</option>
            <option id="a">A</option>
            <option id="e">E</option>
            <option id="b">B</option>
            <option id="fsharp">F#</option>
            <option id="csharp">C#</option>
            <option id="f">F</option>
            <option id="bb">Bb</option>
            <option id="eb">Eb</option>
            <option id="ab">Ab</option>
            <option id="db">Db</option>
        </select>
    </div>

    <div class="edit-field edit-field-wide">
        <label>ABC Notation</label>
        <textarea id="abc" class="edit-setting-abc" name="tune_body" rows="10"></textarea>
    </div>

    <?php include __DIR__ . '/../partials/playback_controls.php'; ?>

    <div class="edit-form-actions">
        <button type="submit" class="edit-save-btn">Submit Setting</button>
        <button type="button" class="add-setting-cancel-btn">Cancel</button>
        <span class="edit-error"></span>
    </div>
</form>
