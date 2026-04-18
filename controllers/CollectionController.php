<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../helpers/tune_helpers.php');
require_once(__DIR__ . '/../models/Collection.php');

class CollectionController {

    public function index() {
        $pdo         = connect();
        $collections = Collection::getAllWithTunes($pdo);
        include __DIR__ . '/../views/collections/index.php';
    }

    public function create() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        include __DIR__ . '/../controllers/add_collection.php';
    }
}
