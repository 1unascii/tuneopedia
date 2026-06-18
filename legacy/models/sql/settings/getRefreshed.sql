SELECT s.setting_id, s.time_signature, s.key_signature,
       s.default_note_length, s.abc_transcription,
       s.source, s.origin, s.history, s.book, s.discography,
       s.transcription_credit, s.area, s.parts, s.tempo, s.lyrics,
       s.instrument_id, COALESCE(i.midi_program, 0) AS midi_program,
       t.name AS tune_name
FROM setting s
JOIN tune t ON t.tune_id = s.tune_id
LEFT JOIN instrument i ON i.instrument_id = s.instrument_id
WHERE s.setting_id = :id
