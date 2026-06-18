<div class="edit-field edit-field-wide playback-controls">
    <label>Keypress Playback Settings</label>
    <div class="playback-controls-grid">

        <div class="playback-select">
            <label for="playback-instrument">Instrument</label>
            <?php
                $instrumentPdo = connect();
                $instruments = $instrumentPdo->query('SELECT instrument_id, name, midi_program FROM instrument ORDER BY sort_order, name')->fetchAll(PDO::FETCH_ASSOC);
                $selectedId = !empty($setting['instrument_id']) ? (int)$setting['instrument_id'] : null;
            ?>
            <select id="playback-instrument" name="instrument_id">
                <?php foreach ($instruments as $inst): ?>
                    <option value="<?= (int)$inst['instrument_id'] ?>"
                            data-midi="<?= (int)$inst['midi_program'] ?>"
                            <?= $selectedId === (int)$inst['instrument_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($inst['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="playback-knob">
            <label>Tempo</label>
            <input type="text" id="playback-tempo" name="tempo" class="playback-dial" value="<?= (int)(!empty($setting['tempo']) ? $setting['tempo'] : 120) ?>"
                   data-min="40" data-max="300" data-step="5"
                   data-width="60" data-height="60"
                   data-fgColor="#59b4d4" data-bgColor="#333"
                   data-inputColor="#ddd" data-font="monospace"
                   data-thickness=".3" />
        </div>

        <div class="playback-knob">
            <label>Volume</label>
            <input type="text" id="playback-volume" class="playback-dial" value="50"
                   data-min="0" data-max="100" data-step="1"
                   data-width="60" data-height="60"
                   data-fgColor="#59b4d4" data-bgColor="#333"
                   data-inputColor="#ddd" data-font="monospace"
                   data-thickness=".3" />
        </div>

        <div class="playback-play-selection">
            <span id="play_selection"></span>
        </div>

    </div>
</div>

<script>
$(function() {
    $('.playback-dial').knob({
        'release': function(v) { /* value is read live by getPlaybackOptions() */ }
    });
});
</script>
