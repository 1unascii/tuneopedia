<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/Setting.php');
require_once(__DIR__ . '/../models/Tune.php');

class SettingController {

    public function edit() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $pdo = connect();

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

    public function update($settingId = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        $settingId = (int)($settingId ?: ($_POST['setting_id'] ?? 0));
        if (!$settingId) {
            echo json_encode(['error' => 'Invalid setting']);
            return;
        }

        $pdo = connect();
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
            'instrument_id'       => !empty($_POST['instrument_id']) ? (int)$_POST['instrument_id'] : null,
        ]);

        if (!$updated) {
            echo json_encode(['error' => 'Setting not found']);
            return;
        }

        echo json_encode(['success' => true, 'setting' => $updated]);
    }

    public function addForm() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) {
            echo '<p class="error-message">Please log in to add a setting.</p>';
            return;
        }

        $tuneId = intval($_GET['tune_id'] ?? 0);
        if (!$tuneId) {
            echo '<p class="error-message">Invalid tune.</p>';
            return;
        }

        $pdo = connect();
        $tuneName = Tune::getName($pdo, $tuneId);
        if (!$tuneName) {
            echo '<p class="error-message">Tune not found.</p>';
            return;
        }

        include __DIR__ . '/../views/settings/add.php';
    }

    public function vote($settingId = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        if (!$userId) {
            echo json_encode(['error' => 'Not logged in']);
            return;
        }

        $settingId = (int)($settingId ?: ($_POST['setting_id'] ?? 0));
        $voteValue = intval($_POST['vote_value'] ?? 0);

        if (!$settingId || !in_array($voteValue, [1, -1])) {
            echo json_encode(['error' => 'Invalid parameters']);
            return;
        }

        $pdo = connect();
        echo json_encode(Setting::vote($pdo, $settingId, $userId, $voteValue));
    }
}
