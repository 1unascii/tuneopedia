<?php
session_start();
include_once('connect.php');
$pdo = connect();

$tuneName      = trim($_POST['tune_title'] ?? '');
$tuneTypeName  = trim($_POST['tune_type'] ?? '');
$composerName  = trim($_POST['composer'] ?? 'Traditional');
$metre         = trim($_POST['metre'] ?? '4/4');
$noteLength    = trim($_POST['default_note_length'] ?? '1/8');
$tuneKey       = trim($_POST['tune_key'] ?? '');
$tuneBody      = trim($_POST['tune_body'] ?? '');
$userId        = $_SESSION['user_id'];

// CONVERT <br /> BACK TO NEWLINES IF SUBMITTED THAT WAY
$tuneBody = str_replace('<br />', "\n", $tuneBody);
$tuneBody = str_replace('<br>', "\n", $tuneBody);

//------------------------------------------------------------------------------
// GET TUNE TYPE ID
//------------------------------------------------------------------------------
$stmt = $pdo->prepare("SELECT tune_type_id FROM tune_type WHERE name = :name LIMIT 1");
$stmt->execute([':name' => $tuneTypeName]);
$tuneType = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tuneType) {
    echo "Error: Tune type not found.";
    exit;
}
$tuneTypeId = $tuneType['tune_type_id'];

//------------------------------------------------------------------------------
// GET OR CREATE COMPOSER
//------------------------------------------------------------------------------
$stmt = $pdo->prepare("SELECT composer_id FROM composer WHERE name = :name LIMIT 1");
$stmt->execute([':name' => $composerName]);
$composer = $stmt->fetch(PDO::FETCH_ASSOC);

if ($composer) {
    $composerId = $composer['composer_id'];
} else {
    $stmt = $pdo->prepare("INSERT INTO composer (name) VALUES (:name)");
    $stmt->execute([':name' => $composerName]);
    $composerId = $pdo->lastInsertId();
}

//------------------------------------------------------------------------------
// INSERT TUNE
//------------------------------------------------------------------------------
$stmt = $pdo->prepare("
    INSERT INTO tune (name, tune_type_id, composer_id)
    VALUES (:name, :tune_type_id, :composer_id)
");
$stmt->execute([
    ':name'         => $tuneName,
    ':tune_type_id' => $tuneTypeId,
    ':composer_id'  => $composerId
]);
$tuneId = $pdo->lastInsertId();

//------------------------------------------------------------------------------
// INSERT SETTING (first setting for this tune)
//------------------------------------------------------------------------------
$stmt = $pdo->prepare("
    INSERT INTO setting (tune_id, user_id, name, time_signature, key_signature, abc_transcription)
    VALUES (:tune_id, :user_id, :name, :time_signature, :key_signature, :abc_transcription)
");
$stmt->execute([
    ':tune_id'           => $tuneId,
    ':user_id'           => $userId,
    ':name'              => $tuneName,
    ':time_signature'    => $metre,
    ':key_signature'     => $tuneKey,
    ':abc_transcription' => $tuneBody
]);

echo 'Thank you. Your tune was submitted';