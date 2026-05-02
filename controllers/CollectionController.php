<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/Collection.php');

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
        include __DIR__ . '/../controllers/CollectionParser.php';
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
