<?php

class Tune {

    private static function sql(string $filename): string {
        return file_get_contents(__DIR__ . '/sql/tunes/' . $filename);
    }

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
        $sql  = self::sql('getAllGroupedByType.sql');
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
        $statement = $pdo->prepare(self::sql('getFavoritesGroupedByType.sql'));
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
        $stmt = $pdo->prepare(self::sql('getName.sql'));
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
        $sql  = self::sql('getSettings.sql');
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
            $stmt = $pdo->prepare(self::sql('getNotes.sql'));
            $stmt->execute([':tune_id' => $tuneId]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            return [];
        }
    }

    // ── Lookups ───────────────────────────────────────────────────────────────

    public static function getAllTypes(PDO $pdo): array {
        return $pdo->query(self::sql('getAllTypes.sql'))->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllComposers(PDO $pdo): array {
        return $pdo->query(self::sql('getAllComposers.sql'))->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getOrCreateType(PDO $pdo, string $name): int {
        $name = ucwords(strtolower(trim($name)));
        $stmt = $pdo->prepare(self::sql('findTypeByName.sql'));
        $stmt->execute([':name' => $name]);
        $id = $stmt->fetchColumn();
        if ($id) return (int) $id;

        $pdo->prepare(self::sql('createType.sql'))->execute([':name' => $name]);
        return (int) $pdo->lastInsertId();
    }

    public static function getOrCreateComposer(PDO $pdo, string $name): int {
        $stmt = $pdo->prepare(self::sql('findComposerByName.sql'));
        $stmt->execute([':name' => $name]);
        $id = $stmt->fetchColumn();
        if ($id) return (int) $id;

        $pdo->prepare(self::sql('createComposer.sql'))->execute([':name' => $name]);
        return (int) $pdo->lastInsertId();
    }

    // ── Create / Delete ──────────────────────────────────────────────────────

    public static function findByName(PDO $pdo, string $name): ?int {
        $stmt = $pdo->prepare(self::sql('findByName.sql'));
        $stmt->execute([':name' => $name]);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int) $id : null;
    }

    public static function create(
        PDO $pdo, string $name, string $tuneType, string $composer,
        string $metre, string $key, string $body, int $userId,
        ?int $tempo = null, ?string $origin = null, ?string $source = null
    ): int {
        $tuneTypeId = self::getOrCreateType($pdo, $tuneType);
        $composerId = self::getOrCreateComposer($pdo, $composer ?: 'Traditional');

        $stmt = $pdo->prepare(self::sql('createTune.sql'));
        $stmt->execute([
            ':name'         => $name,
            ':tune_type_id' => $tuneTypeId,
            ':composer_id'  => $composerId,
            ':origin'       => $origin,
            ':source'       => $source,
        ]);
        $tuneId = (int) $pdo->lastInsertId();

        if (trim($body) !== '') {
            self::addSetting($pdo, $tuneId, $userId, $name, $metre, $key, $body, $tempo);
        }

        return $tuneId;
    }

    public static function addSetting(
        PDO $pdo, int $tuneId, int $userId, string $name,
        string $metre, string $key, string $body, ?int $tempo = null
    ): int {
        $pdo->prepare(self::sql('createSetting.sql'))->execute([
            ':tune_id'           => $tuneId,
            ':user_id'           => $userId,
            ':name'              => $name,
            ':time_signature'    => $metre,
            ':key_signature'     => $key,
            ':abc_transcription' => $body,
            ':tempo'             => $tempo,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function addNote(PDO $pdo, int $tuneId, string $note): void {
        $pdo->prepare("INSERT INTO tune_note (tune_id, note) VALUES (:tune_id, :note)")
            ->execute([':tune_id' => $tuneId, ':note' => $note]);
    }

    public static function addAlternateTitle(PDO $pdo, int $tuneId, string $name): void {
        $pdo->prepare("INSERT INTO alternate_tune_name (tune_id, name) VALUES (:tune_id, :name)")
            ->execute([':tune_id' => $tuneId, ':name' => $name]);
    }

    public static function delete(PDO $pdo, int $tuneId, int $userId): bool {
        // Delete related data first (no CASCADE on FKs)
        $pdo->prepare(self::sql('deleteTuneNotes.sql'))->execute([':id' => $tuneId]);
        $pdo->prepare(self::sql('deleteFavorites.sql'))->execute([':id' => $tuneId]);
        $pdo->prepare(self::sql('deleteCollectionTunes.sql'))->execute([':id' => $tuneId]);
        $pdo->prepare(self::sql('deleteSettingVotes.sql'))->execute([':id' => $tuneId]);
        $pdo->prepare(self::sql('deleteSettings.sql'))->execute([':id' => $tuneId]);
        $stmt = $pdo->prepare(self::sql('deleteTune.sql'));
        $stmt->execute([':id' => $tuneId]);
        return $stmt->rowCount() > 0;
    }
}
