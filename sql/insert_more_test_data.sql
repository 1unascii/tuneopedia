-- Artists
INSERT INTO artist (first_name, last_name) VALUES 
('Matt', 'Molloy'), ('Kevin', 'Burke'), ('Micheál', 'Ó Súilleabháin'), ('The', 'Bothy Band');

-- Albums
INSERT INTO album (name, cover_art) VALUES 
('Heathery Breeze', 'heathery.jpg'), 
('If the Cap Fits', 'cap_fits.jpg'), 
('1975', 'bothy_1975.jpg');

-- Linking Artists to Albums (Many-to-Many)
INSERT INTO artist_album (artist_id, album_id) VALUES 
(4, 1), -- Matt Molloy (as part of a group) on Heathery Breeze
(5, 2), -- Kevin Burke on If the Cap Fits
(7, 3); -- The Bothy Band on 1975

-- Tune 2: The Wind That Shakes the Barley (Reel)
INSERT INTO tune (tune_type_id, composer) VALUES (1, 'Traditional'); 
-- Tune 3: The Kesh Jig (Jig)
INSERT INTO tune (tune_type_id, composer) VALUES (2, 'Traditional'); 
-- Tune 4: Morrison's Jig (Jig)
INSERT INTO tune (tune_type_id, composer) VALUES (2, 'James Morrison'); 

-- Track for Tune 2
INSERT INTO track (album_id, name, track_number, tune_id) VALUES (2, 'Wind That Shakes the Barley', 4, 2);
-- Track for Tune 3
INSERT INTO track (album_id, name, track_number, tune_id) VALUES (4, 'The Kesh Jig', 1, 3);
-- Track for Tune 4
INSERT INTO track (album_id, name, track_number, tune_id) VALUES (3, 'Morrisons', 8, 4);

-- Settings for Tune 2 (Wind That Shakes the Barley)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES 
(2, 1, 'Standard Reel', 'D', 'B2 BA B2 BA | dBBA B2 BA | d2 fd e2 fe | dBAF E2 D2 |'),
(2, 2, 'Sligo Variation', 'D', 'f~d3 B2BA | G2BG FAA2 | feed e2fe | dBAF E2D2 |'),
(2, 1, 'Ornamented', 'D', 'A2AB AF~F2 | ABde fded | Beed e2de | f2af edBd |');

-- Settings for Tune 3 (The Kesh Jig)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES 
(3, 1, 'Basic Jig', 'G', 'G3 GAB | A3 ABd | edB gdB | BAG ABA |'),
(3, 2, 'Session Version', 'G', '~G3 GAB | ~A3 ABd | edB gdB | AGF G2A |'),
(3, 1, 'Double Jig Style', 'G', 'G2G GAB | A2A ABd | ege dBA | BGG G2D |');

-- Settings for Tune 4 (Morrison's Jig)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES 
(4, 2, 'Traditional', 'EDor', 'E2BE cEBE | E2BE AFDF | E2BE cEBE | BABd AFD2 |'),
(4, 1, 'High Octave', 'EDor', 'bee bee | afe d2f | gfe dBA | BAG FGA |'),
(4, 2, 'Fast Session', 'EDor', '~E3 ~B3 | ~E3 AFD | GBG FAF | GFE FED |');

INSERT INTO tunebook (user_id, tune_id) VALUES 
(1, 3), -- TradFan99 adds The Kesh Jig
(1, 4); -- TradFan99 adds Morrison's Jig

-- TradFan99 (User 1) likes "Standard Reel" (Setting 2)
INSERT INTO setting_vote (user_id, setting_id, vote_value) VALUES (1, 2, 1);

-- MusicScholar (User 2) dislikes "Fast Session" (Setting 9)
INSERT INTO setting_vote (user_id, setting_id, vote_value) VALUES (2, 9, -1);

INSERT INTO user (user_name, email, password) VALUES 
('FiddleMaster', 'fiddle@example.com', 'pass1'),
('TinWhistleGuy', 'whistle@example.com', 'pass2'),
('BodhranBeats', 'drum@example.com', 'pass3'),
('FluteFan', 'flute@example.com', 'pass4'),
('Harpist92', 'harp@example.com', 'pass5'),
('PiperPro', 'pipes@example.com', 'pass6');

INSERT INTO tune (tune_type_id, composer) VALUES 
(1, 'Traditional'), -- The Banshee (Reel)
(1, 'Traditional'), -- Drowsy Maggie (Reel)
(2, 'Traditional'), -- The Butterfly (Slip Jig)
(1, 'Traditional'), -- Star of Munster (Reel)
(4, 'Traditional'); -- Harvest Home (Hornpipe)

-- Settings for The Banshee (Tune ID 5)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES 
(5, 3, 'Standard Reel', 'G', 'G2 GD GABd | edBd edBA | G2 GD GABd | edBA GEDE |'),
(5, 6, 'Ornamented', 'G', '~G3 GD GABd | edBd edBA | ~G3 GD GABd | edBA GEDE |');

-- Settings for The Butterfly (Tune ID 7)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES 
(7, 5, 'Basic Slip Jig', 'Em', 'B2E G2E F3 | B2E G2E FED | B2E G2E F2A | B2c d2B AFD |'),
(7, 1, 'Extended Variation', 'Em', 'B2E G2E F3 | B2E G2E FED | G2B d2B BAB | d2B G2B AFD |'),
(7, 4, 'Session Version', 'Em', 'B2E G2E FGA | B2E G2E FED | B2E G2E F2A | B2c d2B AFD |');

-- Settings for Harvest Home (Tune ID 9)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES 
(9, 2, 'Hornpipe Style', 'D', 'A2FA DAFA | defe dcBA | e2ce A2ce | f2df A2df |'),
(9, 6, 'Sligo Style', 'D', 'A2FA DAFA | defe dcBA | e2ce A2ce | afge d2 :|');

-- Votes for "The Banshee" Standard Reel (Setting ID 10)
INSERT INTO setting_vote (user_id, setting_id, vote_value) VALUES 
(1, 10, 1), (2, 10, 1), (4, 10, 1), (5, 10, -1);

-- Votes for "The Butterfly" Basic Slip Jig (Setting ID 12)
INSERT INTO setting_vote (user_id, setting_id, vote_value) VALUES 
(2, 12, 1), (3, 12, 1), (6, 12, 1);

-- Votes for "Harvest Home" Hornpipe Style (Setting ID 15)
INSERT INTO setting_vote (user_id, setting_id, vote_value) VALUES 
(1, 15, 1), (4, 15, -1), (5, 15, 1), (3, 15, 1);

INSERT INTO setting_vote (user_id, setting_id, vote_value)
SELECT * FROM (
    SELECT 
        u.user_id, 
        s.setting_id, 
        IF(RAND() > 0.5, 1, -1) AS val
    FROM user u
    CROSS JOIN setting s
) AS tmp
ON DUPLICATE KEY UPDATE vote_value = tmp.val;
