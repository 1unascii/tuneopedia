<?php

class User {

    // ── Authentication ────────────────────────────────────────────────────────

    /**
     * Verify credentials and return the user row on success, null on failure.
     *
     * Passwords are stored as binary SHA1 (UNHEX(SHA1(...))).  The comparison
     * is done in SQL so the raw binary never needs to pass through PHP.
     *
     * Replaces the buggy authenticateUser() in connect.php, which called
     * sha1($password) as a truthy check instead of comparing it to the stored
     * hash — meaning any password worked for any user.
     */
    public static function authenticate(PDO $pdo, string $username, string $password): ?array {
        $stmt = $pdo->prepare(
            "SELECT * FROM user WHERE user_name = :username AND password = UNHEX(SHA1(:password))"
        );
        $stmt->execute([':username' => $username, ':password' => $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // ── Registration ──────────────────────────────────────────────────────────

    /**
     * Check whether a username is already taken.
     */
    public static function existsByUsername(PDO $pdo, string $username): bool {
        $stmt = $pdo->prepare("SELECT 1 FROM user WHERE user_name = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Check whether an e-mail address is already registered.
     */
    public static function existsByEmail(PDO $pdo, string $email): bool {
        $stmt = $pdo->prepare("SELECT 1 FROM user WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Insert a new user row.
     *
     * Returns an array with:
     *   'success' => bool
     *   'error'   => string|null   — 'username_taken', 'email_taken', or 'db_error'
     *   'user_id' => int|null      — set on success
     */
    public static function register(
        PDO    $pdo,
        string $firstName,
        string $lastName,
        string $username,
        string $email,
        string $password
    ): array {
        if (self::existsByUsername($pdo, $username)) {
            return ['success' => false, 'error' => 'username_taken', 'user_id' => null];
        }
        if (self::existsByEmail($pdo, $email)) {
            return ['success' => false, 'error' => 'email_taken', 'user_id' => null];
        }

        $stmt = $pdo->prepare("
            INSERT INTO user (first_name, last_name, user_name, email, password)
            VALUES (:first_name, :last_name, :user_name, :email, UNHEX(SHA1(:password)))
        ");
        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name'  => $lastName,
            ':user_name'  => $username,
            ':email'      => $email,
            ':password'   => $password,
        ]);

        return ['success' => true, 'error' => null, 'user_id' => (int) $pdo->lastInsertId()];
    }

    // ── Lookup ────────────────────────────────────────────────────────────────

    /**
     * Fetch a single user row by primary key. Returns null if not found.
     */
    public static function findById(PDO $pdo, int $userId): ?array {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // ── Favorites / Tunebook ──────────────────────────────────────────────────

    /**
     * Add a tune to a user's tunebook (favorites).  INSERT IGNORE means
     * calling this twice for the same pair is safe — it's a no-op.
     * Returns true on success, false on DB error.
     */
    public static function addFavorite(PDO $pdo, int $userId, int $tuneId): bool {
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO tunebook (user_id, tune_id) VALUES (:user_id, :tune_id)"
        );
        return $stmt->execute([':user_id' => $userId, ':tune_id' => $tuneId]);
    }

    /**
     * Remove a tune from a user's tunebook.
     */
    public static function removeFavorite(PDO $pdo, int $userId, int $tuneId): bool {
        $stmt = $pdo->prepare(
            "DELETE FROM tunebook WHERE user_id = :user_id AND tune_id = :tune_id"
        );
        return $stmt->execute([':user_id' => $userId, ':tune_id' => $tuneId]);
    }

    /**
     * Check whether a tune is already in a user's tunebook.
     */
    public static function hasFavorite(PDO $pdo, int $userId, int $tuneId): bool {
        $stmt = $pdo->prepare(
            "SELECT 1 FROM tunebook WHERE user_id = :user_id AND tune_id = :tune_id LIMIT 1"
        );
        $stmt->execute([':user_id' => $userId, ':tune_id' => $tuneId]);
        return (bool) $stmt->fetchColumn();
    }
}
