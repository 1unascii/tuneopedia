SELECT s.setting_id, s.tune_id, s.user_id, s.name,
       s.time_signature, s.key_signature,
       s.default_note_length, s.abc_transcription, s.notes,
       s.source, s.origin, s.history, s.book, s.discography,
       s.transcription_credit, s.area, s.parts, s.tempo, s.lyrics,
       t.name AS tune_name, tt.name AS tune_type_name
FROM   setting    s
JOIN   tune       t  ON  t.tune_id       = s.tune_id
LEFT JOIN tune_type tt ON tt.tune_type_id = t.tune_type_id
WHERE  s.setting_id = :id
