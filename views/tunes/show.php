<?php $showAssetBase = preg_replace('#/public$#', '', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')) . '/'; ?>
<link href="<?= $showAssetBase ?>css/tune-page.css?v=7" rel="stylesheet" type="text/css"/>

<div id="tune-page" data-tune-id="<?= $tune_id ?>" data-user-id="<?= $userId ?>">

    <p class='tune-page-setting-count'><?php echo $settingCount; ?>
    <?php if ($settingCount === 1): echo "setting";?>
    <?php else: echo "settings";?>
    <?php endif; ?>
    </p>

    <?php if (!empty($tuneNotes)): ?>
    <div class="tune-page-notes collapsed">
        <div class="tune-page-notes-body">
            <?php foreach ($tuneNotes as $note): ?>
            <p><?= nl2br(htmlspecialchars(preg_replace('/\n+/', "\n", trim($note)))) ?></p>
            <?php endforeach; ?>
        </div>
        <button class="tune-page-notes-toggle" type="button">Show more</button>
    </div>
    <?php endif; ?>

    <div class="playback-options-row">
        <label><input type="checkbox" id="playback-highlight" checked /> Highlight Notes</label>
        <label><input type="checkbox" id="playback-cursor" checked /> Show Cursor</label>
    </div>

    <div class="tablature-select-row">
        <label for="tablature-instrument">Tablature</label>
        <select id="tablature-instrument">
            <option value="">None</option>
            <optgroup label="Fiddle Family">
                <option value="fiddle">Fiddle / Violin (GDAE)</option>
                <option value="mandolin">Mandolin (GDAE)</option>
            </optgroup>
            <optgroup label="Guitar">
                <option value="guitar">Guitar - Standard (EADGBE)</option>
                <option value="guitar-dadgad">Guitar - DADGAD</option>
                <option value="guitar-open-d">Guitar - Open D (DADF#Ad)</option>
                <option value="guitar-open-g">Guitar - Open G (DGDGBD)</option>
            </optgroup>
            <optgroup label="5-String Banjo">
                <option value="banjo-open-g">Open G / Standard (gDGBD)</option>
                <option value="banjo-double-c">Double C (gCGCD)</option>
                <option value="banjo-sawmill">G Modal / Sawmill (gDGCD)</option>
                <option value="banjo-drop-c">Drop C (gCGBD)</option>
                <option value="banjo-open-d">Open D / Graveyard (f#DF#AD)</option>
                <option value="banjo-g-minor">G Minor (gDGBbD)</option>
                <option value="banjo-a-scale">A-Scale (aEAC#E)</option>
                <option value="banjo-long-neck">Long Neck / Open E (eBEG#B)</option>
            </optgroup>
            <optgroup label="4-String Banjo">
                <option value="banjo-tenor-standard">Standard Tenor (CGDA)</option>
                <option value="banjo-tenor-irish">Irish Tenor (GDAE)</option>
                <option value="banjo-chicago">Chicago (DGBE)</option>
                <option value="banjo-plectrum">Plectrum (CGBD)</option>
            </optgroup>
            <optgroup label="Specialty Banjo">
                <option value="banjo-6string">6-String Guitanjo (EADGBE)</option>
                <option value="banjo-ukulele">Banjo Ukulele (GCEA)</option>
            </optgroup>
            <option value="custom">Custom Tuning...</option>
        </select>
    </div>
    <div id="custom-tuning-controls" style="display:none">
            <div class="custom-tuning-row">
                <label for="custom-instrument">Instrument</label>
                <select id="custom-instrument">
                    <option value="fiddle">Fiddle</option>
                    <option value="mandolin">Mandolin</option>
                    <option value="guitar">Guitar</option>
                    <option value="banjo">Banjo</option>
                </select>
            </div>
            <div class="custom-tuning-row">
                <label for="custom-strings">Strings</label>
                <select id="custom-strings">
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
            </div>
            <div class="custom-tuning-row">
                <label for="custom-tuning-input">Tuning</label>
                <input type="text" id="custom-tuning-input" placeholder="e.g. DAdea" />
            </div>
            <button type="button" id="custom-tuning-apply">Apply</button>
    </div>

    <?php if (!empty($settings) && !empty($settings[0]['abc_transcription'])): ?>
    <div id="tune-notation"></div>
    <?php else: ?>
    <p class="tune-page-no-notation">No notation available for this tune yet.</p>
    <?php endif; ?>

    <?php if (!empty($settings)): ?>
    <div id="tune-settings" data-primary-setting-id="<?= $primaryId ?>">
        <?php foreach ($settings as $i => $s):
            $isPrimary = ($i === 0);

            $tempo = !empty($s['tempo']) ? (int)$s['tempo'] : 120;
            $abcStr =
                "X:" . intval($s['setting_id']) . "\n" .
                "T:" . $tuneName . "\n" .
                "M:" . $s['time_signature'] . "\n" .
                "L:" . ($s['default_note_length'] ?? '1/8') . "\n" .
                "Q:1/4=" . $tempo . "\n" .
                "K:" . $s['key_signature'] . "\n" .
                $s['abc_transcription'];

            $userVote = isset($s['user_vote']) ? (int)$s['user_vote'] : 0;
        ?>
        <div class="setting-block<?= $isPrimary ? ' primary-setting' : '' ?>"
             data-setting-id="<?= (int)$s['setting_id'] ?>"
             data-vote-score="<?= (int)$s['vote_score'] ?>"
             data-tempo="<?= (int)($s['tempo'] ?? 120) ?>">

            <script class="setting-abc-data"
                    data-setting-id="<?= (int)$s['setting_id'] ?>"
                    type="application/json"><?= json_encode($abcStr) ?></script>

            <div class="setting-notation"
                 id="setting-notation-<?= (int)$s['setting_id'] ?>"
                 <?= $isPrimary ? 'style="display:none"' : '' ?>></div>

            <div class="setting-meta">
                <span class="setting-label">Setting <?= $i + 1 ?></span>
                <?php if (!empty($s['key_signature'])): ?>
                <span class="setting-key"><?= htmlspecialchars($s['key_signature']) ?></span>
                <?php endif; ?>

                <?php if (!empty($s['user_name'])): ?>
                <span class="setting-submitter">Uploaded by <?= htmlspecialchars($s['user_name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="setting-vote-controls">
                <button class="vote-btn vote-up<?= $userVote === 1 ? ' vote-active' : '' ?>"
                        data-setting-id="<?= (int)$s['setting_id'] ?>"
                        title="Upvote">&#9650;</button>
                <span class="vote-score"><?= (int)$s['vote_score'] ?></span>
                <button class="vote-btn vote-down<?= $userVote === -1 ? ' vote-active' : '' ?>"
                        data-setting-id="<?= (int)$s['setting_id'] ?>"
                        title="Downvote">&#9660;</button>
                <?php if ($userId && (int)$s['user_id'] === $userId): ?>
                <button class="edit-setting-btn"
                        data-setting-id="<?= (int)$s['setting_id'] ?>"
                        title="Edit this setting">Edit</button>
                <?php endif; ?>
            </div>

            <div class="setting-midi-player" id="midi-player-<?= (int)$s['setting_id'] ?>"></div>

            <div class="setting-edit-area" style="display:none"></div>

            <?php if (!empty($s['setting_notes'])): ?>
            <div class="tune-page-notes">
                <p><?= nl2br(htmlspecialchars($s['setting_notes'])) ?></p>
            </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($userId): ?>
    <div class="add-setting-section">
        <button class="add-setting-btn" data-tune-id="<?= $tune_id ?>">Add Setting</button>
        <div class="add-setting-area" style="display:none"></div>
    </div>
    <?php endif; ?>

</div>

<script>
window.tablaturePresets = {
    // Fiddle family
    'fiddle':            { instrument: 'fiddle' },
    'mandolin':          { instrument: 'mandolin' },
    // Guitar
    'guitar':            { instrument: 'guitar' },
    'guitar-dadgad':     { instrument: 'guitar', tuning: ['D,', 'A,', 'D', 'G', 'A', 'd'], label: 'Guitar DADGAD %T' },
    'guitar-open-d':     { instrument: 'guitar', tuning: ['D,', 'A,', 'D', '^F', 'A', 'd'], label: 'Guitar Open D %T' },
    'guitar-open-g':     { instrument: 'guitar', tuning: ['D,', 'G,', 'D', 'G', 'B', 'd'], label: 'Guitar Open G %T' },
    // 5-string banjo (ascending pitch; strOrder [4,0,1,2,3] puts drone on bottom)
    'banjo-open-g':      { instrument: 'banjo', tuning: ['D', 'G', 'B', 'd', 'g'], label: 'Banjo Open G (gDGBD)' },
    'banjo-double-c':    { instrument: 'banjo', tuning: ['C', 'G', 'c', 'd', 'g'], label: 'Banjo Double C (gCGCD)' },
    'banjo-sawmill':     { instrument: 'banjo', tuning: ['D', 'G', 'c', 'd', 'g'], label: 'Banjo Sawmill (gDGCD)' },
    'banjo-drop-c':      { instrument: 'banjo', tuning: ['C', 'G', 'B', 'd', 'g'], label: 'Banjo Drop C (gCGBD)' },
    'banjo-open-d':      { instrument: 'banjo', tuning: ['D', '^F', 'A', 'd', '^f'], label: 'Banjo Open D (f#DF#AD)' },
    'banjo-g-minor':     { instrument: 'banjo', tuning: ['D', 'G', '_B', 'd', 'g'], label: 'Banjo G Minor (gDGBbD)' },
    'banjo-a-scale':     { instrument: 'banjo', tuning: ['E', 'A', '^c', 'e', 'a'], label: 'Banjo A-Scale (aEAC#E)' },
    'banjo-long-neck':   { instrument: 'banjo', tuning: ['B,', 'E', '^G', 'B', 'e'], label: 'Banjo Long Neck (eBEG#B)' },
    // 4-string banjo
    'banjo-tenor-standard': { instrument: 'fiddle', tuning: ['C', 'G', 'd', 'a'], label: 'Tenor Standard %T' },
    'banjo-tenor-irish': { instrument: 'fiddle', tuning: ['G,', 'D', 'A', 'e'], label: 'Tenor Irish %T' },
    'banjo-chicago':     { instrument: 'fiddle', tuning: ['D', 'G', 'B', 'e'], label: 'Chicago %T' },
    'banjo-plectrum':    { instrument: 'fiddle', tuning: ['C', 'G', 'B', 'd'], label: 'Plectrum %T' },
    // Specialty
    'banjo-6string':     { instrument: 'guitar', label: '6-String Guitanjo %T' },
    'banjo-ukulele':     { instrument: 'fiddle', tuning: ['c', 'e', 'g', 'a'], label: 'Banjo Ukulele %T' },
};
</script>

<script>
$(function() {
    var $page = $('#tune-page');
    if (!$page.length) return;

    function getTablatureParams() {
        var val = $('#tablature-instrument').val();
        if (!val) return {};
        var tab = window.tablaturePresets[val];
        if (!tab) return {};
        var entry = { instrument: tab.instrument };
        if (tab.tuning) entry.tuning = tab.tuning;
        if (tab.label) entry.label = tab.label;
        return { tablature: [entry], visualTranspose: 0 };
    }

    function renderAllSettings() {
        var params = getTablatureParams();
        params.add_classes = true;

        var $primaryBlock = $page.find('.setting-block:first');
        if ($primaryBlock.length) {
            var $primaryAbc = $primaryBlock.find('.setting-abc-data');
            if ($primaryAbc.length) {
                try {
                    var vis = ABCJS.renderAbc('tune-notation', JSON.parse($primaryAbc[0].textContent), params);
                    if (vis && vis[0]) $primaryBlock.data('visualObj', vis[0]);
                } catch(e) {}
            }
        }

        $page.find('.setting-block:not(:first-child)').each(function() {
            var $block = $(this);
            var $abcEl = $block.find('.setting-abc-data');
            var $notDiv = $block.find('.setting-notation');
            if ($abcEl.length && $notDiv.length) {
                try {
                    var vis = ABCJS.renderAbc($notDiv.attr('id'), JSON.parse($abcEl[0].textContent), params);
                    if (vis && vis[0]) $block.data('visualObj', vis[0]);
                } catch(e) {}
            }
        });
    }

    renderAllSettings();
    if (typeof initAllMidiPlayers === 'function') {
        initAllMidiPlayers();
        $page.find('.setting-block').each(function() {
            var $block = $(this);
            var vis = $block.data('visualObj');
            if (vis && typeof setMidiTune === 'function') setMidiTune($block, vis);
        });
    }
});
</script>
