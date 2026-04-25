SELECT
    s.setting_id,
    s.key_signature,
    s.time_signature,
    s.default_note_length,
    s.abc_transcription,
    s.notes                                          AS setting_notes,
    s.source,
    s.origin,
    s.history,
    s.book,
    s.discography,
    s.transcription_credit,
    s.area,
    s.parts,
    s.tempo,
    s.lyrics,
    u.user_name,
    COALESCE(SUM(v.vote_value), 0)                   AS vote_score,
    (
        SELECT sv.vote_value
        FROM   setting_vote sv
        WHERE  sv.setting_id = s.setting_id
          AND  sv.user_id    = :user_id
        LIMIT  1
    )                                                AS user_vote
FROM       setting       s
LEFT JOIN  user          u  ON  u.user_id    = s.user_id
LEFT JOIN  setting_vote  v  ON  v.setting_id = s.setting_id
WHERE  s.tune_id = :tune_id
GROUP  BY  s.setting_id, s.key_signature, s.time_signature,
           s.default_note_length, s.abc_transcription, s.notes,
           s.source, s.origin, s.history, s.book, s.discography,
           s.transcription_credit, s.area, s.parts, s.tempo, s.lyrics,
           u.user_name
ORDER  BY  vote_score DESC, s.setting_id ASC
