<!--CSS-->
<link href="css/style.css" rel="stylesheet" type="text/css"/>

<?php

//$debug_tune_name = '';

// DETECT ANACRUSIS — count note units in first bar
function countBeats($content, $unitLength = '1/8') {
    // How many eighth notes is one L: unit worth?
    $multiplier = 1; // default L:1/8 = 1 eighth note
    if (preg_match('/(\d+)\/(\d+)/', $unitLength, $m)) {
        $multiplier = ((int)$m[1] / (int)$m[2]) / (1/8);
        // L:1/4 -> (1/4) / (1/8) = 2  (one quarter note = 2 eighth notes)
        // L:1/8 -> (1/8) / (1/8) = 1
    }

    $content = preg_replace('/\(\d+/', '', $content);   // strip tuplet markers
    $content = preg_replace('/\{[^}]*\}/', '', $content); // grace notes
    $content = preg_replace('/\[[^\]]*\]/', '', $content); // chords
    $content = preg_replace('/[!+~HLMOPSTuv]/', '', $content); // decorations

    preg_match_all('/[a-gA-GzZ][,\']*(\d*)(\/?(\d*))/', $content, $matches, PREG_SET_ORDER);
    $beats = 0;
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
        $beats += ($num / $denom) * $multiplier;
    }
    return $beats;
}

