-- =====================
-- 2. USERS
-- =====================
INSERT INTO user (user_name, email, password) VALUES ('TradFan99', 'fan@example.com', 'hashed_pass_1');
SET @u1 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('MusicScholar', 'scholar@example.com', 'hashed_pass_2');
SET @u2 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('FiddleMaster', 'fiddle@example.com', 'pass1');
SET @u3 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('TinWhistleGuy', 'whistle@example.com', 'pass2');
SET @u4 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('BodhranBeats', 'drum@example.com', 'pass3');
SET @u5 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('FluteFan', 'flute@example.com', 'pass4');
SET @u6 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('Harpist92', 'harp@example.com', 'pass5');
SET @u7 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('PiperPro', 'pipes@example.com', 'pass6');
SET @u8 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('ReelKing', 'reel@example2.com', 'pass7');
SET @u9 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('JigQueen', 'jig@example2.com', 'pass8');
SET @u10 = LAST_INSERT_ID();

INSERT INTO user (user_name, email, password) VALUES ('SlipJigSam', 'slip@example2.com', 'pass9');
SET @u11 = LAST_INSERT_ID();

-- =====================
-- 3. ARTISTS & ALBUMS
-- =====================
INSERT INTO artist (first_name, last_name) VALUES ('Seán', 'Ó Riada');
SET @art_riada = LAST_INSERT_ID();

INSERT INTO artist (first_name, last_name) VALUES ('Matt', 'Molloy');
SET @art_molloy = LAST_INSERT_ID();

INSERT INTO album (name, cover_art) VALUES ('The Vertical Records', 'cover_01.jpg');
SET @alb_vertical = LAST_INSERT_ID();

INSERT INTO artist_album (artist_id, album_id) VALUES (@art_riada, @alb_vertical);

-- =====================
-- 4. TUNES
-- =====================
INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Drowsy Maggie', 1, 'Traditional');
SET @t_drowsy = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Wind That Shakes the Barley', 1, 'Traditional');
SET @t_wind = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Kesh Jig', 2, 'Traditional');
SET @t_kesh = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Morrison''s Jig', 2, 'James Morrison');
SET @t_morrison = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Banshee', 1, 'Traditional');
SET @t_banshee = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Butterfly', 5, 'Traditional');
SET @t_butterfly = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Harvest Home', 4, 'Traditional');
SET @t_harvest = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Morning Dew', 1, 'Traditional');
SET @t_morning = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Silver Spear', 1, 'Traditional');
SET @t_silver = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Flogging Reel', 1, 'Traditional');
SET @t_flogging = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Tarbolton', 1, 'Traditional');
SET @t_tarbolton = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Sally Gardens', 1, 'Traditional');
SET @t_sally = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Connaughtman''s Rambles', 2, 'Traditional');
SET @t_connaughtman = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Lilting Banshee', 2, 'Traditional');
SET @t_lilting = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Paddy Clancy''s Jig', 2, 'Traditional');
SET @t_paddy = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Rocky Road to Dublin', 2, 'Traditional');
SET @t_rocky = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Foxhunter''s Reel', 1, 'Traditional');
SET @t_foxhunter = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Gold Ring', 5, 'Traditional');
SET @t_goldring = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Drops of Brandy', 5, 'Traditional');
SET @t_drops = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Stack of Barley', 3, 'Traditional');
SET @t_stack = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Galway Rambler', 3, 'Traditional');
SET @t_galway = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Londonderry Hornpipe', 4, 'Traditional');
SET @t_londonderry = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Mason''s Apron', 1, 'Traditional');
SET @t_mason = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Sailor''s Bonnet', 1, 'Traditional');
SET @t_sailor = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Jackson''s Reel', 1, 'Traditional');
SET @t_jackson = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Longford Collector', 1, 'Traditional');
SET @t_longford = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Maid Behind the Bar', 1, 'Traditional');
SET @t_maid = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Off to California', 1, 'Traditional');
SET @t_california = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Templehouse Reel', 1, 'Traditional');
SET @t_templehouse = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Tom Billy''s Reel', 1, 'Traditional');
SET @t_tombilly = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Contradiction Reel', 1, 'Traditional');
SET @t_contradiction = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Pipe on the Hob', 2, 'Traditional');
SET @t_pipe = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Humours of Tulla', 2, 'Traditional');
SET @t_humours = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Saddle the Pony', 2, 'Traditional');
SET @t_saddle = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Geese in the Bog', 2, 'Traditional');
SET @t_geese = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Banish Misfortune', 2, 'Traditional');
SET @t_banish = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Garryowen', 2, 'Traditional');
SET @t_garryowen = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Fermoy Lasses', 2, 'Traditional');
SET @t_fermoy = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Tell Her I Am', 2, 'Traditional');
SET @t_tellher = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Sean Sa Cheo', 2, 'Traditional');
SET @t_sean = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Geese in the Bog', 2, 'Traditional');
SET @t_geese2 = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Rights of Man', 4, 'Traditional');
SET @t_rights = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Boys of Bluehill', 4, 'Traditional');
SET @t_bluehill = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The King of the Fairies', 4, 'Traditional');
SET @t_king = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Ballydesmond Polka', 3, 'Traditional');
SET @t_bally = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Denis Murphy''s Polka', 3, 'Traditional');
SET @t_denis = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('John Ryan''s Polka', 3, 'Traditional');
SET @t_johnryan = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('The Kid on the Mountain', 5, 'Traditional');
SET @t_kid = LAST_INSERT_ID();

