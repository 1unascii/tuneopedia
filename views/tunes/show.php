<link href="css/tune-page.css?v=6" rel="stylesheet" type="text/css"/>

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

    <?php if (!empty($settings) && !empty($settings[0]['abc_transcription'])): ?>
    <div id="tune-notation"></div>
    <?php else: ?>
    <p class="tune-page-no-notation">No notation available for this tune yet.</p>
    <?php endif; ?>

    <?php if (!empty($settings)): ?>
    <div id="tune-settings" data-primary-setting-id="<?= $primaryId ?>">
        <?php foreach ($settings as $i => $s):
            $isPrimary = ($i === 0);

            $abcStr =
                "X:" . intval($s['setting_id']) . "\n" .
                "T:" . $tuneName . "\n" .
                "M:" . $s['time_signature'] . "\n" .
                "L:" . ($s['default_note_length'] ?? '1/8') . "\n" .
                "K:" . $s['key_signature'] . "\n" .
                $s['abc_transcription'];

            $userVote = isset($s['user_vote']) ? (int)$s['user_vote'] : 0;
        ?>
        <div class="setting-block<?= $isPrimary ? ' primary-setting' : '' ?>"
             data-setting-id="<?= (int)$s['setting_id'] ?>"
             data-vote-score="<?= (int)$s['vote_score'] ?>">

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
                <button class="edit-setting-btn"
                        data-setting-id="<?= (int)$s['setting_id'] ?>"
                        title="Edit this setting">Edit</button>
            </div>

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

</div>