function formatAbcBody($abcBody, $timeSignature, $unitLength = '1/8', $debug_tune_name) {
    // Clean up input
    $abcBody = preg_replace('/\|\\\\\n/', '|', $abcBody);  // strip |\ continuations
    $abcBody = preg_replace('/\\\\\n/', ' ', $abcBody);     // strip \ continuations
    $abcBody = preg_replace('/\n\s*\n/', "\n", $abcBody);  // remove blank lines
    $abcBody = trim($abcBody);

    // Normalise all ending markers to [1 and [2
    $abcBody = str_replace('|1', '|[1', $abcBody);
    $abcBody = str_replace('|2', '|[2', $abcBody);

    // Beats per measure (in eighth notes)
    // beatsPerMeasure in eighth notes — do NOT scale by unit length
    $beatsPerMeasure = 8;
    if (preg_match('/^(\d+)\/(\d+)$/', $timeSignature, $m)) {
        $beatsPerMeasure = (int)$m[1] * (8 / (int)$m[2]);
        // 3/4 = 3 * (8/4) = 6 eighth notes
        // 4/4 = 4 * (8/4) = 8 eighth notes
        // 6/8 = 6 * (8/8) = 6 eighth notes
    }

    // Protect [1 and [2 so they don't get eaten by the splitter
    $abcBody = str_replace('[2', 'SECONDENDING', $abcBody);
    $abcBody = str_replace('[1', 'FIRSTENDING', $abcBody);

    // Split on barlines, keeping the delimiters
    $parts = preg_split('/(\|\||:\|:|\|2|\|1|::|:\||\|\]|\|:|\|)/', $abcBody, -1, PREG_SPLIT_DELIM_CAPTURE);

    // Pair content with its following barline, restoring ending markers
    $bars = [];
    for ($i = 0; $i < count($parts); $i += 2) {
        $content = trim($parts[$i]);
        $barline = isset($parts[$i + 1]) ? $parts[$i + 1] : '';
        $content = str_replace('SECONDENDING', '[2', $content);
        $content = str_replace('FIRSTENDING', '[1', $content);
        if ($content !== '') {
            $bars[] = ['content' => $content, 'barline' => $barline];
        }
    }



    /*if ($debug_tune_name === 'Dudley Street') {
        foreach ($bars as $i => $bar) {
            file_put_contents('C:/xampp/htdocs/tuneopedia/debug_abc_2.txt',
                "Bar $i: content=[" . $bar['content'] . "] barline=[" . $bar['barline'] . "]\n",
                FILE_APPEND
            );
        }
    }*/

    /*file_put_contents('C:/xampp/htdocs/tuneopedia/debug_abc.txt', 
        "Tune: " . $debug_tune_name . "\n" .
        "First bar content: [" . ($bars[0]['content'] ?? 'EMPTY') . "]\n" .
        "First bar beats: " . countBeats($bars[0]['content'] ?? '', $unitLength) . "\n" .
        "Beats per measure: " . $beatsPerMeasure . "\n" .
        "Unit length: " . $unitLength . "\n\n",
        FILE_APPEND
    );*/

    if (empty($bars)) return $abcBody;

    // Detect anacrusis
    $firstBarBeats = countBeats($bars[0]['content'], $unitLength);
    $isAnacrusis   = ($firstBarBeats < $beatsPerMeasure * 0.75);

    $lines       = [];
    $currentLine = '';
    $barCount    = 0;

    $repeatBarlines = [':|:', '::', ':|', '|:'];
    $firstEndingBarlines = ['|1', '[1'];
    $secondEndingBarlines = ['[2', '|2'];
    
    $lines        = [];
    $currentLine  = '';
    $barCount     = 0;
    $inFirstEnding = false;
    $secondEndingLength = 0;
    
    // Pre-calculate second ending lengths
    // Find each [2 or |2 and count bars until the next || or |] or :|
    $secondEndingSizes = [];
    for ($i = 0; $i < count($bars); $i++) {
        if (in_array(trim($bars[$i]['barline']), $firstEndingBarlines) || str_starts_with($bars[$i]['content'], '[1')) {
            // Look ahead to find the second ending
            $size = 0;
            for ($j = $i + 1; $j < count($bars); $j++) {
                //if (in_array(trim($bars[$j]['barline']), $secondEndingBarlines) || str_starts_with($bars[$j]['content'], '[2')) {
                if (in_array(trim($bars[$j]['barline']), $secondEndingBarlines) || str_starts_with($bars[$j]['content'], '[2')) {    
                    // Now count bars in second ending — start from j (the second ending bar itself)
                    for ($k = $j; $k < count($bars); $k++) {
                        $size++;
                        $bl = trim($bars[$k]['barline']);
                        if (in_array($bl, ['||', '|]', ':|', ':|:']) || $bl === '') break;
                    }
                    break;
                }
            }
            $secondEndingSizes[$i] = $size;
        }
    }
    
    // DEBUG
    file_put_contents('C:/xampp/htdocs/tuneopedia/debug_abc.txt',
        "Tune: $debug_tune_name secondEndingSizes: " . print_r($secondEndingSizes, true) . "\n",
        FILE_APPEND
    );

    foreach ($bars as $index => $bar) {
        $nextBar   = isset($bars[$index + 1]) ? $bars[$index + 1] : null;
        $thisBeats = countBeats($bar['content'], $unitLength);
        $nextBeats = $nextBar ? countBeats($nextBar['content'], $unitLength) : 0;
    
        $isAnacrusisBar      = ($thisBeats < $beatsPerMeasure * 0.75) && ($nextBeats >= $beatsPerMeasure * 0.75);
        $isRepeatBarline     = in_array(trim($bar['barline']), $repeatBarlines);
        $isFirstEndingStart  = in_array(trim($bar['barline']), ['|1']) || str_starts_with($bar['content'], '[1');
        $isSecondEndingStart = in_array(trim($bar['barline']), ['|2']) || str_starts_with($bar['content'], '[2');
    
        if ($isAnacrusisBar && trim($currentLine) !== '') {
            $lines[]     = trim($currentLine);
            $currentLine = '';
            $barCount    = 0;
        }

        
    
        $currentLine .= $bar['content'];
    
        if (!empty($bar['barline'])) {
            $currentLine .= $bar['barline'];
    
            if (!$isAnacrusisBar && !$isSecondEndingStart) {
                $barCount++;
            }
    
            if ($isFirstEndingStart) {
                // Look up how long the second ending is
                $secondEndingLength = $secondEndingSizes[$index] ?? 0;
                $inFirstEnding = true;
            }
    
            if ($isRepeatBarline && $inFirstEnding) {
                // Don't break here if second ending fits on this line
                if ($secondEndingLength >= 3) {
                    // Second ending too long — break here, it gets its own line
                    $lines[]     = trim($currentLine);
                    $currentLine = '';
                    $barCount    = 0;
                    $inFirstEnding = false;
                }
                // Otherwise don't break — let second ending bars append
            } elseif ($isRepeatBarline || $barCount === 4) {
                $lines[]     = trim($currentLine);
                $currentLine = '';
                $barCount    = 0;
                $inFirstEnding = false;
            }
    
            // After second ending ends, always break
            //$bl = trim($bar['barline']);
            //if ($inFirstEnding && in_array($bl, ['||', '|]', ':|:']) && !$isFirstEndingStart) {
            // After second ending ends, always break
            $bl = trim($bar['barline']);
            if ($inFirstEnding && in_array($bl, ['||', '|]', ':|:', ':|']) && ($isSecondEndingStart || !$isFirstEndingStart)) {
                $lines[]     = trim($currentLine);
                $currentLine = '';
                $barCount    = 0;
                $inFirstEnding = false;
            }
        }
    }
    
    if (trim($currentLine) !== '') {
        $lines[] = trim($currentLine);
    }
    
    return implode("\n", $lines);
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
    $abcText        = str_replace("\r\n", "\n", $abcText);
    $abcText        = str_replace("\r", "\n", $abcText);
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
                $unitLength    = '1/8'; // add this
                
                foreach ($lines as $line) {
                    $line = trim($line);
                
                    if (preg_match('/^T:\s*(.+)/', $line, $m)) {
                        if (empty($tuneName)) {
                            $tuneName = trim($m[1]);
                        }
                    } elseif (preg_match('/^M:\s*(.+)/', $line, $m)) {
                        $timeSignature = trim($m[1]);
                    } elseif (preg_match('/^L:\s*(.+)/', $line, $m)) {
                        $unitLength = trim($m[1]);
                    
                    } elseif (preg_match('/^K:\s*(.+)/', $line, $m)) {
                        $keySignature = trim($m[1]);
                        $inBody = true;
                    } elseif ($inBody && !empty($line)) {
                        $bodyLines[] = $line;
                    }
                }

                $abcBody = implode("\n", $bodyLines);
                if (!empty($_POST['normalize_abc'])) {
                    $abcBody = formatAbcBody($abcBody, $timeSignature, $unitLength, $tuneName);
                }
                
                //$abcBody = str_replace("\\\n", "", $abcBody); // remove ABC line continuations
                //$abcBody = formatAbcBody($abcBody, $timeSignature, $unitLength, $tuneName);
                // TEMPORARY DEBUG - remove after testing
                //file_put_contents('C:/xampp/htdocs/tuneopedia/debug_abc.txt', $abcBody);


                // Load all tune types from DB [lowercase name => id]
                $stmt = $pdo->query("SELECT tune_type_id, name FROM tune_type");
                $tuneTypes = [];
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    $tuneTypes[strtolower($row['name'])] = (int)$row['tune_type_id'];

                     // ── Step 1: Check R: field ────────────────────────────────────────────
                    $tuneTypeName = '';
                    foreach ($lines as $line) {
                        if (preg_match('/^R:\s*(.+)/i', trim($line), $m)) {
                            $tuneTypeName = strtolower(trim($m[1]));
                            break;
                        }
                    }

                    // ── Step 2: If no R: field, scan all headers for keywords ─────────────
                    if (empty($tuneTypeName)) {
                        $keywords = ['strathspey', 'slip jig', 'hornpipe', 'march', 'reel', 'jig', 'polka', 'waltz'];
                        foreach ($lines as $line) {
                            $lower = strtolower($line);
                            foreach ($keywords as $keyword) {
                                if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/', $lower)) {
                                    $tuneTypeName = $keyword;
                                    break 2;
                                }
                            }
                        }
                    }

                    // ── Step 3: Fall back to time signature ───────────────────────────────
                    if (empty($tuneTypeName)) {
                        $timeSigMap = [
                            '4/4'  => 'reel',
                            '6/8'  => 'jig',
                            '9/8'  => 'slip jig',
                            '12/8' => 'hornpipe',
                            '3/4'  => 'waltz',
                            '2/4'  => 'other',
                        ];
                        $tuneTypeName = $timeSigMap[$timeSignature] ?? 'other';
                    }

                    if (!isset($tuneTypes[$tuneTypeName])) {
                        $stmt = $pdo->prepare("INSERT IGNORE INTO tune_type (name) VALUES (:name)");
                        $stmt->execute([':name' => ucfirst($tuneTypeName)]);
                        
                        // Whether we inserted or it already existed, fetch the ID
                        $stmt = $pdo->prepare("SELECT tune_type_id FROM tune_type WHERE LOWER(name) = :name LIMIT 1");
                        $stmt->execute([':name' => $tuneTypeName]);
                        $tuneTypeId = (int)$stmt->fetchColumn();
                        $tuneTypes[$tuneTypeName] = $tuneTypeId;
                    } else {
                        $tuneTypeId = $tuneTypes[$tuneTypeName];
                    }


                }

                if (empty($tuneTypeId)) {
                    $tuneTypeId = $tuneTypes['other'] ?? 1; // fallback so the tune always gets inserted
                }

                //error_log("Tune: $tuneName | Type name: $tuneTypeName | Type ID: $tuneTypeId");

                /*------TUNE NAME------*/

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