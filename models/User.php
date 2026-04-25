<?php

class User {

    private static function sql(string $filename): string {
        return file_get_contents(__DIR__ . '/sql/users/' . $filename);
    }

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
        $stmt = $pdo->prepare(self::sql('authenticate.sql'));
        $stmt->execute([':username' => $username, ':password' => $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // ── Registration ──────────────────────────────────────────────────────────

    /**
     * Check whether a username is already taken.
     */
    public static function existsByUsername(PDO $pdo, string $username): bool {
        $stmt = $pdo->prepare(self::sql('existsByUsername.sql'));
        $stmt->execute([':username' => $username]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Check whether an e-mail address is already registered.
     */
    public static function existsByEmail(PDO $pdo, string $email): bool {
        $stmt = $pdo->prepare(self::sql('existsByEmail.sql'));
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

        $stmt = $pdo->prepare(self::sql('register.sql'));
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
        $stmt = $pdo->prepare(self::sql('findById.sql'));
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
        $stmt = $pdo->prepare(self::sql('addFavorite.sql'));
        return $stmt->execute([':user_id' => $userId, ':tune_id' => $tuneId]);
    }

    /**
     * Remove a tune from a user's tunebook.
     */
    public static function removeFavorite(PDO $pdo, int $userId, int $tuneId): bool {
        $stmt = $pdo->prepare(self::sql('removeFavorite.sql'));
        return $stmt->execute([':user_id' => $userId, ':tune_id' => $tuneId]);
    }

    /**
     * Check whether a tune is already in a user's tunebook.
     */
    public static function hasFavorite(PDO $pdo, int $userId, int $tuneId): bool {
        $stmt = $pdo->prepare(self::sql('hasFavorite.sql'));
        $stmt->execute([':user_id' => $userId, ':tune_id' => $tuneId]);
        return (bool) $stmt->fetchColumn();
    }
}
