<!--CSS-->
<link href="css/style.css" rel="stylesheet" type="text/css"/>

<?php

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
    $userId         = $_SESSION['user_id'];

    //--------------------------------------------------------------------------
    // PARSE ABC TEXT INTO INDIVIDUAL TUNES
    // Each tune starts with an X: field. Split on X: at the start of a line.
    //--------------------------------------------------------------------------
    $rawTunes = preg_split('/(?=^\s*X:\s*\d+)/m', $abcText, -1, PREG_SPLIT_NO_EMPTY);

    if (empty($rawTunes)) {
        $error = "No tunes found in the ABC text. Make sure each tune starts with an X: field.";
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
                    $inBody = true; // K: is always the last header, body follows
                } elseif ($inBody && !empty($line)) {
                    $bodyLines[] = $line;
                }
            }

            $abcBody = implode("\n", $bodyLines);

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
                    INSERT INTO tune (name) VALUES (:name)
                ");
                $stmt->execute([':name' => $tuneName]);
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

?>

<!--RESULTS-->
<div id="add-collection-wrapper">

    <h2>Add Collection from ABC</h2>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
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