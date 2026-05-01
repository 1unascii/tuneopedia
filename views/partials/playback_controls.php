<div class="edit-field edit-field-wide playback-controls">
    <label>Keypress Playback Settings</label>
    <div class="playback-controls-grid">

        <div class="playback-select">
            <label for="playback-instrument">Instrument</label>
            <select id="playback-instrument">
                <option value="0" selected>Piano</option>
                <option value="24">Nylon Guitar</option>
                <option value="25">Steel Guitar</option>
                <option value="105">Banjo</option>
                <option value="40">Violin</option>
                <option value="110">Fiddle</option>
                <option value="41">Viola</option>
                <option value="42">Cello</option>
                <option value="22">Harmonica</option>
                <option value="21">Accordion</option>
                <option value="73">Flute</option>
                <option value="72">Piccolo</option>
                <option value="74">Recorder</option>
                <option value="78">Whistle</option>
                <option value="71">Clarinet</option>
                <option value="109">Bagpipe</option>
                <option value="46">Harp</option>
                <option value="15">Dulcimer</option>
                <option value="79">Ocarina</option>
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