INSERT INTO tune (name, tune_type_id, composer) VALUES ('Hardiman the Fiddler', 5, 'Traditional');
SET @t_hardiman = LAST_INSERT_ID();

-- =====================
-- 5. SETTINGS
-- =====================

-- The Drowsy Maggie (3 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_drowsy, @u1, 'Standard', 'EDor', '4/4', 'E2BE dEBE|E2BE AFDF|E2BE dEBE|BABd AFD2|'),
(@t_drowsy, @u2, 'Ornamented', 'EDor', '4/4', '~E3B dEBE|~E3B AFDF|~E3B dEBE|BABd AFD2|'),
(@t_drowsy, @u3, 'Low Octave', 'EDor', '4/4', 'E,2B,E B,EB,E|E,2B,E AFDF|E,2B,E B,EB,E|BABd AFD2|');

-- The Wind That Shakes the Barley (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_wind, @u1, 'Standard', 'D', '4/4', 'B2 BA B2 BA|dBBA B2 BA|d2 fd e2 fe|dBAF E2D2|'),
(@t_wind, @u2, 'Sligo Style', 'D', '4/4', 'f~d3 B2BA|G2BG FAA2|feed e2fe|dBAF E2D2|');

-- The Kesh Jig (3 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_kesh, @u1, 'Standard', 'G', '6/8', 'G3 GAB|A3 ABd|edB gdB|BAG ABA|'),
(@t_kesh, @u3, 'Session Version', 'G', '6/8', '~G3 GAB|~A3 ABd|edB gdB|AGF G2A|'),
(@t_kesh, @u5, 'Clare Style', 'G', '6/8', 'G2G GAB|A2A ABd|ege dBA|BGG G2D|');

-- Morrison's Jig (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_morrison, @u1, 'Standard', 'EDor', '6/8', 'E2BE cEBE|E2BE AFDF|E2BE cEBE|BABd AFD2|'),
(@t_morrison, @u4, 'High Octave', 'EDor', '6/8', 'e2be cebe|e2be afdf|e2be cebe|babD AFD2|');

-- The Banshee (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_banshee, @u3, 'Standard', 'G', '4/4', 'G2GD GABd|edBd edBA|G2GD GABd|edBA GEDE|'),
(@t_banshee, @u6, 'Ornamented', 'G', '4/4', '~G3D GABd|edBd edBA|~G3D GABd|edBA GEDE|');

-- The Butterfly (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_butterfly, @u5, 'Standard', 'Em', '9/8', 'B2E G2E F3|B2E G2E FED|B2E G2E F2A|B2c d2B AFD|'),
(@t_butterfly, @u11, 'Session Version', 'Em', '9/8', 'B2E G2E FGA|B2E G2E FED|B2E G2E F2A|B2c d2B AFD|');

-- Harvest Home (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_harvest, @u2, 'Standard', 'D', '12/8', 'A2FA DAFA|defe dcBA|e2ce A2ce|f2df A2df|');

-- The Morning Dew (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_morning, @u1, 'Standard', 'D', '4/4', 'dAFA dAFA|BGGF G2AB|cAAG AGEG|AGED CDEC|'),
(@t_morning, @u8, 'Ornamented', 'D', '4/4', '~d3A ~f3A|BGGF G2AB|~c3A ~A3G|AGED CDEC|');

