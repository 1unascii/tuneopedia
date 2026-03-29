SELECT
    c.collection_id,
    c.name              AS collection_name,
    c.author,
    c.publisher,
    c.published_date,
    c.description,
    c.cover_image,
    c.created_at,
    ct.position,
    t.tune_id,
    t.name              AS tune_name,
    s.setting_id,
    s.key_signature,
    s.time_signature,
    s.abc_transcription
FROM collection c
LEFT JOIN collection_tune ct  ON c.collection_id = ct.collection_id
LEFT JOIN tune t               ON ct.tune_id = t.tune_id
LEFT JOIN setting s            ON s.tune_id = t.tune_id
    AND s.setting_id = (
        SELECT MIN(s2.setting_id)
        FROM setting s2
        WHERE s2.tune_id = t.tune_id
    )
ORDER BY c.collection_id, ct.position