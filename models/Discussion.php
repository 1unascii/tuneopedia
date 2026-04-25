<?php

class Discussion {

    private static function sql(string $filename): string {
        return file_get_contents(__DIR__ . '/sql/discussions/' . $filename);
    }

    // ── Thread Listing ───────────────────────────────────────────────────────

    /**
     * Fetch all threads with their author, post count, and last activity.
     */
    public static function getAllThreads(PDO $pdo): array {
        $statement = $pdo->query(self::sql('getThreads.sql'));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Single Thread ────────────────────────────────────────────────────────

    /**
     * Fetch a single thread by its ID. Returns null if not found.
     */
    public static function findThreadById(PDO $pdo, int $threadId): ?array {
        $statement = $pdo->prepare(self::sql('getThread.sql'));
        $statement->execute([':thread_id' => $threadId]);
        $thread = $statement->fetch(PDO::FETCH_ASSOC);
        return $thread ?: null;
    }

    /**
     * Fetch all posts for a given thread, oldest first.
     */
    public static function getPostsByThreadId(PDO $pdo, int $threadId): array {
        $statement = $pdo->prepare(self::sql('getPostsForThread.sql'));
        $statement->execute([':thread_id' => $threadId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Creation ─────────────────────────────────────────────────────────────

    /**
     * Create a new discussion thread with its first post.
     * The first post is inserted via createPost(). Returns the new thread ID.
     */
    public static function createThread(PDO $pdo, int $userId, string $title, string $body): int {
        $statement = $pdo->prepare(self::sql('createThread.sql'));
        $statement->execute([
            ':user_id' => $userId,
            ':title'   => $title,
        ]);
        $threadId = (int) $pdo->lastInsertId();

        // The opening post is created via createPost so all posts
        // go through the same code path.
        self::createPost($pdo, $threadId, $userId, $body);

        return $threadId;
    }

    /**
     * Create a post on a thread and update the thread's timestamp.
     * Returns the new post ID.
     */
    public static function createPost(PDO $pdo, int $threadId, int $userId, string $body): int {
        $statement = $pdo->prepare(self::sql('createPost.sql'));
        $statement->execute([
            ':thread_id' => $threadId,
            ':user_id'   => $userId,
            ':body'      => $body,
        ]);
        $postId = (int) $pdo->lastInsertId();

        $pdo->prepare(self::sql('updateThreadTimestamp.sql'))
            ->execute([':thread_id' => $threadId]);

        return $postId;
    }

    // ── Deletion ─────────────────────────────────────────────────────────────

    /**
     * Delete a thread if the given user owns it.
     * Deletes all posts on the thread first (no CASCADE on FK).
     * Returns true on success.
     */
    public static function deleteThread(PDO $pdo, int $threadId, int $userId): bool {
        // Verify ownership
        $checkStatement = $pdo->prepare(self::sql('checkThreadOwnership.sql'));
        $checkStatement->execute([
            ':thread_id' => $threadId,
            ':user_id'   => $userId,
        ]);
        if (!$checkStatement->fetchColumn()) {
            return false;
        }

        // Delete all posts on this thread first
        $pdo->prepare(self::sql('deletePostsForThread.sql'))
            ->execute([':thread_id' => $threadId]);

        // Delete the thread
        $pdo->prepare(self::sql('deleteThread.sql'))
            ->execute([':thread_id' => $threadId]);

        return true;
    }

    /**
     * Delete a post if the given user owns it. Returns true on success.
     */
    public static function deletePost(PDO $pdo, int $postId, int $userId): bool {
        $statement = $pdo->prepare(self::sql('deletePost.sql'));
        $statement->execute([
            ':post_id' => $postId,
            ':user_id' => $userId,
        ]);
        return $statement->rowCount() > 0;
    }
}
