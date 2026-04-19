WITH RankedSettings AS (
    SELECT 
        s.tune_id,
        s.setting_id, -- <--- Added this
        s.user_id,
        s.key_signature,
        s.abc_transcription,
        
        COALESCE(SUM(v.vote_value), 0) AS net_score,
        ROW_NUMBER() OVER (
            PARTITION BY s.tune_id 
            ORDER BY SUM(v.vote_value) DESC, s.setting_id ASC
        ) as setting_rank
    FROM setting s
    LEFT JOIN setting_vote v ON s.setting_id = v.setting_id
    GROUP BY s.setting_id
)
SELECT 
    t.tune_id,
    rs.setting_id AS setting_id, -- <--- Now available in the final result
    t.name AS tune_name,
    t.composer,
    tt.tune_type_id,
    tt.name AS tune_type_name,
    rs.key_signature,
    rs.abc_transcription,
    
    -- Added the username join here
    rs.user_id AS user_id,
    u.user_name AS user_name,
    COALESCE(rs.net_score, 0) AS setting_popularity,
    CASE WHEN tb.tunebook_id IS NOT NULL THEN 1 ELSE 0 END AS is_favorited
FROM tune t
JOIN tune_type tt ON t.tune_type_id = tt.tune_type_id
LEFT JOIN RankedSettings rs ON t.tune_id = rs.tune_id AND rs.setting_rank = 1
-- Link to the user table to get the name
LEFT JOIN user u ON rs.user_id = u.user_id
LEFT JOIN tunebook tb ON t.tune_id = tb.tune_id AND tb.user_id = :user_id
ORDER BY t.name ASC;