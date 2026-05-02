<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/Collection.php');
require_once(__DIR__ . '/../models/Tune.php');
require_once(__DIR__ . '/ParserController.php');
require_once(__DIR__ . '/AbcBodyParser.php');
require_once(__DIR__ . '/../helpers/tune_type_helper.php');

class CollectionController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $pdo         = connect();
        $currentUserId = (int) ($_SESSION['user_id'] ?? 0);
        $collections = Collection::getAllWithTunes($pdo, $currentUserId);
        include __DIR__ . '/../views/collections/index.php';
    }

    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $error   = '';
        $success = false;
        $results = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include_once(__DIR__ . '/../config/database.php');
            $pdo = connect();
            if (!$pdo) { $error = 'Database connection failed'; }

            $collectionName = trim($_POST['collection_name'] ?? '');
            $author         = trim($_POST['author'] ?? '');
            $description    = trim($_POST['description'] ?? '');
            $isShared       = !empty($_POST['is_shared']);
            $normalizeAbc   = !empty($_POST['normalize_abc']);
            $userId         = (int) $_SESSION['user_id'];

            // ── Get ABC text from file upload or paste ────────────────────
            $abcText = '';
            $fileUploaded = !empty($_FILES['abc_files']['name'][0]);
            if ($fileUploaded) {
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
                        if ($content !== false) $abcText .= $content . "\n";
                    }
                }
                if (!empty($fileErrors)) $error = implode(' ', $fileErrors);
            } else {
                $abcText = $_POST['abc_text'] ?? '';
            }

            $abcText = trim($abcText);
            if (empty($abcText) && empty($error)) {
                $error = 'Please upload an ABC file or paste ABC notation.';
            }

            if (empty($error)) {
                // ── Parse ─────────────────────────────────────────────────
                $tuneGroups = ParserController::parse($abcText);

                if (empty($tuneGroups)) {
                    $error = 'No tunes found. Make sure each tune starts with an X: field.';
                } elseif (Collection::existsByName($pdo, $collectionName)) {
                    $error = 'A collection named "' . htmlspecialchars($collectionName) . '" already exists.';
                } else {
                    // ── Create collection ─────────────────────────────────
                    $collectionId = Collection::create($pdo, $collectionName, $author, $description, $isShared, $userId);
                    $position = 1;

                    foreach ($tuneGroups as $group) {
                        $tuneName = $group['name'];
                        $firstSetting = $group['settings'][0];

                        // Detect tune type
                        $tuneTypeName = detectTuneType(
                            $firstSetting['type'],
                            $tuneName,
                            $firstSetting['time_signature']
                        );

                        // Check if tune already exists
                        $existingTuneId = Tune::findByName($pdo, $tuneName);

                        if ($existingTuneId) {
                            $tuneId = $existingTuneId;
                        } else {
                            // Create new tune + first setting
                            $abcBody = $firstSetting['abc_transcription'];
                            if ($normalizeAbc && !empty($abcBody)) {
                                $abcBody = formatAbcBody($abcBody, $firstSetting['time_signature'], $firstSetting['default_note_length'], $tuneName);
                            }

                            $tuneId = Tune::create(
                                $pdo, $tuneName, $tuneTypeName, 'Traditional',
                                $firstSetting['time_signature'],
                                $firstSetting['key_signature'],
                                $abcBody, $userId
                            );

                            // Save alternate titles
                            foreach ($firstSetting['alternate_titles'] as $altTitle) {
                                Tune::addAlternateTitle($pdo, $tuneId, $altTitle);
                            }

                            // Save notes
                            if (!empty($firstSetting['notes'])) {
                                Tune::addNote($pdo, $tuneId, $firstSetting['notes']);
                            }
                            if (!empty($firstSetting['source'])) {
                                Tune::addNote($pdo, $tuneId, $firstSetting['source']);
                            }
                            if (!empty($firstSetting['transcription_credit'])) {
                                Tune::addNote($pdo, $tuneId, $firstSetting['transcription_credit']);
                            }

                            $results[] = ['status' => 'inserted', 'tune' => $tuneName];

                            // Additional settings (X:2, X:3, etc.) for new tunes
                            for ($s = 1; $s < count($group['settings']); $s++) {
                                $setting = $group['settings'][$s];
                                $settingBody = $setting['abc_transcription'];
                                if ($normalizeAbc && !empty($settingBody)) {
                                    $settingBody = formatAbcBody($settingBody, $setting['time_signature'], $setting['default_note_length'], $tuneName);
                                }
                                Tune::addSetting(
                                    $pdo, $tuneId, $userId, $tuneName,
                                    $setting['time_signature'],
                                    $setting['key_signature'],
                                    $settingBody
                                );
                                $results[] = ['status' => 'additional_setting', 'tune' => $tuneName, 'tune_id' => $tuneId];
                            }

                            // Link to collection
                            $pdo->prepare("INSERT INTO collection_tune (collection_id, tune_id, position) VALUES (:cid, :tid, :pos)")
                                ->execute([':cid' => $collectionId, ':tid' => $tuneId, ':pos' => $position]);
                            $position++;
                            continue;
                        }

                        // Existing tune — add all settings as new settings
                        foreach ($group['settings'] as $setting) {
                            $settingBody = $setting['abc_transcription'];
                            if ($normalizeAbc && !empty($settingBody)) {
                                $settingBody = formatAbcBody($settingBody, $setting['time_signature'], $setting['default_note_length'], $tuneName);
                            }
                            Tune::addSetting(
                                $pdo, $tuneId, $userId, $tuneName,
                                $setting['time_signature'],
                                $setting['key_signature'],
                                $settingBody
                            );
                            $results[] = ['status' => 'existing_tune', 'tune' => $tuneName, 'tune_id' => $tuneId];
                        }

                        // Link to collection
                        $pdo->prepare("INSERT INTO collection_tune (collection_id, tune_id, position) VALUES (:cid, :tid, :pos)")
                            ->execute([':cid' => $collectionId, ':tid' => $tuneId, ':pos' => $position]);
                        $position++;
                    }

                    $success = true;
                }
            }
        }

        // Render the view (form + results)
        include __DIR__ . '/../views/collections/create.php';
    }

    public function createFromFavorites() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['Authenticated'])) {
            http_response_code(401);
            echo json_encode(['error' => 'You must be logged in to create a collection.']);
            return;
        }

        $collectionName = trim($_POST['collection_name'] ?? '');
        $author         = trim($_POST['author'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $isShared       = !empty($_POST['is_shared']);
        $tuneIds        = json_decode($_POST['tune_ids'] ?? '[]', true);

        if ($collectionName === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Collection name is required.']);
            return;
        }

        if (empty($tuneIds)) {
            http_response_code(400);
            echo json_encode(['error' => 'No tunes selected.']);
            return;
        }

        include_once(__DIR__ . '/../config/database.php');
        require_once(__DIR__ . '/../models/Collection.php');
        $pdo = connect();

        if (Collection::existsByName($pdo, $collectionName)) {
            http_response_code(409);
            echo json_encode(['error' => 'A collection named "' . $collectionName . '" already exists.']);
            return;
        }

        $userId       = (int) $_SESSION['user_id'];
        $collectionId = Collection::create($pdo, $collectionName, $author, $description, $isShared, $userId);
        Collection::addTunes($pdo, $collectionId, $tuneIds);

        if (!empty($_POST['remove_from_favorites'])) {
            require_once(__DIR__ . '/../models/User.php');
            foreach ($tuneIds as $tuneId) {
                User::removeFavorite($pdo, $userId, (int) $tuneId);
            }
        }

        echo json_encode(['success' => true, 'collection_id' => $collectionId, 'tune_count' => count($tuneIds)]);
    }

    public function addToExisting($collectionId = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['Authenticated'])) {
            http_response_code(401);
            echo json_encode(['error' => 'You must be logged in.']);
            return;
        }

        $collectionId = (int) ($collectionId ?: ($_POST['collection_id'] ?? 0));
        $tuneIds      = json_decode($_POST['tune_ids'] ?? '[]', true);

        if (!$collectionId || empty($tuneIds)) {
            http_response_code(400);
            echo json_encode(['error' => 'Collection and tunes are required.']);
            return;
        }

        include_once(__DIR__ . '/../config/database.php');
        require_once(__DIR__ . '/../models/Collection.php');
        require_once(__DIR__ . '/../models/User.php');
        $pdo    = connect();
        $userId = (int) $_SESSION['user_id'];

        Collection::addTunes($pdo, $collectionId, $tuneIds);

        if (!empty($_POST['remove_from_favorites'])) {
            foreach ($tuneIds as $tuneId) {
                User::removeFavorite($pdo, $userId, (int) $tuneId);
            }
        }

        echo json_encode(['success' => true, 'tune_count' => count($tuneIds)]);
    }
}
