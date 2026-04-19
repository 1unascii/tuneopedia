<?php

class Tune {

    // ── Tunebook listing ──────────────────────────────────────────────────────

    /**
     * Fetch every tune with its top-voted setting and group by tune type.
     *
     * Returns a two-element array:
     *   [0] $grouped    — [ tune_type_id => [ ...tune rows ] ]
     *   [1] $typeNames  — [ tune_type_id => type name string ]
     *
     * Used by tunes.php to build the tabbed tunebook view.
     */
    public static function getAllGroupedByType(PDO $pdo, int $userId = 0): array {
        $sql  = file_get_contents(__DIR__ . '/../sql/show_tunebook.sql');
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped   = [];
        $typeNames = [];

        foreach ($rows as $row) {
            $typeId = $row['tune_type_id'];
            if (!isset($grouped[$typeId])) {
                $grouped[$typeId]   = [];
                $typeNames[$typeId] = $row['tune_type_name'];
            }
            $grouped[$typeId][] = $row;
        }

        return [$grouped, $typeNames];
    }

    // ── User favorites ─────────────────────────────────────────────────────────

    /**
     * Fetch a user's favorited tunes with their top-voted setting, grouped by type.
     *
     * Returns the same structure as getAllGroupedByType():
     *   [0] $grouped    — [ tune_type_id => [ ...tune rows ] ]
     *   [1] $typeNames  — [ tune_type_id => type name string ]
     */
    public static function getFavoritesGroupedByType(PDO $pdo, int $userId): array {
        $statement = $pdo->prepare("
            WITH RankedSettings AS (
                SELECT
                    s.tune_id,
                    s.setting_id,
                    s.user_id,
                    s.key_signature,
                    s.time_signature,
                    s.abc_transcription,
                    COALESCE(SUM(v.vote_value), 0) AS net_score,
                    ROW_NUMBER() OVER (
                        PARTITION BY s.tune_id
                        ORDER BY SUM(v.vote_value) DESC, s.setting_id ASC
                    ) AS setting_rank
                FROM setting s
                LEFT JOIN setting_vote v ON s.setting_id = v.setting_id
                GROUP BY s.setting_id
            )
            SELECT
                t.tune_id,
                rs.setting_id,
                t.name AS tune_name,
                tt.tune_type_id,
                tt.name AS tune_type_name,
                rs.key_signature,
                rs.time_signature,
                rs.abc_transcription
            FROM tunebook tb
            JOIN tune t ON t.tune_id = tb.tune_id
            JOIN tune_type tt ON t.tune_type_id = tt.tune_type_id
            LEFT JOIN RankedSettings rs ON t.tune_id = rs.tune_id AND rs.setting_rank = 1
            WHERE tb.user_id = :user_id
            ORDER BY t.name ASC
        ");
        $statement->execute([':user_id' => $userId]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $grouped   = [];
        $typeNames = [];

        foreach ($rows as $row) {
            $typeId = $row['tune_type_id'];
            if (!isset($grouped[$typeId])) {
                $grouped[$typeId]   = [];
                $typeNames[$typeId] = $row['tune_type_name'];
            }
            $grouped[$typeId][] = $row;
        }

        return [$grouped, $typeNames];
    }

    // ── Tune page ─────────────────────────────────────────────────────────────

    /**
     * Fetch the display name of a tune. Returns null if the tune_id doesn't exist.
     */
    public static function getName(PDO $pdo, int $tuneId): ?string {
        $stmt = $pdo->prepare("SELECT name FROM tune WHERE tune_id = :tune_id");
        $stmt->execute([':tune_id' => $tuneId]);
        $name = $stmt->fetchColumn();
        return $name !== false ? $name : null;
    }

    /**
     * Fetch all settings for a tune ordered by vote score descending.
     *
     * $userId is the logged-in user's id (0 for guests) — the query uses it
     * to include the current user's own vote direction (user_vote: 1, -1, null).
     */
    public static function getSettings(PDO $pdo, int $tuneId, int $userId): array {
        $sql  = file_get_contents(__DIR__ . '/../sql/get_tune_settings.sql');
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':tune_id' => $tuneId, ':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch free-text tune-level annotations (tune_note rows).
     * Returns an empty array if there are none or if the table doesn't exist.
     */
    public static function getNotes(PDO $pdo, int $tuneId): array {
        try {
            $stmt = $pdo->prepare("SELECT note FROM tune_note WHERE tune_id = :tune_id");
            $stmt->execute([':tune_id' => $tuneId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            return [];
        }
    }

    // ── Lookups ───────────────────────────────────────────────────────────────

    public static function getAllTypes(PDO $pdo): array {
        return $pdo->query("SELECT * FROM tune_type ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllComposers(PDO $pdo): array {
        return $pdo->query("SELECT * FROM composer ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrCreateType(PDO $pdo, string $name): int {
        $stmt = $pdo->prepare("SELECT tune_type_id FROM tune_type WHERE name = :name LIMIT 1");
        $stmt->execute([':name' => $name]);
        $id = $stmt->fetchColumn();
        if ($id) return (int) $id;

        $pdo->prepare("INSERT INTO tune_type (name) VALUES (:name)")->execute([':name' => $name]);
        return (int) $pdo->lastInsertId();
    }

    public static function getOrCreateComposer(PDO $pdo, string $name): int {
        $stmt = $pdo->prepare("SELECT composer_id FROM composer WHERE name = :name LIMIT 1");
        $stmt->execute([':name' => $name]);
        $id = $stmt->fetchColumn();
        if ($id) return (int) $id;

        $pdo->prepare("INSERT INTO composer (name) VALUES (:name)")->execute([':name' => $name]);
        return (int) $pdo->lastInsertId();
    }

    // ── Create / Delete ──────────────────────────────────────────────────────

    public static function create(
        PDO $pdo, string $name, string $tuneType, string $composer,
        string $metre, string $key, string $body, int $userId
    ): int {
        $tuneTypeId = self::getOrCreateType($pdo, $tuneType);
        $composerId = self::getOrCreateComposer($pdo, $composer ?: 'Traditional');

        $stmt = $pdo->prepare("
            INSERT INTO tune (name, tune_type_id, composer_id)
            VALUES (:name, :tune_type_id, :composer_id)
        ");
        $stmt->execute([
            ':name'         => $name,
            ':tune_type_id' => $tuneTypeId,
            ':composer_id'  => $composerId,
        ]);
        $tuneId = (int) $pdo->lastInsertId();

        $pdo->prepare("
            INSERT INTO setting (tune_id, user_id, name, time_signature, key_signature, abc_transcription)
            VALUES (:tune_id, :user_id, :name, :time_signature, :key_signature, :abc_transcription)
        ")->execute([
            ':tune_id'           => $tuneId,
            ':user_id'           => $userId,
            ':name'              => $name,
            ':time_signature'    => $metre,
            ':key_signature'     => $key,
            ':abc_transcription' => $body,
        ]);

        return $tuneId;
    }

    public static function delete(PDO $pdo, int $tuneId, int $userId): bool {
        $stmt = $pdo->prepare("DELETE FROM tune WHERE tune_id = :tune_id");
        return $stmt->execute([':tune_id' => $tuneId]);
    }
}
