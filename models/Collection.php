<?php

class Collection {

    // ── Listing ───────────────────────────────────────────────────────────────

    /**
     * Fetch every collection with its tunes nested by tune type.
     *
     * Returns an array keyed by collection_id:
     * [
     *   collection_id => [
     *     'name', 'author', 'publisher', 'published_date',
     *     'description', 'cover_image', 'created_at',
     *     'tunes' => [
     *       tune_type_id => [
     *         'type_id', 'type_name',
     *         'items' => [ ...tune rows ]
     *       ]
     *     ]
     *   ]
     * ]
     *
     * Used by collections.php to build the accordion/tabs view.
     */
    public static function getAllWithTunes(PDO $pdo): array {
        $sql  = file_get_contents(__DIR__ . '/../sql/show_collections.sql');
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $collections = [];

        foreach ($rows as $row) {
            $cid = $row['collection_id'];

            if (!isset($collections[$cid])) {
                $collections[$cid] = [
                    'collection_id'  => $row['collection_id'],
                    'name'           => $row['collection_name'],
                    'author'         => $row['author'],
                    'publisher'      => $row['publisher'],
                    'published_date' => $row['published_date'],
                    'description'    => $row['description'],
                    'cover_image'    => $row['cover_image'],
                    'created_at'     => $row['created_at'],
                    'is_shared'      => (int) $row['is_shared'],
                    'user_id'        => $row['collection_user_id'],
                    'tunes'          => [],
                ];
            }

            if (!$row['tune_id']) {
                continue;
            }

            $typeId   = $row['tune_type_id'] ?: 0;
            $typeName = $row['tune_type_name'] ?: 'Other';

            if (!isset($collections[$cid]['tunes'][$typeId])) {
                $collections[$cid]['tunes'][$typeId] = [
                    'type_id'   => $typeId,
                    'type_name' => $typeName,
                    'items'     => [],
                ];
            }

            $collections[$cid]['tunes'][$typeId]['items'][] = [
                'tune_id'           => $row['tune_id'],
                'tune_name'         => $row['tune_name'],
                'setting_id'        => $row['setting_id'],
                'key_signature'     => $row['key_signature'],
                'time_signature'    => $row['time_signature'],
                'abc_transcription' => $row['abc_transcription'],
                'position'          => $row['position'],
            ];
        }

        return $collections;
    }

    // ── Creation ──────────────────────────────────────────────────────────────

    /**
     * Check whether a collection with this exact name already exists.
     */
    public static function existsByName(PDO $pdo, string $name): bool {
        $stmt = $pdo->prepare(
            "SELECT collection_id FROM collection WHERE name = :name LIMIT 1"
        );
        $stmt->execute([':name' => $name]);
        return (bool)$stmt->fetch();
    }

    /**
     * Insert a new collection row and return its new collection_id.
     */
    public static function create(PDO $pdo, string $name, string $author, string $description, bool $isShared = false, ?int $userId = null): int {
        $stmt = $pdo->prepare("
            INSERT INTO collection (user_id, name, author, description, is_shared, created_at)
            VALUES (:user_id, :name, :author, :description, :is_shared, NOW())
        ");
        $stmt->execute([
            ':user_id'     => $userId,
            ':name'        => $name,
            ':author'      => $author,
            ':description' => $description,
            ':is_shared'   => $isShared ? 1 : 0,
        ]);
        return (int)$pdo->lastInsertId();
    }

    // ── Link tunes ───────────────────────────────────────────────────────────

    /**
     * Link an array of tune IDs to a collection with sequential positions.
     */
    public static function addTunes(PDO $pdo, int $collectionId, array $tuneIds): void {
        $statement = $pdo->prepare("
            INSERT INTO collection_tune (collection_id, tune_id, position)
            VALUES (:collection_id, :tune_id, :position)
        ");
        $position = 1;
        foreach ($tuneIds as $tuneId) {
            $statement->execute([
                ':collection_id' => $collectionId,
                ':tune_id'       => (int) $tuneId,
                ':position'      => $position,
            ]);
            $position++;
        }
    }
}
