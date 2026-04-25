UPDATE setting
SET time_signature      = :time_signature,
    key_signature       = :key_signature,
    default_note_length = :default_note_length,
    abc_transcription   = :abc_transcription,
    source              = :source,
    origin              = :origin,
    history             = :history,
    book                = :book,
    discography         = :discography,
    transcription_credit = :transcription_credit,
    area                = :area,
    parts               = :parts,
    tempo               = :tempo,
    lyrics              = :lyrics
WHERE setting_id = :setting_id
