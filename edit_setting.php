<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('connect.php');
include_once('functions.php');

$pdo = connect();

// ── POST: save changes ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $settingId     = intval($_POST['setting_id']          ?? 0);
    $tuneName      = trim($_POST['tune_name']              ?? '');
    $tuneTypeName  = trim($_POST['tune_type']              ?? '');
    $timeSignature = trim($_POST['time_signature']         ?? '4/4');
    $keySignature  = trim($_POST['key_signature']          ?? '');
    $noteLength    = trim($_POST['default_note_length']    ?? '1/8');
    $abcBody       = trim($_POST['abc_transcription']      ?? '');

    if (!$settingId) {
        echo json_encode(['error' => 'Invalid setting']);
        exit;
    }

    // Get the tune_id for this setting
    $stmt = $pdo->prepare("SELECT tune_id FROM setting WHERE setting_id = :id");
    $stmt->execute([':id' => $settingId]);
    $tuneId = (int)$stmt->fetchColumn();

    if (!$tuneId) {
        echo json_encode(['error' => 'Setting not found']);
        exit;
    }

    // Update tune name
    if ($tuneName !== '') {
        $pdo->prepare("UPDATE tune SET name = :name WHERE tune_id = :id")
            ->execute([':name' => $tuneName, ':id' => $tuneId]);
    }

    // Update tune type
    if ($tuneTypeName !== '') {
        $stmt = $pdo->prepare("SELECT tune_type_id FROM tune_type WHERE LOWER(name) = LOWER(:name) LIMIT 1");
        $stmt->execute([':name' => $tuneTypeName]);
        $tuneTypeId = $stmt->fetchColumn();
        if (!$tuneTypeId) {
            $pdo->prepare("INSERT IGNORE INTO tune_type (name) VALUES (:name)")
                ->execute([':name' => ucfirst($tuneTypeName)]);
            $stmt = $pdo->prepare("SELECT tune_type_id FROM tune_type WHERE LOWER(name) = LOWER(:name) LIMIT 1");
            $stmt->execute([':name' => $tuneTypeName]);
            $tuneTypeId = $stmt->fetchColumn();
        }
        if ($tuneTypeId) {
            $pdo->prepare("UPDATE tune SET tune_type_id = :tid WHERE tune_id = :id")
                ->execute([':tid' => $tuneTypeId, ':id' => $tuneId]);
        }
    }

    // Update setting
    $pdo->prepare("
        UPDATE setting
        SET time_signature      = :time_signature,
            key_signature       = :key_signature,
            default_note_length = :default_note_length,
            abc_transcription   = :abc_transcription
        WHERE setting_id = :setting_id
    ")->execute([
        ':time_signature'      => $timeSignature,
        ':key_signature'       => $keySignature,
        ':default_note_length' => $noteLength,
        ':abc_transcription'   => $abcBody,
        ':setting_id'          => $settingId,
    ]);

    // Return updated data so JS can re-render notation without a page reload
    $stmt = $pdo->prepare("
        SELECT s.setting_id, s.time_signature, s.key_signature,
               s.default_note_length, s.abc_transcription,
               t.name AS tune_name
        FROM setting s
        JOIN tune t ON t.tune_id = s.tune_id
        WHERE s.setting_id = :id
    ");
    $stmt->execute([':id' => $settingId]);
    $updated = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'setting' => $updated]);
    exit;
}

// ── GET: return edit form HTML ────────────────────────────────────────────────
$settingId = intval($_GET['setting_id'] ?? 0);
if (!$settingId) {
    echo '<p class="error-message">Invalid setting.</p>';
    exit;
}

$stmt = $pdo->prepare("
    SELECT s.setting_id, s.time_signature, s.key_signature,
           s.default_note_length, s.abc_transcription,
           t.name AS tune_name, tt.name AS tune_type_name
    FROM   setting   s
    JOIN   tune      t  ON  t.tune_id      = s.tune_id
    LEFT JOIN tune_type tt ON tt.tune_type_id = t.tune_type_id
    WHERE  s.setting_id = :id
");
$stmt->execute([':id' => $settingId]);
$setting = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$setting) {
    echo '<p class="error-message">Setting not found.</p>';
    exit;
}

$tuneTypes = simpleQuery("SELECT name FROM tune_type ORDER BY name");
include 'forms/edit_setting.php';
