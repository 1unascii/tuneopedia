SELECT 
    t.tune_id,
    -- Replacing tune_id with the tune name (assuming 'name' exists in tune table)
    t.name AS tune_title, 
    tt.name AS tune_type,
    s.name AS setting_name,
    s.key_signature,
    COALESCE(SUM(v.vote_value), 0) AS net_score,
    COUNT(v.vote_id) AS total_votes
FROM tune t
JOIN tune_type tt ON t.tune_type_id = tt.tune_type_id
JOIN setting s ON t.tune_id = s.tune_id
LEFT JOIN setting_vote v ON s.setting_id = v.setting_id
GROUP BY s.setting_id
-- 1. Sort by Tune Title (A-Z)
-- 2. Sort by Score (High to Low)
ORDER BY tune_title ASC, net_score DESC;
