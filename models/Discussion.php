<?php

class Discussion {

    // ── Thread Listing ───────────────────────────────────────────────────────

    /**
     * Fetch all threads with their author, post count, and last activity.
     */
    public static function getAllThreads(PDO $pdo): array {
        $statement = $pdo->query("
            SELECT
                dt.discussion_thread_id,
                dt.title,
                dt.created_at,
                dt.updated_at,
                u.user_name,
                u.user_id,
                (SELECT COUNT(*) FROM post p
                 WHERE p.thread_id = dt.discussion_thread_id) AS post_count,
                (SELECT MAX(p2.created_at) FROM post p2
                 WHERE p2.thread_id = dt.discussion_thread_id) AS last_post_at
            FROM discussion_thread dt
            JOIN user u ON u.user_id = dt.user_id
            ORDER BY COALESCE(
                (SELECT MAX(p3.created_at) FROM post p3
                 WHERE p3.thread_id = dt.discussion_thread_id),
                dt.created_at
            ) DESC
        ");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Single Thread ────────────────────────────────────────────────────────

    /**
     * Fetch a single thread by its ID. Returns null if not found.
     */
    public static function findThreadById(PDO $pdo, int $threadId): ?array {
        $statement = $pdo->prepare("
            SELECT
                dt.discussion_thread_id,
                dt.title,
                dt.created_at,
                dt.user_id,
                u.user_name
            FROM discussion_thread dt
            JOIN user u ON u.user_id = dt.user_id
            WHERE dt.discussion_thread_id = :thread_id
        ");
        $statement->execute([':thread_id' => $threadId]);
        $thread = $statement->fetch(PDO::FETCH_ASSOC);
        return $thread ?: null;
    }

    /**
     * Fetch all posts for a given thread, oldest first.
     */
    public static function getPostsByThreadId(PDO $pdo, int $threadId): array {
        $statement = $pdo->prepare("
            SELECT
                p.post_id,
                p.body,
                p.created_at,
                p.user_id,
                u.user_name
            FROM post p
            JOIN user u ON u.user_id = p.user_id
            WHERE p.thread_id = :thread_id
            ORDER BY p.created_at ASC
        ");
        $statement->execute([':thread_id' => $threadId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Creation ─────────────────────────────────────────────────────────────

    /**
     * Create a new discussion thread with its first post. Returns the new thread ID.
     */
    public static function createThread(PDO $pdo, int $userId, string $title, string $body): int {
        $threadStatement = $pdo->prepare("
            INSERT INTO discussion_thread (user_id, title, created_at, updated_at)
            VALUES (:user_id, :title, NOW(), NOW())
        ");
        $threadStatement->execute([
            ':user_id' => $userId,
            ':title'   => $title,
        ]);
        $threadId = (int) $pdo->lastInsertId();

        $postStatement = $pdo->prepare("
            INSERT INTO post (thread_id, user_id, body, created_at)
            VALUES (:thread_id, :user_id, :body, NOW())
        ");
        $postStatement->execute([
            ':thread_id' => $threadId,
            ':user_id'   => $userId,
            ':body'      => $body,
        ]);

        return $threadId;
    }

    /**
     * Create a post on a thread. Returns the new post ID.
     */
    public static function createPost(PDO $pdo, int $threadId, int $userId, string $body): int {
        $statement = $pdo->prepare("
            INSERT INTO post (thread_id, user_id, body, created_at)
            VALUES (:thread_id, :user_id, :body, NOW())
        ");
        $statement->execute([
            ':thread_id' => $threadId,
            ':user_id'   => $userId,
            ':body'      => $body,
        ]);

        $updateStatement = $pdo->prepare("
            UPDATE discussion_thread SET updated_at = NOW()
            WHERE discussion_thread_id = :thread_id
        ");
        $updateStatement->execute([':thread_id' => $threadId]);

        return (int) $pdo->lastInsertId();
    }

    // ── Deletion ─────────────────────────────────────────────────────────────

    /**
     * Delete a thread if the given user owns it. Returns true on success.
     */
    public static function deleteThread(PDO $pdo, int $threadId, int $userId): bool {
        $statement = $pdo->prepare("
            DELETE FROM discussion_thread
            WHERE discussion_thread_id = :thread_id AND user_id = :user_id
        ");
        $statement->execute([
            ':thread_id' => $threadId,
            ':user_id'   => $userId,
        ]);
        return $statement->rowCount() > 0;
    }

    /**
     * Delete a post if the given user owns it. Returns true on success.
     */
    public static function deletePost(PDO $pdo, int $postId, int $userId): bool {
        $statement = $pdo->prepare("
            DELETE FROM post
            WHERE post_id = :post_id AND user_id = :user_id
        ");
        $statement->execute([
            ':post_id' => $postId,
            ':user_id' => $userId,
        ]);
        return $statement->rowCount() > 0;
    }
}
