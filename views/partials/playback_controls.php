<div class="edit-field edit-field-wide playback-controls">
    <label>Playback Settings</label>
    <div class="playback-controls-grid">

        <div class="playback-select">
            <label for="playback-waveform">Waveform</label>
            <select id="playback-waveform">
                <option value="sine">Sine</option>
                <option value="square">Square</option>
                <option value="sawtooth">Sawtooth</option>
                <option value="triangle" selected>Triangle</option>
            </select>
        </div>

        <div class="playback-knob">
            <label>Tempo</label>
            <input type="text" id="playback-tempo" class="playback-dial" value="120"
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

        <div class="playback-knob">
            <label>Attack</label>
            <input type="text" id="playback-attack" class="playback-dial" value="1"
                   data-min="0" data-max="100" data-step="1"
                   data-width="60" data-height="60"
                   data-fgColor="#ffc73d" data-bgColor="#333"
                   data-inputColor="#ddd" data-font="monospace"
                   data-thickness=".3" />
        </div>

        <div class="playback-knob">
            <label>Decay</label>
            <input type="text" id="playback-decay" class="playback-dial" value="20"
                   data-min="0" data-max="100" data-step="1"
                   data-width="60" data-height="60"
                   data-fgColor="#ffc73d" data-bgColor="#333"
                   data-inputColor="#ddd" data-font="monospace"
                   data-thickness=".3" />
        </div>

        <div class="playback-knob">
            <label>Sustain</label>
            <input type="text" id="playback-sustain" class="playback-dial" value="10"
                   data-min="0" data-max="100" data-step="1"
                   data-width="60" data-height="60"
                   data-fgColor="#ffc73d" data-bgColor="#333"
                   data-inputColor="#ddd" data-font="monospace"
                   data-thickness=".3" />
        </div>

        <div class="playback-knob">
            <label>Release</label>
            <input type="text" id="playback-release" class="playback-dial" value="10"
                   data-min="0" data-max="100" data-step="1"
                   data-width="60" data-height="60"
                   data-fgColor="#ffc73d" data-bgColor="#333"
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