-- The Silver Spear (3 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_silver, @u2, 'Standard', 'D', '4/4', 'A2FA B2FB|A2FA BAFA|A2FA B2dB|AFEG FDD2|'),
(@t_silver, @u5, 'High Version', 'D', '4/4', 'a2fa b2fb|a2fa bafa|a2fa b2db|afeg fdd2|'),
(@t_silver, @u9, 'Session Cut', 'D', '4/4', 'A2FA BAFA|d2fd edBA|~f3e dBAF|AFEG FDD2|');

-- The Flogging Reel (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_flogging, @u1, 'Standard', 'G', '4/4', 'G2BG dGBG|G2BG dBAG|FADA fADA|FADE FDD2|');

-- The Tarbolton (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_tarbolton, @u6, 'Standard', 'Amix', '4/4', 'eAAG A2Bd|eAAG A2Bd|eaag aged|BGGF GEEd|'),
(@t_tarbolton, @u2, 'Connacht Style', 'Amix', '4/4', '~e3G A2Bd|~e3G A2Bd|eaag a2ge|BGGF GEE2|');

-- The Sally Gardens (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_sally, @u3, 'Standard', 'G', '4/4', 'D2GG AGEG|D2GG A2Bd|e2ee edBA|BAGE EDDE|');

-- The Connaughtman's Rambles (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_connaughtman, @u1, 'Standard', 'G', '6/8', 'G2D G2D GAB|cBc ABc d2A|BdB cAc BGE|GAB cAF G3|'),
(@t_connaughtman, @u5, 'Session Version', 'G', '6/8', '~G3 ~G3 GAB|~c3 ABc d2A|BdB cAc BGE|GAB cAF ~G3|');

-- The Lilting Banshee (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_lilting, @u2, 'Standard', 'G', '6/8', 'G2|DGGF G2AB|cBAG AGEG|DGGF G2Bd|egdB A2|');

-- Paddy Clancy's Jig (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_paddy, @u4, 'Standard', 'D', '6/8', 'dAF DFA d2|edc BAG F2|dAF DFA dA|BAG FAD D2|'),
(@t_paddy, @u1, 'Clare Style', 'D', '6/8', '~d3 DFA d2|edc BAG ~F3|~d3 DFA dA|BAG FAD ~D3|');

-- The Rocky Road to Dublin (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_rocky, @u3, 'Standard', 'Edor', '6/8', 'EBBA B2EB|B2AB dBAG|FDAD BDAD|FDAD dAFD|'),
(@t_rocky, @u7, 'Fast Session', 'Edor', '6/8', '~E3A B2EB|B2AB dBAG|~F3D BDAD|~F3D dAFD|');

-- The Foxhunter's Reel (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_foxhunter, @u2, 'Standard', 'G', '4/4', 'dGGB d2Bd|gfed BdcB|AGAB cBAG|FGAF GFED|');

-- The Gold Ring (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_goldring, @u6, 'Standard', 'Edor', '9/8', 'EBBA B2ef|g2fe dBAF|DEFA B2ef|gedB A3|'),
(@t_goldring, @u3, 'Session Version', 'Edor', '9/8', '~E3A B2ef|~g3e dBAF|DEFA B2ef|gedB ~A3|');

-- The Drops of Brandy (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_drops, @u1, 'Standard', 'G', '9/8', 'G3 ABd|e2dB A2|G3 GAB|dBAG E2|');

-- The Stack of Barley (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_stack, @u2, 'Standard', 'G', '3/4', 'GE|DGGF G2AB|cBAG AGED|DGGF G2Bd|egdB A2|'),
(@t_stack, @u7, 'Kerry Style', 'G', '3/4', 'GE|DGGF G2AB|cBAG AGED|DGGF G2Bd|egdB A2|');

-- The Galway Rambler (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_galway, @u5, 'Standard', 'D', '3/4', 'FA|dFFE F2GA|BAGF EDCE|dFFE F2AB|dBAG FDD2|');

-- The Londonderry Hornpipe (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_londonderry, @u1, 'Standard', 'D', '4/4', 'A2FA d2fd|e2ce a2ea|f2df a2fa|gfed cBAG|'),
(@t_londonderry, @u6, 'Ornamented', 'D', '4/4', 'A2FA ~d3f|e2ce ~a3e|~f3d ~a3f|gfed cBAG|');

