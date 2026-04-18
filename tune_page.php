<?php
// ── Session & dependencies ────────────────────────────────────────────────────
// Start a session only if one isn't already active (this file may be included
// inside an already-started session from the outer page).
if (session_status() === PHP_SESSION_NONE) session_start();

// connect.php exposes connect() which returns a PDO instance.
// functions.php contains shared helper utilities used across the app.
include_once('connect.php');
include_once('functions.php');

// ── Input validation ──────────────────────────────────────────────────────────
// Read tune_id from the query string and cast to int immediately.
// intval() returns 0 for missing/non-numeric values, so the check below catches both.
$tune_id = intval($_GET['tune_id'] ?? 0);
if (!$tune_id) {
    echo '<p class="error-message">Tune not found.</p>';
    exit;
}

// ── Auth & DB ─────────────────────────────────────────────────────────────────
// Grab the logged-in user id (0 means guest / not logged in).
// This is passed to the settings query so it can flag which way the current user voted.
$userId = (int)($_SESSION['user_id'] ?? 0);
$pdo    = connect();

// ── Fetch tune name ───────────────────────────────────────────────────────────
// We need the tune name separately because the settings query returns it per-row,
// but we also need it for the ABC header (T: field) when building ABC strings.
$stmt = $pdo->prepare("SELECT name FROM tune WHERE tune_id = :tune_id");
$stmt->execute([':tune_id' => $tune_id]);
$tuneName = $stmt->fetchColumn();

// Bail out early if the tune id doesn't exist in the database.
if (!$tuneName) {
    echo '<p class="error-message">Tune not found.</p>';
    exit;
}

