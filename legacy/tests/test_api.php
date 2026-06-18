<?php
/**
 * Test API endpoints — called by populate_test_data.html.
 * Handles test user cleanup and auto-increment resets.
 */

require_once(__DIR__ . '/../config/database.php');

function testCleanup() {
    $username = $_POST['user_name'] ?? '';
    if (!preg_match('/^testuser_/', $username)) {
        echo 'Refused: only test users can be deleted';
        return;
    }
    $db = connect();
    if (!$db) { echo 'Database error'; return; }

    $stmt = $db->prepare("SELECT user_id FROM user WHERE user_name = :name LIMIT 1");
    $stmt->execute([':name' => $username]);
    $userId = $stmt->fetchColumn();
    if (!$userId) { echo 'User not found'; return; }

    // Delete in order to avoid FK constraint violations
    $collStmt = $db->prepare("SELECT collection_id FROM collection WHERE user_id = :uid");
    $collStmt->execute([':uid' => $userId]);
    $collectionIds = $collStmt->fetchAll(PDO::FETCH_COLUMN);
    if (!empty($collectionIds)) {
        $placeholders = implode(',', array_fill(0, count($collectionIds), '?'));
        $db->prepare("DELETE FROM collection_tune WHERE collection_id IN ($placeholders)")->execute($collectionIds);
        $db->prepare("DELETE FROM collection WHERE collection_id IN ($placeholders)")->execute($collectionIds);
    }
    $db->prepare("DELETE FROM favorites WHERE user_id = :uid")->execute([':uid' => $userId]);
    $db->prepare("DELETE FROM setting_vote WHERE user_id = :uid")->execute([':uid' => $userId]);
    $db->prepare("DELETE FROM setting WHERE user_id = :uid")->execute([':uid' => $userId]);
    $db->prepare("DELETE FROM post WHERE thread_id IN (SELECT discussion_thread_id FROM discussion_thread WHERE user_id = :uid)")->execute([':uid' => $userId]);
    $db->prepare("DELETE FROM post WHERE user_id = :uid")->execute([':uid' => $userId]);
    $db->prepare("DELETE FROM discussion_thread WHERE user_id = :uid")->execute([':uid' => $userId]);
    $db->prepare("DELETE FROM user WHERE user_id = :uid")->execute([':uid' => $userId]);

    echo 'Deleted';
}

function resetAutoIncrements() {
    $db = connect();
    if (!$db) { echo 'Database error'; return; }

    $tables = [
        'artist_album', 'collection_tune', 'setting_vote', 'tune_alias',
        'tune_track', 'tune_video', 'favorites', 'post', 'discussion_thread',
        'relationship', 'track', 'album', 'artist', 'setting', 'collection',
        'tune', 'user'
    ];

    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach ($tables as $table) {
        try {
            $db->exec("ALTER TABLE $table AUTO_INCREMENT = 1");
        } catch (Exception $e) {
            // Table might not exist, skip
        }
    }
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo 'Auto-increments reset';
}