-- The Mason's Apron (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_mason, @u1, 'Standard', 'D', '4/4', 'd2fd edfd|d2fd edBA|d2fd edfe|dBAF GFED|'),
(@t_mason, @u3, 'Ornamented', 'D', '4/4', '~d3f edfd|~d3f edBA|~d3f edfe|dBAF GFED|');

-- The Sailor's Bonnet (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_sailor, @u4, 'Standard', 'G', '4/4', 'g2dg egdg|g2dg edBA|GBdg egdg|edBA GEE2|');

-- Jackson's Reel (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_jackson, @u2, 'Standard', 'D', '4/4', 'AFDF AFDF|AFdF AFDE|FAAF dfed|BAFA d2ed|'),
(@t_jackson, @u9, 'High Octave', 'D', '4/4', 'afdf afdf|afdf afde|faaf dfed|bafa d2ed|');

-- The Longford Collector (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_longford, @u5, 'Standard', 'D', '4/4', 'fded cAGE|FGAF GFED|fded cABc|dfec d2ed|');

-- The Maid Behind the Bar (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_maid, @u2, 'Standard', 'D', '4/4', 'd2AD BDAD|d2AG FDD2|d2AD BdAF|GFGE FDD2|'),
(@t_maid, @u4, 'Sligo Style', 'D', '4/4', '~d3A BDAD|~d3G FDD2|~d3A BdAF|GFGE FDD2|');

-- Off to California (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_california, @u3, 'Standard', 'D', '4/4', 'ADFA dAFA|ADFA GEED|ADFA dAFd|edBA FDD2|');

-- The Templehouse Reel (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_templehouse, @u1, 'Standard', 'Edor', '4/4', 'EFGA Beed|efge dBAG|EFGA Beed|BAGA BEE2|'),
(@t_templehouse, @u5, 'Session Version', 'Edor', '4/4', '~E3A Beed|efge dBAG|~E3A Beed|BAGA ~B3|');

-- Tom Billy's Reel (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_tombilly, @u2, 'Standard', 'G', '4/4', 'BGAG FGAF|GBAG FDD2|BGAG FGAd|BAGA GEE2|');

-- The Contradiction Reel (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_contradiction, @u1, 'Standard', 'Amix', '4/4', 'eAAG A2cd|eage dBGB|eAAG A2cd|edge dBA2|'),
(@t_contradiction, @u3, 'Connacht Style', 'Amix', '4/4', '~e3G A2cd|eage dBGB|~e3G A2cd|edge dB~A3|');

-- The Pipe on the Hob (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_pipe, @u1, 'Standard', 'D', '6/8', 'dAA ABd|efe dAG|FAA ABd|efe d3|');

-- The Humours of Tulla (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_humours, @u2, 'Standard', 'Edor', '6/8', 'EFGA Beed|Beed B2AB|EFGA Beed|BAGA BEE2|'),
(@t_humours, @u5, 'Clare Style', 'Edor', '6/8', '~E3A Beed|Beed B2AB|~E3A Beed|BAGA ~B3|');

-- Saddle the Pony (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_saddle, @u3, 'Standard', 'G', '6/8', 'GFG GAB|dBd dBG|GFG GAB|dge d3|'),
(@t_saddle, @u6, 'Session Cut', 'G', '6/8', '~G3 GAB|dBd dBG|~G3 GAB|dge ~d3|');

-- The Geese in the Bog (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_geese, @u1, 'Standard', 'Edor', '6/8', 'BEED E2FG|ABcA BEED|BEED E2FG|ABcA BEE2|');

-- Banish Misfortune (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_banish, @u2, 'Standard', 'Dmix', '6/8', 'dcd AGE|GED GAG|dcd AGE|GEA GEE|'),
(@t_banish, @u7, 'Ornamented', 'Dmix', '6/8', '~d3 AGE|GED GAG|~d3 AGE|GEA ~G3|');

-- Garryowen (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_garryowen, @u1, 'Standard', 'G', '6/8', 'GEE GEE|GBd dBG|GEE GEE|DFA d3|'),
(@t_garryowen, @u4, 'Session Version', 'G', '6/8', '~G3 ~G3|GBd dBG|~G3 ~G3|DFA ~d3|');

-- The Fermoy Lasses (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_fermoy, @u2, 'Standard', 'G', '6/8', 'GBdg gdBd|GBdg dBAG|GBdg gdBd|egdB A2GE|');

-- Tell Her I Am (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_tellher, @u1, 'Standard', 'D', '6/8', 'fdd dcd|fdd dAG|fdd dcd|ecA AGE|'),
(@t_tellher, @u4, 'Clare Style', 'D', '6/8', '~f3 dcd|~f3 dAG|~f3 dcd|ecA AGE|');

