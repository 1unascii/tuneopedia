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
    rs.abc_transcription,
    COALESCE(sc.settings_count, 0) AS settings_count
FROM favorites tb
JOIN tune t ON t.tune_id = tb.tune_id
JOIN tune_type tt ON t.tune_type_id = tt.tune_type_id
LEFT JOIN RankedSettings rs ON t.tune_id = rs.tune_id AND rs.setting_rank = 1
LEFT JOIN (
    SELECT tune_id, COUNT(*) AS settings_count
    FROM setting
    GROUP BY tune_id
) sc ON sc.tune_id = t.tune_id
WHERE tb.user_id = :user_id
ORDER BY t.name ASC
