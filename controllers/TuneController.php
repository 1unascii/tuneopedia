<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/Tune.php');
require_once(__DIR__ . '/../models/Setting.php');
require_once(__DIR__ . '/../models/User.php');

class TuneController {

    public function index() {
        $pdo = connect();
        [$groupedTunes, $tune_type_names] = Tune::getAllGroupedByType($pdo);
        include __DIR__ . '/../views/tunes/index.php';
    }

    public function show() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        include_once(__DIR__ . '/../helpers/tune_helpers.php');

        $tune_id = intval($_GET['tune_id'] ?? 0);
        if (!$tune_id) {
            echo '<p class="error-message">Tune not found.</p>';
            return;
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $pdo    = connect();

        $tuneName = Tune::getName($pdo, $tune_id);
        if (!$tuneName) {
            echo '<p class="error-message">Tune not found.</p>';
            return;
        }

        $settings     = Tune::getSettings($pdo, $tune_id, $userId);
        $tuneNotes    = Tune::getNotes($pdo, $tune_id);
        $settingCount = count($settings);
        $primaryId    = !empty($settings) ? (int)$settings[0]['setting_id'] : 0;

        include __DIR__ . '/../views/tunes/show.php';
    }

    public function create() {
        session_start();
        $pdo = connect();
        if (!$pdo) {
            die('Database error');
        }

        $tuneName     = trim($_POST['tune_title'] ?? '');
        $tuneType     = trim($_POST['tune_type'] ?? '');
        $composer     = trim($_POST['composer'] ?? 'Traditional');
        $metre        = trim($_POST['metre'] ?? '4/4');
        $tuneKey      = trim($_POST['tune_key'] ?? '');
        $tuneBody     = trim($_POST['tune_body'] ?? '');
        $userId       = (int) $_SESSION['user_id'];

        $tuneBody = str_replace('<br />', "\n", $tuneBody);
        $tuneBody = str_replace('<br>', "\n", $tuneBody);

        Tune::create($pdo, $tuneName, $tuneType, $composer, $metre, $tuneKey, $tuneBody, $userId);

        echo 'Thank you. Your tune was submitted';
    }

    public function delete() {
        session_start();
        if (!array_key_exists('Authenticated', $_SESSION) || empty($_SESSION['Authenticated']) || !isset($_SESSION['user_id'])) {
            die("Unauthorized");
        }

        $tune_id = (int) $_POST['tune_id'];
        $user_id = (int) $_SESSION['user_id'];

        $pdo = connect();
        if (!$pdo) {
            die("Database error");
        }

        if (Tune::delete($pdo, $tune_id, $user_id)) {
            echo "Deleted successfully.";
        } else {
            echo "You don't have permission to delete this.";
        }
    }

    public function getBody() {
        session_start();
        if (!isset($_POST['setting_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing setting_id']);
            return;
        }

        $pdo       = connect();
        $settingId = intval($_POST['setting_id']);
        $setting   = Setting::findById($pdo, $settingId);

        if ($setting) {
            echo json_encode($setting);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Setting not found']);
        }
    }

    public function toggleFavorite() {
        session_start();
        header('Content-Type: text/plain; charset=utf-8');

        if (!array_key_exists('Authenticated', $_SESSION) || empty($_SESSION['Authenticated']) || !isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo 'Unauthorized';
            return;
        }

        $tune_id = (int) $_POST['tune_id'];
        $user_id = (int) $_POST['user_id'];

        $db = connect();
        if (!$db) {
            http_response_code(500);
            echo 'Database error';
            return;
        }

        $stmt = $db->prepare("SELECT 1 FROM tune WHERE tune_id = ? LIMIT 1");
        $stmt->execute([$tune_id]);
        if (!$stmt->fetchColumn()) {
            http_response_code(404);
            echo 'Tune not found';
            return;
        }

        if (!User::addFavorite($db, $user_id, $tune_id)) {
            http_response_code(500);
            echo 'Could not save favorite';
            return;
        }

        echo 'OK';
    }
}
