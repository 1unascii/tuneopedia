<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../helpers/tune_helpers.php');
require_once(__DIR__ . '/../models/Setting.php');
require_once(__DIR__ . '/../models/Tune.php');

class SettingController {

    public function edit() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $pdo = connect();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $settingId = intval($_POST['setting_id'] ?? 0);
            if (!$settingId) {
                echo json_encode(['error' => 'Invalid setting']);
                return;
            }

            $updated = Setting::update($pdo, $settingId, [
                'tune_name'           => trim($_POST['tune_name']           ?? ''),
                'tune_type'           => trim($_POST['tune_type']           ?? ''),
                'time_signature'      => trim($_POST['time_signature']      ?? '4/4'),
                'key_signature'       => trim($_POST['key_signature']       ?? ''),
                'default_note_length' => trim($_POST['default_note_length'] ?? '1/8'),
                'abc_transcription'   => trim($_POST['abc_transcription']   ?? ''),
                'source'              => trim($_POST['source']              ?? ''),
                'origin'              => trim($_POST['origin']              ?? ''),
                'history'             => trim($_POST['history']             ?? ''),
                'book'                => trim($_POST['book']                ?? ''),
                'discography'         => trim($_POST['discography']         ?? ''),
                'transcription_credit' => trim($_POST['transcription_credit'] ?? ''),
                'area'                => trim($_POST['area']                ?? ''),
                'parts'               => trim($_POST['parts']              ?? ''),
                'tempo'               => trim($_POST['tempo']              ?? ''),
                'lyrics'              => trim($_POST['lyrics']             ?? ''),
            ]);

            if (!$updated) {
                echo json_encode(['error' => 'Setting not found']);
                return;
            }

            echo json_encode(['success' => true, 'setting' => $updated]);
            return;
        }

        $settingId = intval($_GET['setting_id'] ?? 0);
        if (!$settingId) {
            echo '<p class="error-message">Invalid setting.</p>';
            return;
        }

        $setting = Setting::getForEdit($pdo, $settingId);
        if (!$setting) {
            echo '<p class="error-message">Setting not found.</p>';
            return;
        }

        $tuneTypes = Tune::getAllTypes($pdo);
        include __DIR__ . '/../views/settings/edit.php';
    }

    public function vote() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if (!$userId) {
            echo json_encode(['error' => 'Not logged in']);
            return;
        }

        $settingId = intval($_POST['setting_id'] ?? 0);
        $voteValue = intval($_POST['vote_value'] ?? 0);

        if (!$settingId || !in_array($voteValue, [1, -1])) {
            echo json_encode(['error' => 'Invalid parameters']);
            return;
        }

        $pdo = connect();
        echo json_encode(Setting::vote($pdo, $settingId, $userId, $voteValue));
    }
}
