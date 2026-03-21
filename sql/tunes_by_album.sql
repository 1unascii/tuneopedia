SELECT 
    a.name AS album_name,
    GROUP_CONCAT(DISTINCT CONCAT(art.first_name, ' ', art.last_name) SEPARATOR ' & ') AS artist_names,
    t.track_number,
    t.name AS track_title,
    tt.name AS tune_type,
    tn.composer
FROM album a
-- Link to Artists via your new bridge table
LEFT JOIN artist_album aa ON a.album_id = aa.album_id
LEFT JOIN artist art ON aa.artist_id = art.artist_id
-- Link to Tracks
JOIN track t ON a.album_id = t.album_id
-- Link to the Tune definitions
JOIN tune tn ON t.tune_id = tn.tune_id
JOIN tune_type tt ON tn.tune_type_id = tt.tune_type_id
GROUP BY a.album_id, t.track_id
ORDER BY a.name, t.track_number;