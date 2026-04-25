<?php

include_once(__DIR__ . '/../helpers/tune_helpers.php');
require_once(__DIR__ . '/../models/Collection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    set_time_limit(300);
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once(__DIR__ . '/../config/database.php');
    $pdo = connect();
    if (!$pdo) { die('Database connection failed'); }
    $author         = trim($_POST['author'] ?? '');
    $collectionName = trim($_POST['collection_name']);
    $description    = trim($_POST['description'] ?? '');
    $fileUploaded = !empty($_FILES['abc_files']['name'][0]);
    if ($fileUploaded) {
        $abcText  = '';
        $fileErrors = [];
        foreach ($_FILES['abc_files']['tmp_name'] as $i => $tmpName) {
            $uploadErr = $_FILES['abc_files']['error'][$i];
            $fileName  = htmlspecialchars($_FILES['abc_files']['name'][$i]);
            if ($uploadErr !== UPLOAD_ERR_OK) {
                $fileErrors[] = "{$fileName}: upload error (code {$uploadErr}).";
            } elseif ($_FILES['abc_files']['size'][$i] === 0) {
                $fileErrors[] = "{$fileName} is empty.";
            } else {
                $content = file_get_contents($tmpName);
                if ($content === false) {
                    $fileErrors[] = "Could not read {$fileName}.";
                } else {
                    $abcText .= ($abcText !== '' ? "\n" : '') . $content;
                }
            }
        }
        if (!empty($fileErrors)) {
            $error = implode(' ', $fileErrors);
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
                // The body ends when a line contains an all-caps word with an H-Y
                // letter (outside A-G note range) — same heuristic as the body parser.
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
                        // Detect annotation start anywhere on the line: look for any
                        // all-caps word (2+ letters) containing a letter H-Y (outside
                        // the A-G note range). Handles titles like "A" POLKA and
                        // A' POLKA where the old start-of-line check failed.
                        // Guard against %% comments and lines with | (bar lines).
                        $looksLikeTitle = $t[0] !== '%'
                            && !preg_match('/^[A-Za-z]:/', $t)
                            && strpos($t, '|') === false
                            && (bool) preg_match('/\b(?=[A-Z]{2,}\b)[A-Z]*[H-Y][A-Z]*\b/', $t);
                        if ($looksLikeTitle) {
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

        // Strip separator lines (***..., ......, ──── etc.) and "See also" / "Source for" lines
        $nonAbcText = preg_replace('/^[\s]*[\*\.·\-─]{3,}[\s]*$/m', '', $nonAbcText);
        $nonAbcText = preg_replace('/^[\s]*(?:See also|Source for)\b.*$/mi', '', $nonAbcText);

        // Title is a single line of predominantly uppercase text, ending at
        // punctuation (.?!) followed by whitespace then a description start.
        // [^\n]+? keeps the title on one line. The notes body spans multiple
        // lines until the next title or end of text.
        preg_match_all('/^([^\n]+?)[.?!]\s+(?=[A-Z][a-z]|AKA|\()(.*?)(?=^[^\n]+?[.?!]\s+(?=[A-Z][a-z]|AKA|\()|\z)/ms',
            $nonAbcText, $annotationMatches, PREG_SET_ORDER);

        foreach ($annotationMatches as $match) {
            $rawTitle = trim($match[1]);
            $notes    = trim($match[2]);
            if (empty($rawTitle)) continue;
            // Skip separator lines, non-title matches, and lyrics/citations
            if (preg_match('/^[\*\.·\-\s]+$/', $rawTitle)) continue;
            if (preg_match('/^(See also|Source for)/i', $rawTitle)) continue;
            // Title must be predominantly uppercase (strip parentheticals first
            // since translations like "(Cnoic Aiteannach)" are mixed case)
            $titleForCheck = preg_replace('/\([^)]*\)/', '', $rawTitle);
            $letters = preg_replace('/[^A-Za-z]/', '', $titleForCheck);
            $upper = preg_replace('/[^A-Z]/', '', $letters);
            if (strlen($letters) < 2 || strlen($upper) / strlen($letters) < 0.6) continue;
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

    file_put_contents(__DIR__ . '/../docs/debug_annotated_abc.txt', "ANNOTATIONS:\n" . print_r($annotationNotes, true));


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

        if (Collection::existsByName($pdo, $collectionName)) {
            $error = "A collection named \"" . htmlspecialchars($collectionName) . "\" already exists.";
        } else {

            $isShared     = !empty($_POST['is_shared']);
            $collectionId = Collection::create($pdo, $collectionName, $author, $description, $isShared, $userId);

            $position   = 1;
            $results    = [];
            $lastTuneId = null;
            $lastTuneName = '';

            foreach ($rawTunes as $rawTune) {
                $rawTune = trim($rawTune);
                if (empty($rawTune)) continue;

                $xNumber = 1;
                if (preg_match('/^\s*X:\s*(\d+)/m', $rawTune, $xm)) {
                    $xNumber = (int)$xm[1];
                }
                $isAdditionalSetting = ($xNumber > 1 && $lastTuneId !== null);

                $tuneName            = '';
                $keySignature        = '';
                $timeSignature       = '4/4';
                $default_note_length = '1/8';
                $lines               = explode("\n", $rawTune);
                $bodyLines           = [];
                $fieldNotes          = [];
                $inBody              = false;
                $abcSource           = null;
                $abcOrigin           = null;
                $abcHistory          = null;
                $abcBook             = null;
                $abcDiscography      = null;
                $abcTranscriber      = null;
                $abcArea             = null;
                $abcParts            = null;
                $abcTempo            = null;
                $abcLyrics           = [];

                // ── Extract headers ───────────────────────────────────────────
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (preg_match('/^T:\s*(.+)/', $line, $m)) {
                        if (empty($tuneName)) $tuneName = trim(preg_replace('/\s*\[\d+\]/', '', trim($m[1])));
                    } elseif (preg_match('/^M:\s*(.+)/', $line, $m)) {
                        $ts = trim($m[1]);
                        if ($ts === 'C')  $ts = '4/4';
                        elseif ($ts === 'C|') $ts = '2/2';
                        $timeSignature = $ts;
                    } elseif (preg_match('/^L:\s*(.+)/', $line, $m)) {
                        $default_note_length = trim($m[1]);
                    } elseif (preg_match('/^K:\s*(.+)/', $line, $m)) {
                        $keySignature = trim($m[1]);
                        $inBody = true;
                    } elseif (preg_match('/^S:\s*(.+)/', $line, $m)) {
                        $abcSource = trim($m[1]);
                        $fieldNotes[] = trim($m[1]);
                    } elseif (preg_match('/^O:\s*(.+)/', $line, $m)) {
                        $abcOrigin = trim($m[1]);
                    } elseif (preg_match('/^H:\s*(.+)/', $line, $m)) {
                        $abcHistory = trim($m[1]);
                    } elseif (preg_match('/^B:\s*(.+)/', $line, $m)) {
                        $abcBook = trim($m[1]);
                    } elseif (preg_match('/^D:\s*(.+)/', $line, $m)) {
                        $abcDiscography = trim($m[1]);
                    } elseif (preg_match('/^Z:\s*(.+)/', $line, $m)) {
                        $abcTranscriber = trim($m[1]);
                        $fieldNotes[] = trim($m[1]);
                    } elseif (preg_match('/^A:\s*(.+)/', $line, $m)) {
                        $abcArea = trim($m[1]);
                    } elseif (preg_match('/^P:\s*(.+)/', $line, $m) && !$inBody) {
                        $abcParts = trim($m[1]);
                    } elseif (preg_match('/^Q:\s*(.+)/', $line, $m) && !$inBody) {
                        $abcTempo = trim($m[1]);
                    } elseif (preg_match('/^N:\s*(.+)/', $line, $m)) {
                        $fieldNotes[] = trim($m[1]);
                    } elseif (preg_match('/^W:\s*(.+)/', $line, $m)) {
                        $abcLyrics[] = trim($m[1]);
                    } elseif ($inBody) {
                        if (empty($line)) continue;
                        // Stop collecting body lines when we hit an annotation title.
                        // Look for any all-caps word (2+ letters) anywhere on the line
                        // that contains a letter H-Y — such a letter can't be an ABC
                        // note (A-G) or rest (Z), so it must be an English word.
                        // Guard against %% directives and lines with | (bar lines).
                        $isAnnotTitle = $line[0] !== '%'
                            && !preg_match('/^[A-Za-z]:/', $line)
                            && strpos($line, '|') === false
                            && (bool) preg_match('/\b(?=[A-Z]{2,}\b)[A-Z]*[H-Y][A-Z]*\b/', $line);
                        if ($isAnnotTitle) break;
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

                if (empty($tuneName) && !$isAdditionalSetting) {
                    $results[] = ['status' => 'skipped', 'reason' => 'No T: field found', 'tune' => $rawTune];
                    continue;
                }

                // ── Additional setting (X:2, X:3, etc.) ──────────────────────
                if ($isAdditionalSetting) {
                    $tuneId = $lastTuneId;
                    $settingName = $tuneName ?: $lastTuneName;
                    $stmt = $pdo->prepare("
                        INSERT INTO setting (tune_id, user_id, name, default_note_length, time_signature, key_signature, abc_transcription, source, origin, history, book, discography, transcription_credit, area, parts, tempo, lyrics)
                        VALUES (:tune_id, :user_id, :name, :default_note_length, :time_signature, :key_signature, :abc_transcription, :source, :origin, :history, :book, :discography, :transcription_credit, :area, :parts, :tempo, :lyrics)
                    ");
                    $stmt->execute([
                        ':tune_id'             => $tuneId,
                        ':user_id'             => $userId,
                        ':name'                => $settingName,
                        ':default_note_length' => $default_note_length,
                        ':time_signature'      => $timeSignature,
                        ':key_signature'       => $keySignature,
                        ':abc_transcription'   => $abcBody,
                        ':source'              => $abcSource,
                        ':origin'              => $abcOrigin,
                        ':history'             => $abcHistory,
                        ':book'                => $abcBook,
                        ':discography'         => $abcDiscography,
                        ':transcription_credit' => $abcTranscriber,
                        ':area'                => $abcArea,
                        ':parts'               => $abcParts,
                        ':tempo'               => $abcTempo,
                        ':lyrics'              => !empty($abcLyrics) ? implode("\n", $abcLyrics) : null,
                    ]);
                    $results[] = ['status' => 'additional_setting', 'tune' => $settingName, 'tune_id' => $tuneId];
                    continue;
                }

                // ── Check if tune exists ──────────────────────────────────────
                $stmt = $pdo->prepare("SELECT tune_id FROM tune WHERE name = :name LIMIT 1");
                $stmt->execute([':name' => $tuneName]);
                $existingTune = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingTune) {
                    $tuneId = $existingTune['tune_id'];
                    $stmt = $pdo->prepare("
                        INSERT INTO setting (tune_id, user_id, name, default_note_length, time_signature, key_signature, abc_transcription, source, origin, history, book, discography, transcription_credit, area, parts, tempo, lyrics)
                        VALUES (:tune_id, :user_id, :name, :default_note_length, :time_signature, :key_signature, :abc_transcription, :source, :origin, :history, :book, :discography, :transcription_credit, :area, :parts, :tempo, :lyrics)
                    ");
                    $stmt->execute([
                        ':tune_id'             => $tuneId,
                        ':user_id'             => $userId,
                        ':name'                => $tuneName,
                        ':default_note_length' => $default_note_length,
                        ':time_signature'      => $timeSignature,
                        ':key_signature'       => $keySignature,
                        ':abc_transcription'   => $abcBody,
                        ':source'              => $abcSource,
                        ':origin'              => $abcOrigin,
                        ':history'             => $abcHistory,
                        ':book'                => $abcBook,
                        ':discography'         => $abcDiscography,
                        ':transcription_credit' => $abcTranscriber,
                        ':area'                => $abcArea,
                        ':parts'               => $abcParts,
                        ':tempo'               => $abcTempo,
                        ':lyrics'              => !empty($abcLyrics) ? implode("\n", $abcLyrics) : null,
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
                        INSERT INTO setting (tune_id, user_id, name, default_note_length, time_signature, key_signature, abc_transcription, source, origin, history, book, discography, transcription_credit, area, parts, tempo, lyrics)
                        VALUES (:tune_id, :user_id, :name, :default_note_length, :time_signature, :key_signature, :abc_transcription, :source, :origin, :history, :book, :discography, :transcription_credit, :area, :parts, :tempo, :lyrics)
                    ");
                    $stmt->execute([
                        ':tune_id'             => $tuneId,
                        ':user_id'             => $userId,
                        ':name'                => $tuneName,
                        ':default_note_length' => $default_note_length,
                        ':time_signature'      => $timeSignature,
                        ':key_signature'       => $keySignature,
                        ':abc_transcription'   => $abcBody,
                        ':source'              => $abcSource,
                        ':origin'              => $abcOrigin,
                        ':history'             => $abcHistory,
                        ':book'                => $abcBook,
                        ':discography'         => $abcDiscography,
                        ':transcription_credit' => $abcTranscriber,
                        ':area'                => $abcArea,
                        ':parts'               => $abcParts,
                        ':tempo'               => $abcTempo,
                        ':lyrics'              => !empty($abcLyrics) ? implode("\n", $abcLyrics) : null,
                    ]);
                    $settingId = $pdo->lastInsertId();
                    $results[] = ['status' => 'inserted', 'tune' => $tuneName, 'tune_id' => $tuneId, 'setting_id' => $settingId];
                }

                $lastTuneId = $tuneId;
                $lastTuneName = $tuneName;

                // ── Save notes ────────────────────────────────────────────────
                $noteStmt = $pdo->prepare("INSERT INTO tune_note (tune_id, note) VALUES (:tune_id, :note)");
                if (!empty($tuneNotes)) {
                    // Split annotation into one row per paragraph, skip separators like ***
                    $paragraphs = preg_split('/\n{2,}/', trim($tuneNotes));
                    foreach ($paragraphs as $para) {
                        $para = preg_replace('/\n+/', "\n", trim($para));
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
                            $para = preg_replace('/\n+/', "\n", trim($para));
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
        <?php
            $newTunes = array_filter($results, fn($r) => $r['status'] === 'inserted');
            $existingTunes = array_filter($results, fn($r) => $r['status'] === 'existing_tune');
            $additionalSettings = array_filter($results, fn($r) => $r['status'] === 'additional_setting');
            $skipped = array_filter($results, fn($r) => $r['status'] === 'skipped');

            $multiSettingTunes = [];
            foreach ($results as $r) {
                if ($r['status'] === 'additional_setting' && isset($r['tune_id'])) {
                    $multiSettingTunes[$r['tune_id']][] = $r['tune'];
                }
            }
        ?>
        <div class="success-message">
            <p>Collection <strong><?= htmlspecialchars($collectionName) ?></strong> created.</p>
            <ul>
                <li><?= count($newTunes) ?> new tune(s) added</li>
                <?php if (!empty($additionalSettings)): ?>
                <li><?= count($multiSettingTunes) ?> tune(s) with multiple settings (<?= count($additionalSettings) ?> additional setting(s) total)</li>
                <?php endif; ?>
                <?php if (!empty($existingTunes)): ?>
                <li><?= count($existingTunes) ?> existing tune(s) — new setting added:
                    <ul>
                        <?php foreach ($existingTunes as $et): ?>
                        <li><?= htmlspecialchars($et['tune']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if (!empty($skipped)): ?>
                <li><?= count($skipped) ?> skipped</li>
                <?php endif; ?>
            </ul>
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
                            <?php elseif ($r['status'] === 'additional_setting'): ?>
                                Additional setting added
                            <?php else: ?>
                                Skipped: <?= htmlspecialchars($r['reason'] ?? '') ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>