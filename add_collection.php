<!--CSS-->
<link href="css/style.css" rel="stylesheet" type="text/css"/>

<?php

// DETECT ANACRUSIS — count note units in first bar
function countBeats($content) {
    $beats = 0;
    // Match notes: optional accidental, note letter, optional octave, optional length
    preg_match_all('/[a-gA-GzZ][,\']*(\d*)(\/?(\d*))/', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $note) {
        $num   = $note[1] !== '' ? (int)$note[1] : 1;
        $slash = $note[2];
        if ($slash === '/') {
            $denom = 2;
        } elseif (preg_match('/\/(\d+)/', $slash, $dm)) {
            $denom = (int)$dm[1];
        } else {
            $denom = 1;
        }
        $beats += $num / $denom;
    }
    return $beats;
}

function formatAbcBody($abcBody, $timeSignature) {
    // PARSE BEATS PER MEASURE FROM TIME SIGNATURE
    // Using eighth notes as the unit (L:1/8 is assumed)\
    // CLEAN UP BEFORE PROCESSING
    // Remove blank lines and normalize
    $abcBody = preg_replace('/\n\s*\n/', "\n", $abcBody);
    $abcBody = preg_replace('/\|\\\\\n/', "|", $abcBody); // strip |\ continuations
    $abcBody = preg_replace('/\\\\\n/', " ", $abcBody);   // strip remaining \ continuations
    $abcBody = trim($abcBody);

    $beatsPerMeasure = 8;
    if (preg_match('/^(\d+)\/(\d+)$/', $timeSignature, $m)) {
        $numerator       = (int)$m[1];
        $denominator     = (int)$m[2];
        $beatsPerMeasure = $numerator * (8 / $denominator);
    }

    // SPLIT BODY INTO MEASURES ON | BUT KEEP THE DELIMITERS
    // Handles |, ||, |:, :|, :||:, [|, |] etc.
    //$measures = preg_split('/(\|[\|:\]]?|:\|[\|:]?)/', $abcBody, -1, PREG_SPLIT_DELIM_CAPTURE);
    // SPLIT ON BARLINES — but not repeat colons that are part of |: or :|
    $measures = preg_split('/(\|\||\|:|:\||\[|\]|\|)/', $abcBody, -1, PREG_SPLIT_DELIM_CAPTURE);

    // PAIR EACH MEASURE CONTENT WITH ITS FOLLOWING BARLINE
    // preg_split with DELIM_CAPTURE gives: [content, barline, content, barline, ...]
    $bars = [];
    for ($i = 0; $i < count($measures); $i += 2) {
        $content = $measures[$i];
        $barline = isset($measures[$i + 1]) ? $measures[$i + 1] : '';
        if (trim($content) !== '') {
            $bars[] = ['content' => $content, 'barline' => $barline];
        }
    }

    

    if (empty($bars)) return $abcBody;

    $firstBarBeats = countBeats($bars[0]['content']);
    $isAnacrusis   = ($firstBarBeats < $beatsPerMeasure);

    $output     = '';
    $barCount   = 0;
    $breakAfter = $isAnacrusis ? 5 : 4;

    foreach ($bars as $bar) {
        $output .= $bar['content'];
        $barCount++;

        if (!empty($bar['barline'])) {
            $output .= $bar['barline'];
            if ($barCount === $breakAfter || ($barCount > $breakAfter && ($barCount - $breakAfter) % 4 === 0)) {
                $output .= "\\\n";
            }
        }
    }

    return trim($output);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once('connect.php');
    $pdo = connect();
    $author         = trim($_POST['author'] ?? '');
    $collectionName = trim($_POST['collection_name']);
    $description    = trim($_POST['description'] ?? '');
    $abcText        = trim($_POST['abc_text']);
    $abcText = str_replace("\r\n", "\n", $abcText);
    $abcText = str_replace("\r", "\n", $abcText);
    $userId         = $_SESSION['user_id'];

    //--------------------------------------------------------------------------
    // PARSE ABC TEXT INTO INDIVIDUAL TUNES
    // Each tune starts with an X: field. Split on X: at the start of a line.
    //--------------------------------------------------------------------------
    $rawTunes = preg_split('/(?=^\s*X:\s*\d+)/m', $abcText, -1, PREG_SPLIT_NO_EMPTY);

    if (empty($rawTunes)) {
        $error = "No tunes found in the ABC text. Make sure each tune starts with an X: field.";
    } else {

        // CHECK IF COLLECTION ALREADY EXISTS
        $stmt = $pdo->prepare("SELECT collection_id FROM collection WHERE name = :name LIMIT 1");
        $stmt->execute([':name' => $collectionName]);
        if ($stmt->fetch()) {
            $error = "A collection named \"" . htmlspecialchars($collectionName) . "\" already exists.";
        } else {
            //----------------------------------------------------------------------
            // INSERT COLLECTION
            //----------------------------------------------------------------------
            $stmt = $pdo->prepare("
                INSERT INTO collection (name, author, description, created_at)
                VALUES (:name, :author, :description, NOW())
            ");
            $stmt->execute([
                ':name'        => $collectionName,
                ':author'      => $author,
                ':description' => $description
            ]);
            $collectionId = $pdo->lastInsertId();

            $position = 1;
            $results  = [];

            foreach ($rawTunes as $rawTune) {

                $rawTune = trim($rawTune);
                if (empty($rawTune)) continue;

                //------------------------------------------------------------------
                // EXTRACT FIELDS FROM ABC BLOCK
                //------------------------------------------------------------------
                $tuneName      = '';
                $keySignature  = '';
                $timeSignature = '4/4';

                $lines     = explode("\n", $rawTune);
                $bodyLines = [];
                $inBody    = false;

                foreach ($lines as $line) {
                    $line = trim($line);
                
                    if (preg_match('/^T:\s*(.+)/', $line, $m)) {
                        if (empty($tuneName)) $tuneName = trim($m[1]);
                    } elseif (preg_match('/^M:\s*(.+)/', $line, $m)) {
                        $timeSignature = trim($m[1]);
                    } elseif (preg_match('/^K:\s*(.+)/', $line, $m)) {
                        $keySignature = trim($m[1]);
                        $inBody = true;
                    } elseif ($inBody && !empty($line)) {
                        $bodyLines[] = $line;
                    }
                }

                $abcBody = implode("\n", $bodyLines);
                //$abcBody = str_replace("\\\n", "", $abcBody); // remove ABC line continuations
                $abcBody = formatAbcBody($abcBody, $timeSignature);
                // TEMPORARY DEBUG - remove after testing
                file_put_contents('C:/xampp/htdocs/tuneopedia/debug_abc.txt', $abcBody);

                // EXTRACT R: FIELD IF PRESENT
                $tuneTypeId = 6; // default to Other
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (preg_match('/^R:\s*(.+)/i', $line, $m)) {
                        $r = strtolower(trim($m[1]));
                        if (str_contains($r, 'strathspey'))                            $tuneTypeId = 8;
                        elseif (str_contains($r, 'slip') && str_contains($r, 'jig'))  $tuneTypeId = 5;
                        elseif (str_contains($r, 'reel'))                              $tuneTypeId = 1;
                        elseif (str_contains($r, 'jig'))                               $tuneTypeId = 2;
                        elseif (str_contains($r, 'polka'))                             $tuneTypeId = 3;
                        elseif (str_contains($r, 'hornpipe'))                          $tuneTypeId = 4;
                        elseif (str_contains($r, 'waltz'))                             $tuneTypeId = 7;
                        break;
                    }
                }

                // IF NO R: FIELD, GUESS FROM TIME SIGNATURE
                if ($tuneTypeId === 6) {
                    switch ($timeSignature) {
                        case '4/4':  $tuneTypeId = 1; break; // Reel
                        case '6/8':  $tuneTypeId = 2; break; // Jig
                        case '3/4':  $tuneTypeId = 7; break; // Waltz
                        case '12/8': $tuneTypeId = 4; break; // Hornpipe
                        case '9/8':  $tuneTypeId = 5; break; // Slip Jig
                        case '2/4':  $tuneTypeId = 3; break; // Polka
                        default:     $tuneTypeId = 6; break; // Other
                    }
                }

                if (empty($tuneName)) {
                    $results[] = ['status' => 'skipped', 'reason' => 'No T: field found', 'tune' => $rawTune];
                    continue;
                }

                //------------------------------------------------------------------
                // CHECK IF TUNE ALREADY EXISTS BY NAME
                //------------------------------------------------------------------
                $stmt = $pdo->prepare("
                    SELECT tune_id FROM tune WHERE name = :name LIMIT 1
                ");
                $stmt->execute([':name' => $tuneName]);
                $existingTune = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingTune) {
                    //--------------------------------------------------------------
                    // TUNE EXISTS — add a new setting named after the collection
                    //--------------------------------------------------------------
                    $tuneId = $existingTune['tune_id'];

                    $stmt = $pdo->prepare("
                        INSERT INTO setting (tune_id, user_id, name, time_signature, key_signature, abc_transcription)
                        VALUES (:tune_id, :user_id, :name, :time_signature, :key_signature, :abc_transcription)
                    ");
                    $stmt->execute([
                        ':tune_id'           => $tuneId,
                        ':user_id'           => $userId,
                        ':name'              => $collectionName,
                        ':time_signature'    => $timeSignature,
                        ':key_signature'     => $keySignature,
                        ':abc_transcription' => $abcBody
                    ]);
                    $settingId = $pdo->lastInsertId();

                    $results[] = ['status' => 'existing_tune', 'tune' => $tuneName, 'tune_id' => $tuneId, 'setting_id' => $settingId];

                } else {
                    //--------------------------------------------------------------
                    // NEW TUNE — insert into tune then setting
                    //--------------------------------------------------------------
                    $stmt = $pdo->prepare("
                        INSERT INTO tune (name, tune_type_id) VALUES (:name, :tune_type_id)
                    ");
                    $stmt->execute([
                        ':name'         => $tuneName,
                        ':tune_type_id' => $tuneTypeId
                    ]);
                    $tuneId = $pdo->lastInsertId();

                    $stmt = $pdo->prepare("
                        INSERT INTO setting (tune_id, user_id, name, time_signature, key_signature, abc_transcription)
                        VALUES (:tune_id, :user_id, :name, :time_signature, :key_signature, :abc_transcription)
                    ");
                    $stmt->execute([
                        ':tune_id'           => $tuneId,
                        ':user_id'           => $userId,
                        ':name'              => $tuneName,
                        ':time_signature'    => $timeSignature,
                        ':key_signature'     => $keySignature,
                        ':abc_transcription' => $abcBody
                    ]);
                    $settingId = $pdo->lastInsertId();

                    $results[] = ['status' => 'inserted', 'tune' => $tuneName, 'tune_id' => $tuneId, 'setting_id' => $settingId];
                }

                //------------------------------------------------------------------
                // LINK TUNE TO COLLECTION
                //------------------------------------------------------------------
                $stmt = $pdo->prepare("
                    INSERT INTO collection_tune (collection_id, tune_id, position)
                    VALUES (:collection_id, :tune_id, :position)
                ");
                $stmt->execute([
                    ':collection_id' => $collectionId,
                    ':tune_id'       => $tuneId,
                    ':position'      => $position
                ]);

                $position++;
            }

            $success = true;
        }
    }
}

?>

<!--RESULTS-->
<div id="add-collection-wrapper">

    <h2>Add Collection from ABC</h2>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <button onclick="history.back()">Go Back</button>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success-message">
            <p>Collection <strong><?= htmlspecialchars($collectionName) ?></strong> created with <?= count($results) ?> tune(s).</p>
            <table class="collection-results-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tune</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($r['tune']) ?></td>
                        <td>
                            <?php if ($r['status'] === 'inserted'): ?>
                                New tune added
                            <?php elseif ($r['status'] === 'existing_tune'): ?>
                                Existing tune — new setting added
                            <?php else: ?>
                                Skipped: <?= htmlspecialchars($r['reason']) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>