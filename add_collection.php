<?php

include_once('functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once('connect.php');
    $pdo = connect();
    $author         = trim($_POST['author'] ?? '');
    $collectionName = trim($_POST['collection_name']);
    $description    = trim($_POST['description'] ?? '');
    $fileUploaded = isset($_FILES['abc_file']) && $_FILES['abc_file']['error'] !== UPLOAD_ERR_NO_FILE;
    if ($fileUploaded) {
        if ($_FILES['abc_file']['error'] !== UPLOAD_ERR_OK) {
            $error = "File upload failed (error code " . $_FILES['abc_file']['error'] . ").";
        } elseif ($_FILES['abc_file']['size'] === 0) {
            $error = "The uploaded file is empty.";
        } else {
            $abcText = file_get_contents($_FILES['abc_file']['tmp_name']);
            if ($abcText === false) {
                $error = "Failed to read the uploaded file.";
            }
        }
    } else {
        $abcText = trim($_POST['abc_text'] ?? '');
        if (empty($abcText)) {
            $error = "Please upload an ABC file or paste ABC notation.";
        }
    }
    if (empty($error)) {
    $abcText        = str_replace("\r\n", "\n", $abcText);
    $abcText        = str_replace("\r", "\n", $abcText);
    $userId         = $_SESSION['user_id'];
    $annotationNotes = [];

    
    if (!empty($_POST['parse_annotations'])) {
        // Build a text containing ONLY annotation sections:
        //   1. Text before the first X: block
        //   2. The annotation "tail" of each X: block (text after the abc body ends)
        //
        // This prevents the regex from matching S:, B:, Z:, N: header fields
        // or body lines that use '.' for staccato notation.
        $segments = preg_split('/(?=^\s*X:\s*\d+)/m', $abcText, -1, PREG_SPLIT_NO_EMPTY);
        $nonAbcParts = [];

        foreach ($segments as $seg) {
            if (!preg_match('/^\s*X:\s*\d+/m', $seg)) {
                // Pre-X: content (annotations before the very first tune)
                $nonAbcParts[] = $seg;
            } else {
                // X: block — keep only the annotation tail after the abc body ends.
                // The body ends when a line starts with an H-Y letter (English word,
                // not a note — same heuristic used by the body parser below).
                $segLines = explode("\n", $seg);
                $inBody   = false;
                $tail     = [];

                foreach ($segLines as $line) {
                    if (preg_match('/^K:/i', trim($line))) {
                        $inBody = true;
                        continue;
                    }
                    if ($inBody) {
                        $t = trim($line);
                        if ($t === '') continue;
                        if (preg_match('/^[A-GZ]*[H-Y]/', $t)) {
                            $inBody = false;   // body has ended
                            $tail[] = $line;   // first annotation line
                        }
                        // else: abc body line — skip
                    } elseif (!empty($tail)) {
                        $tail[] = $line;       // subsequent annotation lines
                    }
                    // before K: — header lines, skip
                }

                if (!empty($tail)) {
                    $nonAbcParts[] = implode("\n", $tail);
                }
            }
        }

        $nonAbcText = implode("\n", $nonAbcParts);

        // Title: uppercase first char + anything up to first period on the line.
        // Notes: everything until the next annotation title (2+ consecutive uppercase
        //        letters at line start + period on same line) or end of text.
        preg_match_all('/^([A-Z][^\n.]+)\.\s*(.*?)(?=^[A-Z]{2,}[^\n]*\.\s|\z)/ms',
            $nonAbcText, $annotationMatches, PREG_SET_ORDER);

        foreach ($annotationMatches as $match) {
            $rawTitle = trim($match[1]);
            $notes    = trim($match[2]);
            if (empty($rawTitle)) continue;
            // Strip parenthetical subtitles e.g. "(An Cnota Bán)" and bracketed numbers e.g. "[1]"
            $keyBase = preg_replace('/\([^)]*\)/', '', $rawTitle);
            $keyBase = preg_replace('/\[\d+\]/', '', $keyBase);
            // Extract only the ALL-CAPS portion (the tune name), stopping before any
            // lowercase description e.g. "WHICH WAY DID SHE GO?  Irish, Slow Air (3/4 time)"
            // → use "WHICH WAY DID SHE GO?" only, not the trailing description
            if (preg_match('/^([^a-z]+)/', $keyBase, $capsMatch)) {
                $capsTitle = $capsMatch[1];
            } else {
                $capsTitle = $keyBase;
            }
            $normalised = strtoupper(preg_replace('/[^A-Z0-9\s]/i', '', $capsTitle));
            $words = preg_split('/\s+/', trim($normalised));
            sort($words);
            $sortedKey = implode(' ', array_filter($words));
            if (empty($sortedKey)) continue;
            // Don't overwrite — keep first entry ([1] variant is usually more complete than [2])
            if (!isset($annotationNotes[$sortedKey])) {
                $annotationNotes[$sortedKey] = [
                    'title' => ucwords(strtolower($rawTitle)),
                    'notes' => $notes
                ];
            }
        }

        // Reduce abcText to only the ABC blocks for subsequent parsing
        preg_match_all('/^X:.*?(?=^X:|\z)/ms', $abcText, $abcBlocks);
        $abcText = trim(implode("\n", $abcBlocks[0]));
    }

    file_put_contents('C:/xampp/htdocs/tuneopedia/debug_annotated_abc.txt', "ANNOTATIONS:\n" . print_r($annotationNotes, true));


    //--------------------------------------------------------------------------
    // LOAD TUNE TYPES FROM DB ONCE
    //--------------------------------------------------------------------------
    $stmt = $pdo->query("SELECT tune_type_id, name FROM tune_type");
    $tuneTypes = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $tuneTypes[strtolower($row['name'])] = (int)$row['tune_type_id'];
    }

    //--------------------------------------------------------------------------
    // PARSE ABC TEXT INTO INDIVIDUAL TUNES
    //--------------------------------------------------------------------------
    $rawTunes = preg_split('/(?=^\s*X:\s*\d+)/m', $abcText, -1, PREG_SPLIT_NO_EMPTY);

    if (empty($rawTunes)) {
        $error = "No tunes found in the ABC text. Make sure each tune starts with an X: field.";
    } else {

        $stmt = $pdo->prepare("SELECT collection_id FROM collection WHERE name = :name LIMIT 1");
        $stmt->execute([':name' => $collectionName]);
        if ($stmt->fetch()) {
            $error = "A collection named \"" . htmlspecialchars($collectionName) . "\" already exists.";
        } else {

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

                $tuneName            = '';
                $keySignature        = '';
                $timeSignature       = '4/4';
                $default_note_length = '1/8';
                $lines               = explode("\n", $rawTune);
                $bodyLines           = [];
                $fieldNotes          = [];
                $inBody              = false;

                // ── Extract headers ───────────────────────────────────────────
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (preg_match('/^T:\s*(.+)/', $line, $m)) {
                        if (empty($tuneName)) $tuneName = trim($m[1]);
                    } elseif (preg_match('/^M:\s*(.+)/', $line, $m)) {
                        $timeSignature = trim($m[1]);
                    } elseif (preg_match('/^L:\s*(.+)/', $line, $m)) {
                        $default_note_length = trim($m[1]);
                    } elseif (preg_match('/^K:\s*(.+)/', $line, $m)) {
                        $keySignature = trim($m[1]);
                        $inBody = true;
                    } elseif (preg_match('/^[ZSN]:\s*(.+)/', $line, $m)) {
                        $fieldNotes[] = trim($m[1]);
                    } elseif ($inBody) {
                        if (empty($line)) continue;
                        // An annotation title is two or more ALL-CAPS words — stop here
                        // ABC notes only use A-G (and Z for rests); a word with any H-Y letter is a real word
                        if (preg_match('/^[A-GZ]*[H-Y]/', $line)) break;
                        $bodyLines[] = $line;
                    }
                }

                // ── Build ABC body ────────────────────────────────────────────
                $abcBody = implode("\n", $bodyLines);
                if (!empty($_POST['normalize_abc'])) {
                    $abcBody = formatAbcBody($abcBody, $timeSignature, $default_note_length, $tuneName);
                }

                // ── Annotation lookup ─────────────────────────────────────────
                $tuneNotes = '';
                if (!empty($_POST['parse_annotations']) && !empty($tuneName)) {
                    $tuneKeyBase = preg_replace('/\([^)]*\)/', '', $tuneName);
                    $tuneKeyBase = preg_replace('/\[\d+\]/', '', $tuneKeyBase);
                    $normalisedTuneName = strtoupper(preg_replace('/[^A-Z0-9\s]/i', '', $tuneKeyBase));
                    $tuneWords = preg_split('/\s+/', trim($normalisedTuneName));
                    sort($tuneWords);
                    $sortedTuneName = implode(' ', array_filter($tuneWords));
                    if (isset($annotationNotes[$sortedTuneName])) {
                        $tuneNotes = $annotationNotes[$sortedTuneName]['notes'];
                        unset($annotationNotes[$sortedTuneName]);
                    }
                }

                // ── Tune type detection ───────────────────────────────────────
                $tuneTypeName = '';
                foreach ($lines as $line) {
                    if (preg_match('/^R:\s*(.+)/i', trim($line), $m)) {
                        $tuneTypeName = strtolower(trim($m[1]));
                        break;
                    }
                }

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
                    $stmt = $pdo->prepare("SELECT tune_type_id FROM tune_type WHERE LOWER(name) = :name LIMIT 1");
                    $stmt->execute([':name' => $tuneTypeName]);
                    $tuneTypeId = (int)$stmt->fetchColumn();
                    $tuneTypes[$tuneTypeName] = $tuneTypeId;
                } else {
                    $tuneTypeId = $tuneTypes[$tuneTypeName];
                }

                if (empty($tuneTypeId)) {
                    $tuneTypeId = $tuneTypes['other'] ?? 1;
                }

                if (empty($tuneName)) {
                    $results[] = ['status' => 'skipped', 'reason' => 'No T: field found', 'tune' => $rawTune];
                    continue;
                }

                // ── Check if tune exists ──────────────────────────────────────
                $stmt = $pdo->prepare("SELECT tune_id FROM tune WHERE name = :name LIMIT 1");
                $stmt->execute([':name' => $tuneName]);
                $existingTune = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingTune) {
                    $tuneId = $existingTune['tune_id'];
                    $stmt = $pdo->prepare("
                        INSERT INTO setting (tune_id, user_id, name, default_note_length, time_signature, key_signature, abc_transcription)
                        VALUES (:tune_id, :user_id, :name, :default_note_length, :time_signature, :key_signature, :abc_transcription)
                    ");
                    $stmt->execute([
                        ':tune_id'             => $tuneId,
                        ':user_id'             => $userId,
                        ':name'                => $collectionName,
                        ':default_note_length' => $default_note_length,
                        ':time_signature'      => $timeSignature,
                        ':key_signature'       => $keySignature,
                        ':abc_transcription'   => $abcBody
                    ]);
                    $settingId = $pdo->lastInsertId();
                    $results[] = ['status' => 'existing_tune', 'tune' => $tuneName, 'tune_id' => $tuneId, 'setting_id' => $settingId];

                } else {
                    $stmt = $pdo->prepare("INSERT INTO tune (name, tune_type_id) VALUES (:name, :tune_type_id)");
                    $stmt->execute([
                        ':name'         => $tuneName,
                        ':tune_type_id' => $tuneTypeId
                    ]);
                    $tuneId = $pdo->lastInsertId();

                    $stmt = $pdo->prepare("
                        INSERT INTO setting (tune_id, user_id, name, default_note_length, time_signature, key_signature, abc_transcription)
                        VALUES (:tune_id, :user_id, :name, :default_note_length, :time_signature, :key_signature, :abc_transcription)
                    ");
                    $stmt->execute([
                        ':tune_id'             => $tuneId,
                        ':user_id'             => $userId,
                        ':name'                => $tuneName,
                        ':default_note_length' => $default_note_length,
                        ':time_signature'      => $timeSignature,
                        ':key_signature'       => $keySignature,
                        ':abc_transcription'   => $abcBody
                    ]);
                    $settingId = $pdo->lastInsertId();
                    $results[] = ['status' => 'inserted', 'tune' => $tuneName, 'tune_id' => $tuneId, 'setting_id' => $settingId];
                }

                // ── Save notes ────────────────────────────────────────────────
                $noteStmt = $pdo->prepare("INSERT INTO tune_note (tune_id, note) VALUES (:tune_id, :note)");
                if (!empty($tuneNotes)) {
                    // Split annotation into one row per paragraph, skip separators like ***
                    $paragraphs = preg_split('/\n{2,}/', trim($tuneNotes));
                    foreach ($paragraphs as $para) {
                        $para = trim($para);
                        if ($para === '' || preg_match('/^\*+$/', $para)) continue;
                        $noteStmt->execute([':tune_id' => $tuneId, ':note' => $para]);
                    }
                }
                foreach ($fieldNotes as $fn) {
                    if ($fn !== '') {
                        $noteStmt->execute([':tune_id' => $tuneId, ':note' => $fn]);
                    }
                }

                // ── Link to collection ────────────────────────────────────────
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

            // ── Insert tunes with no notation ─────────────────────────────────
            if (!empty($_POST['parse_annotations'])) {
                foreach ($annotationNotes as $normalised => $annotation) {
                    $stmt = $pdo->prepare("INSERT INTO tune (name, tune_type_id) VALUES (:name, :tune_type_id)");
                    $stmt->execute([
                        ':name'         => $annotation['title'],
                        ':tune_type_id' => $tuneTypes['other'] ?? 1
                    ]);
                    $tuneId = $pdo->lastInsertId();

                    if (!empty($annotation['notes'])) {
                        $noteStmt2 = $pdo->prepare("INSERT INTO tune_note (tune_id, note) VALUES (:tune_id, :note)");
                        $paragraphs = preg_split('/\n{2,}/', trim($annotation['notes']));
                        foreach ($paragraphs as $para) {
                            $para = trim($para);
                            if ($para === '' || preg_match('/^\*+$/', $para)) continue;
                            $noteStmt2->execute([':tune_id' => $tuneId, ':note' => $para]);
                        }
                    }

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
                    $results[] = ['status' => 'inserted', 'tune' => $annotation['title']];
                }
            }

            $success = true;
        }
    }
    } // end if (empty($error))
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