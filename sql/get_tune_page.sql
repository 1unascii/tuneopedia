SELECT
    t.tune_id,
    t.name AS tune_name,
    tt.name AS tune_type_name,
    rs.setting_id,
    rs.key_signature,
    rs.time_signature,
    rs.default_note_length,
    rs.abc_transcription,
    rs.notes AS setting_notes,
    u.user_name
FROM tune t
JOIN tune_type tt ON t.tune_type_id = tt.tune_type_id
LEFT JOIN (
    SELECT s.setting_id, s.tune_id, s.user_id, s.key_signature, s.time_signature,
           s.default_note_length, s.abc_transcription, s.notes,
           ROW_NUMBER() OVER (
               PARTITION BY s.tune_id
               ORDER BY COALESCE(SUM(v.vote_value), 0) DESC, s.setting_id ASC
           ) AS setting_rank
    FROM setting s
    LEFT JOIN setting_vote v ON s.setting_id = v.setting_id
    GROUP BY s.setting_id
) rs ON t.tune_id = rs.tune_id AND rs.setting_rank = 1
LEFT JOIN user u ON rs.user_id = u.user_id
WHERE t.tune_id = :tune_id
