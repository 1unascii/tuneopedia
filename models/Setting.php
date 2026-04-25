<?php

require_once(__DIR__ . '/Tune.php');

class Setting {

    private static function sql(string $filename): string {
        return file_get_contents(__DIR__ . '/sql/settings/' . $filename);
    }

    // ── Edit form ─────────────────────────────────────────────────────────────

    /**
     * Fetch a setting with its parent tune and tune type, for pre-populating
     * the edit form. Returns null if the setting_id doesn't exist.
     */
    public static function getForEdit(PDO $pdo, int $settingId): ?array {
        $stmt = $pdo->prepare(self::sql('getForEdit.sql'));
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
        $stmt = $pdo->prepare(self::sql('getTuneId.sql'));
        $stmt->execute([':id' => $settingId]);
        $tuneId = (int)$stmt->fetchColumn();

        if (!$tuneId) {
            return null;
        }

        // Update tune name
        if (!empty($data['tune_name'])) {
            $pdo->prepare(self::sql('updateTuneName.sql'))
                ->execute([':name' => $data['tune_name'], ':id' => $tuneId]);
        }

        // Update tune type — get or create the type row
        if (!empty($data['tune_type'])) {
            $tuneTypeId = Tune::getOrCreateType($pdo, $data['tune_type']);
            $pdo->prepare(self::sql('updateTuneType.sql'))
                ->execute([':tid' => $tuneTypeId, ':id' => $tuneId]);
        }

        // Update the setting itself
        $pdo->prepare(self::sql('update.sql'))->execute([
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
        $stmt = $pdo->prepare(self::sql('getRefreshed.sql'));
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

        $stmt = $pdo->prepare(self::sql('getVote.sql'));
        $stmt->execute([':vote_id' => $voteId]);
        $existing = $stmt->fetchColumn();

        if ($existing !== false && (int)$existing === $voteValue) {
            // Same direction clicked again — retract the vote
            $pdo->prepare(self::sql('deleteVote.sql'))
                ->execute([':vote_id' => $voteId]);
            $userVote = null;
        } else {
            // New vote or direction switch — upsert
            $pdo->prepare(self::sql('upsertVote.sql'))->execute([
                ':vote_id'    => $voteId,
                ':user_id'    => $userId,
                ':setting_id' => $settingId,
                ':vote_value' => $voteValue,
            ]);
            $userVote = $voteValue;
        }

        // Return updated net score
        $stmt = $pdo->prepare(self::sql('getVoteScore.sql'));
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
        $stmt = $pdo->prepare(self::sql('findById.sql'));
        $stmt->execute([':param' => $settingId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
