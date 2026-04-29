<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/Discussion.php');

class DiscussionController {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $pdo     = connect();
        $threads = Discussion::getAllThreads($pdo);
        include __DIR__ . '/../views/discussions/index.php';
    }

    public function show() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $threadId = intval($_GET['thread_id'] ?? 0);
        if (!$threadId) {
            echo '<p class="error-message">Thread not found.</p>';
            return;
        }

        $pdo    = connect();
        $thread = Discussion::findThreadById($pdo, $threadId);
        if (!$thread) {
            echo '<p class="error-message">Thread not found.</p>';
            return;
        }

        $posts = Discussion::getPostsByThreadId($pdo, $threadId);
        include __DIR__ . '/../views/discussions/show.php';
    }

    public function createThread() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['Authenticated'])) {
            http_response_code(401);
            echo json_encode(['error' => 'You must be logged in to start a discussion.']);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $body  = trim($_POST['body'] ?? '');

        if ($title === '' || $body === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Title and body are required.']);
            return;
        }

        $pdo      = connect();
        $userId   = (int) $_SESSION['user_id'];
        $threadId = Discussion::createThread($pdo, $userId, $title, $body);

        echo json_encode(['success' => true, 'thread_id' => $threadId]);
    }

    public function createPost($threadId = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['Authenticated'])) {
            http_response_code(401);
            echo json_encode(['error' => 'You must be logged in to reply.']);
            return;
        }

        $threadId = (int) ($threadId ?: ($_POST['thread_id'] ?? 0));
        $body     = trim($_POST['body'] ?? '');

        if (!$threadId || $body === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Thread ID and body are required.']);
            return;
        }

        $pdo    = connect();
        $userId = (int) $_SESSION['user_id'];

        $thread = Discussion::findThreadById($pdo, $threadId);
        if (!$thread) {
            http_response_code(404);
            echo json_encode(['error' => 'Thread not found.']);
            return;
        }

        $postId = Discussion::createPost($pdo, $threadId, $userId, $body);
        echo json_encode(['success' => true, 'post_id' => $postId]);
    }

    public function deleteThread($threadId = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['Authenticated'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $threadId = (int) ($threadId ?: ($_POST['thread_id'] ?? 0));
        $userId   = (int) $_SESSION['user_id'];
        $pdo      = connect();

        if (Discussion::deleteThread($pdo, $threadId, $userId)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'You do not have permission to delete this thread.']);
        }
    }

    public function deletePost($postId = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['Authenticated'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $postId = (int) ($postId ?: ($_POST['post_id'] ?? 0));
        $userId = (int) $_SESSION['user_id'];
        $pdo    = connect();

        if (Discussion::deletePost($pdo, $postId, $userId)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'You do not have permission to delete this post.']);
        }
    }
}
