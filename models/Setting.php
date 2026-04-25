<?php

require_once(__DIR__ . '/Tune.php');

class Setting {

    // ── Edit form ─────────────────────────────────────────────────────────────

    /**
     * Fetch a setting with its parent tune and tune type, for pre-populating
     * the edit form. Returns null if the setting_id doesn't exist.
     */
    public static function getForEdit(PDO $pdo, int $settingId): ?array {
        $stmt = $pdo->prepare("
            SELECT s.setting_id, s.time_signature, s.key_signature,
                   s.default_note_length, s.abc_transcription,
                   s.source, s.origin, s.history, s.book, s.discography,
                   s.transcription_credit, s.area, s.parts, s.tempo, s.lyrics,
                   t.name AS tune_name, tt.name AS tune_type_name
            FROM   setting    s
            JOIN   tune       t  ON  t.tune_id       = s.tune_id
            LEFT JOIN tune_type tt ON tt.tune_type_id = t.tune_type_id
            WHERE  s.setting_id = :id
        ");
        $stmt->execute([':id' => $settingId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // ── Update ────────────────────────────────────────────────────────────────

    /**
     * Apply edits from the edit-setting form.
     *
     * Updates the setting row (time sig, key, note length, ABC body) and
     * optionally the parent tune's name and type. Returns the refreshed
     * setting data so the caller can re-render notation without a page reload,
     * or null if the setting_id doesn't exist.
     */
    public static function update(PDO $pdo, int $settingId, array $data): ?array {
        // Resolve the parent tune
        $stmt = $pdo->prepare("SELECT tune_id FROM setting WHERE setting_id = :id");
        $stmt->execute([':id' => $settingId]);
        $tuneId = (int)$stmt->fetchColumn();

        if (!$tuneId) {
            return null;
        }

        // Update tune name
        if (!empty($data['tune_name'])) {
            $pdo->prepare("UPDATE tune SET name = :name WHERE tune_id = :id")
                ->execute([':name' => $data['tune_name'], ':id' => $tuneId]);
        }

        // Update tune type — get or create the type row
        if (!empty($data['tune_type'])) {
            $tuneTypeId = Tune::getOrCreateType($pdo, $data['tune_type']);
            $pdo->prepare("UPDATE tune SET tune_type_id = :tid WHERE tune_id = :id")
                ->execute([':tid' => $tuneTypeId, ':id' => $tuneId]);
        }

        // Update the setting itself
        $pdo->prepare("
            UPDATE setting
            SET time_signature      = :time_signature,
                key_signature       = :key_signature,
                default_note_length = :default_note_length,
                abc_transcription   = :abc_transcription,
                source              = :source,
                origin              = :origin,
                history             = :history,
                book                = :book,
                discography         = :discography,
                transcription_credit = :transcription_credit,
                area                = :area,
                parts               = :parts,
                tempo               = :tempo,
                lyrics              = :lyrics
            WHERE setting_id = :setting_id
        ")->execute([
            ':time_signature'      => $data['time_signature']      ?? '4/4',
            ':key_signature'       => $data['key_signature']       ?? '',
            ':default_note_length' => $data['default_note_length'] ?? '1/8',
            ':abc_transcription'   => $data['abc_transcription']   ?? '',
            ':source'              => $data['source']              ?: null,
            ':origin'              => $data['origin']              ?: null,
            ':history'             => $data['history']             ?: null,
            ':book'                => $data['book']                ?: null,
            ':discography'         => $data['discography']         ?: null,
            ':transcription_credit' => $data['transcription_credit'] ?: null,
            ':area'                => $data['area']                ?: null,
            ':parts'               => $data['parts']              ?: null,
            ':tempo'               => $data['tempo']              ?: null,
            ':lyrics'              => $data['lyrics']             ?: null,
            ':setting_id'          => $settingId,
        ]);

        // Return refreshed data so JS can re-render notation immediately
        $stmt = $pdo->prepare("
            SELECT s.setting_id, s.time_signature, s.key_signature,
                   s.default_note_length, s.abc_transcription,
                   s.source, s.origin, s.history, s.book, s.discography,
                   s.transcription_credit, s.area, s.parts, s.tempo, s.lyrics,
                   t.name AS tune_name
            FROM setting s
            JOIN tune t ON t.tune_id = s.tune_id
            WHERE s.setting_id = :id
        ");
        $stmt->execute([':id' => $settingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ── Voting ────────────────────────────────────────────────────────────────

    /**
     * Cast or retract a vote on a setting.
     *
     * Voting the same direction twice toggles the vote off (retract).
     * Voting the opposite direction switches the vote.
     *
     * Returns ['setting_id', 'vote_score', 'user_vote'] so the caller can
     * update the UI without a page reload.
     */
    public static function vote(PDO $pdo, int $settingId, int $userId, int $voteValue): array {
        // vote_id is a composite of user_id and setting_id — no AUTO_INCREMENT needed.
        $voteId = $userId * 100000 + $settingId;

        $stmt = $pdo->prepare(
            "SELECT vote_value FROM setting_vote WHERE vote_id = :vote_id"
        );
        $stmt->execute([':vote_id' => $voteId]);
        $existing = $stmt->fetchColumn();

        if ($existing !== false && (int)$existing === $voteValue) {
            // Same direction clicked again — retract the vote
            $pdo->prepare("DELETE FROM setting_vote WHERE vote_id = :vote_id")
                ->execute([':vote_id' => $voteId]);
            $userVote = null;
        } else {
            // New vote or direction switch — upsert
            $pdo->prepare("
                INSERT INTO setting_vote (vote_id, user_id, setting_id, vote_value)
                VALUES (:vote_id, :user_id, :setting_id, :vote_value)
                ON DUPLICATE KEY UPDATE vote_value = VALUES(vote_value)
            ")->execute([
                ':vote_id'    => $voteId,
                ':user_id'    => $userId,
                ':setting_id' => $settingId,
                ':vote_value' => $voteValue,
            ]);
            $userVote = $voteValue;
        }

        // Return updated net score
        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(vote_value), 0) FROM setting_vote WHERE setting_id = :setting_id"
        );
        $stmt->execute([':setting_id' => $settingId]);

        return [
            'setting_id' => $settingId,
            'vote_score' => (int)$stmt->fetchColumn(),
            'user_vote'  => $userVote,
        ];
    }

    // ── Quick lookup ──────────────────────────────────────────────────────────

    /**
     * Fetch a single setting with its parent tune data (for the inline ABC
     * preview / get_tune_body endpoint). Returns null if not found.
     */
    public static function findById(PDO $pdo, int $settingId): ?array {
        $sql  = file_get_contents(__DIR__ . '/../sql/get_setting.sql');
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':param' => $settingId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
