SELECT
    c.collection_id,
    c.name              AS collection_name,
    c.author,
    c.publisher,
    c.published_date,
    c.description,
    c.cover_image,
    c.created_at,
    c.is_shared,
    c.user_id AS collection_user_id,
    ct.position,
    t.tune_id,
    t.name              AS tune_name,
    t.tune_type_id,
    tt.name             AS tune_type_name,
    s.setting_id,
    s.key_signature,
    s.time_signature,
    s.abc_transcription,
    CASE WHEN tb.tunebook_id IS NOT NULL THEN 1 ELSE 0 END AS is_favorited
FROM collection c
LEFT JOIN collection_tune ct  ON c.collection_id = ct.collection_id
LEFT JOIN tune t               ON ct.tune_id = t.tune_id
LEFT JOIN tune_type tt         ON t.tune_type_id = tt.tune_type_id
LEFT JOIN setting s            ON s.tune_id = t.tune_id
    AND s.setting_id = (
        SELECT MIN(s2.setting_id)
        FROM setting s2
        WHERE s2.tune_id = t.tune_id
    )
LEFT JOIN tunebook tb ON t.tune_id = tb.tune_id AND tb.user_id = :user_id
ORDER BY c.collection_id, ct.position
