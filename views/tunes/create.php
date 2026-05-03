<?php
    session_start();
    include_once(__DIR__ . '/../../config/database.php');
    require_once(__DIR__ . '/../../models/Tune.php');
    $pdo = connect();
    $tune_types = Tune::getAllTypes($pdo);
    $composers  = Tune::getAllComposers($pdo);
?>
<link href="css/tune-page.css?v=8" rel="stylesheet" type="text/css"/>

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
            <option value=""></option>
            <?php foreach ($composers as $row): ?>
            <option value="<?= htmlspecialchars($row['name']) ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="edit-field">
        <label>Metre</label>
        <select id="metre" name="metre">
            <option id="4/4">4/4</option>
            <option id="3/4">3/4</option>
            <option id="2/4">2/4</option>
            <option id="6/8">6/8</option>
            <option id="9/8">9/8</option>
            <option id="12/8">12/8</option>
            <option id="other_metre">Other(Advanced)</option>
        </select>
    </div>

    <div class="edit-field">
        <label>Mode</label>
        <select id="tune_mode_input">
            <option id="maj">Major/Ionian</option>
            <option id="min">Minor/Aeolian (natural minor)</option>
            <option id="dor">Dorian</option>
            <option id="mix">Mixolydian</option>
        </select>
    </div>

    <div class="edit-field" id="tune_key_input">
        <label>Key</label>
        <select id="key">
            <option id="c">C</option>
            <option id="g">G</option>
            <option id="d">D</option>
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

    <div class="edit-field edit-field-wide" id="abc_editor">
        <label>ABC</label>
        <textarea id="abc" rows="10" name="tune_body"></textarea>
    </div>

    <?php include __DIR__ . '/../partials/playback_controls.php'; ?>

    <div class="edit-form-actions">
        <div id="save_or_login">
            <input type="button" value="Save" id="save" class="edit-save-btn"/>
        </div>
    </div>

</form>
</div>

<div id="canvas_wrapper">
    <div id="warning_canvas_wrapper">
        <div id="canvas"></div>
        <div id="warnings"></div>
    </div>
</div>

<div id="add-tune-midi-player"></div>

<script>
$(function() {
    if (typeof window.startNewAbc === 'function') {
        window.startNewAbc();
    }
    $('.playback-dial').knob({ 'release': function(v) {} });
});
</script>
