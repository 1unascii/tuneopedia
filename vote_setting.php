<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once('connect.php');

header('Content-Type: application/json');

$userId = (int)($_SESSION['user_id'] ?? 0);
if (!$userId) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$settingId = intval($_POST['setting_id'] ?? 0);
$voteValue = intval($_POST['vote_value'] ?? 0);

if (!$settingId || !in_array($voteValue, [1, -1])) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

$pdo = connect();

// vote_id is a composite of user_id and setting_id (no AUTO_INCREMENT).
// Formula: user_id * 100000 + setting_id (supports up to ~21,474 users and 100,000 settings).
$voteId = $userId * 100000 + $settingId;

// Check whether the user already has a vote for this setting
$stmt = $pdo->prepare("SELECT vote_value FROM setting_vote WHERE vote_id = :vote_id");
$stmt->execute([':vote_id' => $voteId]);
$existing = $stmt->fetchColumn();

if ($existing !== false && (int)$existing === $voteValue) {
    // Same vote clicked again: toggle it off
    $pdo->prepare("DELETE FROM setting_vote WHERE vote_id = :vote_id")
        ->execute([':vote_id' => $voteId]);
    $userVote = null;
} else {
    // New vote or switching direction: upsert
    $pdo->prepare("
        INSERT INTO setting_vote (vote_id, user_id, setting_id, vote_value)
        VALUES (:vote_id, :user_id, :setting_id, :vote_value)
        ON DUPLICATE KEY UPDATE vote_value = VALUES(vote_value)
    ")->execute([
        ':vote_id'    => $voteId,
        ':user_id'    => $userId,
        ':setting_id' => $settingId,
        ':vote_value' => $voteValue,
    ]);
    $userVote = $voteValue;
}

// Return updated totals
$stmt = $pdo->prepare("SELECT COALESCE(SUM(vote_value), 0) FROM setting_vote WHERE setting_id = :setting_id");
$stmt->execute([':setting_id' => $settingId]);
$voteScore = (int)$stmt->fetchColumn();

echo json_encode([
    'setting_id' => $settingId,
    'vote_score' => $voteScore,
    'user_vote'  => $userVote,
]);
