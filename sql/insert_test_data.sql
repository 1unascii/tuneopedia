-- Define Tune Types
INSERT INTO tune_type (name) VALUES ('Reel'), ('Jig'), ('Polka'), ('Hornpipe');

-- Define Some Artists
INSERT INTO artist (first_name, last_name) VALUES ('Seán', 'Ó Riada'), ('Mary', 'Bergin'), ('Matt', 'Molloy');

-- Create a few Users
INSERT INTO user (user_name, email, password) VALUES 
('TradFan99', 'fan@example.com', 'hashed_pass_1'),
('MusicScholar', 'scholar@example.com', 'hashed_pass_2');

-- Create a Tune (e.g., The Drowsy Maggie)
INSERT INTO tune (tune_type_id, composer) VALUES (1, 'Traditional');

-- Create an Album
INSERT INTO album (name, cover_art) VALUES ('The Vertical Records', 'cover_01.jpg');

-- Link Artist to Album (Bridge Table)
INSERT INTO artist_album (artist_id, album_id) VALUES (1, 1);

-- Create a Track and link it to the Album AND the Tune
INSERT INTO track (album_id, name, track_number, tune_id) VALUES (1, 'Drowsy Maggie', 1, 1);

-- Map the Track-Tune relationship (Bridge Table)
INSERT INTO tune_track (tune_id, track_id) VALUES (1, 1);

-- Add a musical "Setting" for a tune
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES 
(1, 1, 'Standard Setting', 'EDor', 'E2BE dEBE|E2BE AFDF|...');

-- Create a relationship between two tunes (e.g., related versions)
-- Note: Requires two tune entries; assuming tune_id 1 and a second tune exist
INSERT INTO tune (tune_type_id, composer) VALUES (1, 'Traditional'); -- Tune ID 2
INSERT INTO relationship (tune_id_1, tune_id_2) VALUES (1, 2);

-- Start a Discussion Thread about a tune
INSERT INTO discussion_thread (tune_id, user_id) VALUES (1, 1);

-- Create a Post in that thread
INSERT INTO post (thread_id, user_id, body) VALUES 
(1, 2, 'Does anyone have the sheet music for the B-part of this tune?');

-- Reply to that post
INSERT INTO reply (post_id, user_id, body) VALUES 
(1, 1, 'Check the settings table above! I just uploaded the ABC notation.');