// ── Fetch all settings for this tune ─────────────────────────────────────────
// get_tune_settings.sql returns every setting row joined with:
//   - vote totals (vote_score)
//   - the current user's own vote direction (user_vote: 1, -1, or NULL)
//   - the uploader's username
// Results are already ordered by vote_score DESC so the highest-voted setting
// comes first — that first row becomes the "primary" setting rendered at the top.
$stmt = $pdo->prepare(file_get_contents('sql/get_tune_settings.sql'));
$stmt->execute([':tune_id' => $tune_id, ':user_id' => $userId]);
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── Fetch tune-level notes ────────────────────────────────────────────────────
// tune_note rows are free-text annotations about the tune as a whole
// (not tied to any specific setting). Errors are silently swallowed because
// the notes table is optional — the page still works if it doesn't exist yet.
$tuneNotes = [];
try {
    $stmt = $pdo->prepare("SELECT note FROM tune_note WHERE tune_id = :tune_id");
    $stmt->execute([':tune_id' => $tune_id]);
    $tuneNotes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

// ── Convenience variables ─────────────────────────────────────────────────────
// $settingCount — used for the display label ("1 setting" vs "N settings").
// $primaryId    — the setting_id of the top-voted (first) setting; stored on the
//                 #tune-settings container so JS can track which setting currently
//                 occupies the primary slot without re-querying the DOM every time.
$settingCount  = count($settings);
$primaryId     = !empty($settings) ? (int)$settings[0]['setting_id'] : 0;
?>
<link href="css/tune_page.css?v=5" rel="stylesheet" type="text/css"/>

<!--
    #tune-page is the root container for the whole tune page fragment.
    data-tune-id is read by JS when it needs to make AJAX calls that require
    the tune id (e.g. fetching the edit form via edit_setting.php).
-->
<div id="tune-page" data-tune-id="<?= $tune_id ?>">

    <!-- ── Setting count label ──────────────────────────────────────────────── -->
    <!-- Displays "N setting" or "N settings" depending on count. -->
    <p class='tune-page-setting-count'><?php echo $settingCount; ?>
    <?php if ($settingCount === 1): echo "setting";?>
    <?php else: echo "settings";?>
    <?php endif; ?>
    </p>


    <!-- ── Tune-level notes (collapsible) ───────────────────────────────────── -->
    <!--
        If there are any tune_note rows for this tune they are shown here in a
        collapsible panel. The "collapsed" CSS class limits the max-height so only
        the first couple of lines are visible. JS in tune_page.js handles the
        Show more / Show less toggle by animating max-height.
        nl2br + htmlspecialchars + preg_replace collapse consecutive blank lines
        so imported annotations don't have excessive whitespace.
    -->
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

    <!-- ── Primary notation area ────────────────────────────────────────────── -->
    <!--
        #tune-notation is the single top-level div where ABCJS renders the
        sheet music for whichever setting is currently ranked first (highest votes).
        ABCJS.renderAbc('tune-notation', abcString) is called from JS (tune_page.js)
        on page load and whenever votes change the ranking.

        The per-setting <div class="setting-notation"> inside each .setting-block
        mirrors this slot — the primary setting's own .setting-notation is hidden
        (display:none) because its ABC is displayed here instead.

        If the top setting has no abc_transcription at all, a fallback message is
        shown instead and #tune-notation is never created, so ABCJS is never called.
    -->
    <?php if (!empty($settings) && !empty($settings[0]['abc_transcription'])): ?>
    <div id="tune-notation"></div>
    <?php else: ?>
    <p class="tune-page-no-notation">No notation available for this tune yet.</p>
    <?php endif; ?>

    <!-- ── Settings list ────────────────────────────────────────────────────── -->
    <!--
        #tune-settings wraps all .setting-block cards.
        data-primary-setting-id tracks which setting currently occupies the primary
        slot so JS (reorderSettings) can detect when a vote pushes a new setting
        to the top and swap the notation accordingly.
    -->
    <?php if (!empty($settings)): ?>
    <div id="tune-settings" data-primary-setting-id="<?= $primaryId ?>">
        <?php foreach ($settings as $i => $s):
            // The first setting in the sorted result is the primary (highest voted).
            $isPrimary = ($i === 0);

            // Build a complete ABC string for this setting.
            // ABC format requires these header fields in order before the note body:
            //   X: — reference number (we use setting_id as a unique integer)
            //   T: — title
            //   M: — time signature (e.g. 3/4, 4/4, 6/8)
            //   L: — default note length (e.g. 1/8)
            //   K: — key signature (e.g. G, Dmix, Ador) — must be last header line
            // abc_transcription contains the raw note body that follows the headers.
            $abcStr =
                "X:" . intval($s['setting_id']) . "\n" .
                "T:" . $tuneName . "\n" .
                "M:" . $s['time_signature'] . "\n" .
                "L:" . ($s['default_note_length'] ?? '1/8') . "\n" .
                "K:" . $s['key_signature'] . "\n" .
                $s['abc_transcription'];

            // Normalise the user's vote to an int (NULL from SQL becomes 0 in PHP).
            $userVote = isset($s['user_vote']) ? (int)$s['user_vote'] : 0;
        ?>
        <!--
            .setting-block is the card for one setting.
            data-setting-id  — the DB primary key; used by vote and edit AJAX calls.
            data-vote-score  — current net score; used by JS to sort blocks after a vote.
            .primary-setting is added to the top-ranked block so JS and CSS can
            treat it differently (its notation shows in #tune-notation, not inline).
        -->
        <div class="setting-block<?= $isPrimary ? ' primary-setting' : '' ?>"
             data-setting-id="<?= (int)$s['setting_id'] ?>"
             data-vote-score="<?= (int)$s['vote_score'] ?>">

            <!--
                .setting-abc-data is an invisible <script type="application/json"> tag
                that stores the full ABC string for this setting as JSON.
                It is NOT executed as JavaScript — type="application/json" prevents that.
                JS reads it (JSON.parse) whenever it needs to re-render the notation
                without a round-trip to the server, e.g. after a vote reorder or when
                cancelling an edit and restoring the original notation.
            -->
            <script class="setting-abc-data"
                    data-setting-id="<?= (int)$s['setting_id'] ?>"
                    type="application/json"><?= json_encode($abcStr) ?></script>

            <!--
                .setting-notation is the per-setting notation target div.
                For the primary setting it is hidden (display:none) because its ABC
                is rendered into the shared #tune-notation div at the top of the page.
                For all non-primary settings ABCJS renders directly into this div.
                When a vote promotes this block to primary, JS hides this div and
                moves the rendered notation up to #tune-notation.
            -->
            <div class="setting-notation"
                 id="setting-notation-<?= (int)$s['setting_id'] ?>"
                 <?= $isPrimary ? 'style="display:none"' : '' ?>></div>

            <!-- ── Setting metadata row ───────────────────────────────────── -->
            <div class="setting-meta">
                <!-- "Setting 1", "Setting 2", etc. — JS renumbers these after vote reorder -->
                <span class="setting-label">Setting <?= $i + 1 ?></span>
                <?php if (!empty($s['key_signature'])): ?>
                <span class="setting-key"><?= htmlspecialchars($s['key_signature']) ?></span>
                <?php endif; ?>

                <?php if (!empty($s['user_name'])): ?>
                <span class="setting-submitter">Uploaded by <?= htmlspecialchars($s['user_name']) ?></span>
                <?php endif; ?>
            </div>

            <!-- ── Vote controls ─────────────────────────────────────────── -->
            <!--
                Up/down vote buttons post to vote_setting.php via AJAX (tune_page.js).
                .vote-active is added server-side when the current user has already
                voted in that direction, giving visual feedback on page load.
                After a vote the JS handler updates the score, toggles .vote-active,
                and calls reorderSettings() which re-sorts the .setting-block cards.
            -->
            <div class="setting-vote-controls">
                <button class="vote-btn vote-up<?= $userVote === 1 ? ' vote-active' : '' ?>"
                        data-setting-id="<?= (int)$s['setting_id'] ?>"
                        title="Upvote">&#9650;</button>
                <span class="vote-score"><?= (int)$s['vote_score'] ?></span>
                <button class="vote-btn vote-down<?= $userVote === -1 ? ' vote-active' : '' ?>"
                        data-setting-id="<?= (int)$s['setting_id'] ?>"
                        title="Downvote">&#9660;</button>
                <!--
                    Edit button: clicking it fires the .edit-setting-btn handler in
                    tune_page.js, which GETs edit_setting.php?setting_id=N and injects
                    the returned HTML form into .setting-edit-area below.
                    Clicking it again while the form is open closes the form and
                    restores the original notation.
                -->
                <button class="edit-setting-btn"
                        data-setting-id="<?= (int)$s['setting_id'] ?>"
                        title="Edit this setting">Edit</button>
            </div>

            <!-- ── Inline edit area ──────────────────────────────────────── -->
            <!--
                .setting-edit-area starts hidden and empty.
                When the Edit button is clicked, tune_page.js fetches the form from
                edit_setting.php and injects it here. The form contains:
                  - text inputs for tune_name, time_signature, key_signature,
                    default_note_length
                  - a <select id="tune_mode_input"> for the mode
                  - a <select id="key"> for the key note
                  - a <textarea id="abc" class="edit-setting-abc"> for the ABC body

                ━━━ THIS IS WHERE NOTE PLAYBACK HAPPENS ━━━
                As the user types in the <textarea id="abc" class="edit-setting-abc">,
                abc_playback.js fires its keypress handler (delegated on document so it
                works with this dynamically-injected textarea). On each keystroke it:
                  1. Reads the caret position and the several characters around it
                     (findSurroundingChars) to understand context.
                  2. Reads the current key from #key.val() so it can apply the correct
                     key-signature accidentals automatically.
                  3. Calls determinePlayback(key, keyPress) which looks up the key in
                     sharpKeysArray / flatKeysArray, builds a list of sharps or flats
                     that should be in effect, and returns the ABC note string to play
                     (e.g. "^F" if F is sharp in that key).
                  4. Calls $(this).play(noteString) via jquery-turtle.js to emit the
                     note through the browser's audio system.
                  Special cases handled before step 3:
                    - Typing ^ _ = (accidental prefix) plays the accidental + the
                      note that follows it in the textarea (look-ahead).
                    - Typing , or ' (octave modifiers) rebuilds the full note with
                      its accidental prefix and octave marker before playing it.
                    - Typing a letter immediately after ^ _ plays accidental + note.

                Each field change (input / change) also triggers renderFromForm()
                in tune_page.js, which assembles the current form values into a
                complete ABC string and calls ABCJS.renderAbc() so the sheet-music
                notation updates live as the user edits.
            -->
            <div class="setting-edit-area" style="display:none"></div>

            <!-- ── Per-setting notes ─────────────────────────────────────── -->
            <!--
                setting_notes is a free-text annotation specific to this setting
                (e.g. "Originally in Dm", "Slow air version").
                Unlike tune-level notes, these are always fully visible (no toggle).
            -->
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
