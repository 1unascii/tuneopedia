<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/User.php');

class AuthController {

    public function login() {
        session_start();
        $db = connect();
        if (!$db) {
            echo 'Database error';
            return;
        }

        $user = User::authenticate($db, $_POST['user_name'], $_POST['password']);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['Authenticated'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            echo 'Login successful!';
        } else {
            echo 'Invalid username or password.';
        }
    }

    public function logout() {
        session_start();
        if (array_key_exists('Authenticated', $_SESSION)) {
            session_destroy();
            echo "You have been logged out";
        } else {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            $base = preg_replace('#/public$#', '', $base);
            header("Location: $base/");
        }
    }

    public function testCleanup() {
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
        // Delete posts ON this user's threads (by any user), then posts BY this user
        $db->prepare("DELETE FROM post WHERE thread_id IN (SELECT discussion_thread_id FROM discussion_thread WHERE user_id = :uid)")->execute([':uid' => $userId]);
        $db->prepare("DELETE FROM post WHERE user_id = :uid")->execute([':uid' => $userId]);
        $db->prepare("DELETE FROM discussion_thread WHERE user_id = :uid")->execute([':uid' => $userId]);
        $db->prepare("DELETE FROM user WHERE user_id = :uid")->execute([':uid' => $userId]);

        echo 'Deleted';
    }

    public function resetAutoIncrements() {
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

    public function register() {
        session_start();
        $db = connect();
        if (!$db) {
            echo 'Database error';
            return;
        }

        $result = User::register(
            $db,
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['user_name'],
            $_POST['email'],
            $_POST['password']
        );

        if ($result['success']) {
            echo 'Thank you for signing up';
        } else {
            switch ($result['error']) {
                case 'email_taken':
                    echo 'The email you entered is already in use';
                    break;
                case 'username_taken':
                    echo 'The username you entered is already in use';
                    break;
                default:
                    echo 'There was an unexpected error';
                    break;
            }
        }
    }
}