-- Sean Sa Cheo (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_sean, @u5, 'Standard', 'Edor', '6/8', 'BEED EFGE|BEED B2AB|GFGA BAGE|DEGA BEE2|');

-- The Rights of Man (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_rights, @u1, 'Standard', 'Edor', '4/4', 'e2fe dBAG|FGAF GFED|E2FE DEFA|Beed B2AG|'),
(@t_rights, @u3, 'Ornamented', 'Edor', '4/4', 'e2fe dBAG|~F3F GFED|E2FE DEFA|Beed B2AG|');

-- The Boys of Bluehill (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_bluehill, @u4, 'Standard', 'G', '4/4', 'G2BG dGBG|cBAc BGGE|DGGF GABc|dBAG GEDE|');

-- The King of the Fairies (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_king, @u3, 'Standard', 'Edor', '4/4', 'BEED EFGE|DEED DEGA|BEED EFGE|dBAG BEE2|'),
(@t_king, @u5, 'Session Cut', 'Edor', '4/4', '~B3D EFGE|~D3D DEGA|~B3D EFGE|dBAG ~B3|');

-- The Ballydesmond Polka (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_bally, @u1, 'Standard', 'D', '3/4', 'AFDF AFDF|AFDF AGFE|AFDF AFAd|BAFA d2ed|'),
(@t_bally, @u3, 'Kerry Style', 'D', '3/4', 'AFDF AFDF|AFDF AGFE|AFDF AFAd|BAFA d2ed|');

-- Denis Murphy's Polka (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_denis, @u2, 'Standard', 'D', '3/4', 'dAFA dAFA|dAFA GEE2|dAFA dAFd|edBA FDD2|');

-- John Ryan's Polka (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_johnryan, @u2, 'Standard', 'D', '3/4', 'e2fe dBAF|E2FE DEFA|d2fd edBA|BAFA d2ed|'),
(@t_johnryan, @u7, 'Kerry Style', 'D', '3/4', 'e2fe dBAF|E2FE DEFA|d2fd edBA|BAFA d2ed|');

-- The Kid on the Mountain (1 setting)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_kid, @u2, 'Standard', 'Edor', '9/8', 'B2E E2F G2E|B2E E2G FED|B2E E2F GFG|A2B ABA GED|');

-- Hardiman the Fiddler (2 settings)
INSERT INTO setting (tune_id, user_id, name, key_signature, time_signature, abc_transcription) VALUES
(@t_hardiman, @u2, 'Standard', 'G', '9/8', 'G2B d2B cBA|G2B d2g fed|G2B d2B cBc|d2e fed B3|'),
(@t_hardiman, @u5, 'Session Cut', 'G', '9/8', '~G3 d2B cBA|~G3 d2g fed|~G3 d2B cBc|d2e fed ~B3|');

-- =====================
-- 6. TRACKS & TUNE-TRACK
-- =====================
INSERT INTO track (album_id, name, track_number, tune_id)
VALUES (@alb_vertical, 'Drowsy Maggie', 1, @t_drowsy);
SET @trk_drowsy = LAST_INSERT_ID();

INSERT INTO tune_track (tune_id, track_id) VALUES (@t_drowsy, @trk_drowsy);

-- =====================
-- 7. TUNEBOOK
-- =====================
INSERT INTO tunebook (user_id, tune_id) VALUES
(@u1, @t_kesh),
(@u1, @t_morrison),
(@u2, @t_drowsy),
(@u3, @t_silver);

-- =====================
-- 8. DISCUSSIONS
-- =====================
INSERT INTO discussion_thread (tune_id, user_id) VALUES (@t_drowsy, @u1);
SET @thread1 = LAST_INSERT_ID();

INSERT INTO post (thread_id, user_id, body)
VALUES (@thread1, @u2, 'Does anyone have the sheet music for the B-part of this tune?');
SET @post1 = LAST_INSERT_ID();

INSERT INTO reply (post_id, user_id, body)
VALUES (@post1, @u1, 'Check the settings table! I just uploaded the ABC notation.');

-- =====================
-- 9. VOTES
INSERT IGNORE INTO setting_vote (user_id, setting_id, vote_value)
SELECT u.user_id, s.setting_id, IF(RAND() > 0.5, 1, -1)
FROM user u
CROSS JOIN setting s;