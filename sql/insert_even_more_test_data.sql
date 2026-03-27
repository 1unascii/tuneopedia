-- Additional Users
INSERT INTO user (user_name, email, password) VALUES
('ReelKing', 'reel@example2.com', 'pass7'),
('JigQueen', 'jig@example2.com', 'pass8'),
('SlipJigSam', 'slip@example2.com', 'pass9');

-- Additional Tunes
INSERT INTO tune (name, tune_type_id, composer) VALUES
('The Morning Dew', 1, 'Traditional'),           -- Reel
('The Silver Spear', 1, 'Traditional'),           -- Reel
('The Flogging Reel', 1, 'Traditional'),          -- Reel
('The Tarbolton', 1, 'Traditional'),              -- Reel
('The Sally Gardens', 1, 'Traditional'),          -- Reel
('The Connaughtman''s Rambles', 2, 'Traditional'),-- Jig
('The Lilting Banshee', 2, 'Traditional'),        -- Jig
('Paddy Clancy''s Jig', 2, 'Traditional'),        -- Jig
('The Rocky Road to Dublin', 2, 'Traditional'),   -- Jig
('The Foxhunter''s Reel', 1, 'Traditional'),      -- Reel
('The Gold Ring', 5, 'Traditional'),              -- Slip Jig
('The Drops of Brandy', 5, 'Traditional'),        -- Slip Jig
('The Stack of Barley', 3, 'Traditional'),        -- Polka
('The Galway Rambler', 3, 'Traditional'),         -- Polka
('The Londonderry Hornpipe', 4, 'Traditional');   -- Hornpipe

-- Settings for The Morning Dew (tune_id 11)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(11, 1, 'Standard', 'D', 'A2|:dAFA dAFA|BGGF G2 AB|cAAG AGEG|AGED CDEC|'),
(11, 3, 'Ornamented', 'D', 'A2|:~d3A ~f3A|BGGF G2 AB|~c3A ~A3G|AGED CDEC|'),
(11, 8, 'Slow Air Style', 'D', 'A2|:d2 dA d2 dA|BG GF G2 AB|cA AG AG EG|AG ED CD EC|');

-- Settings for The Silver Spear (tune_id 12)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(12, 2, 'Standard', 'D', '|:A2 FA B2 FB|A2 FA BAFA|A2 FA B2 dB|AFEG FDD2:|'),
(12, 5, 'High Version', 'D', '|:a2 fa b2 fb|a2 fa bafa|a2 fa b2 db|afeg fdd2:|'),
(12, 9, 'Session Cut', 'D', '|:A2FA BAFA|d2fd edBA|~f3e dBAF|AFEG FDD2:|');

-- Settings for The Flogging Reel (tune_id 13)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(13, 1, 'Standard', 'G', '|:G2 BG dGBG|G2 BG dBAG|FADA fADA|FADE FDD2:|'),
(13, 4, 'Donegal Style', 'G', '|:~G3 B dGBG|~G3 B dBAG|~f3 A dAFA|FADE FDD2:|'),
(13, 11, 'Double Time', 'G', '|:GBGB dGBd|GBGB dBAG|fAfA dAfA|fAdA FDD2:|');

-- Settings for The Tarbolton (tune_id 14)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(14, 6, 'Standard', 'Amix', '|:eAAG A2 Bd|eAAG A2 Bd|eaag aged|BGGF GEEd:|'),
(14, 2, 'Connacht Style', 'Amix', '|:~e3 G A2 Bd|~e3 G A2 Bd|eaag a2 ge|BGGF GEE2:|'),
(14, 8, 'Slow Version', 'Amix', '|:e2 AA GA Bd|e2 AA GA Bd|ea ag ag ed|BG GF GE E2:|');

-- Settings for The Sally Gardens (tune_id 15)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(15, 3, 'Standard', 'G', '|:D2 GG AGEG|D2 GG A2 Bd|e2 ee edBA|BAGE EDDE:|'),
(15, 7, 'Ornamented', 'G', '|:D2 ~G3 AGEG|D2 ~G3 A2 Bd|e2 ~e3 edBA|BAGE EDD2:|');

-- Settings for The Connaughtman's Rambles (tune_id 16)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(16, 1, 'Standard Jig', 'G', '|:G2D G2D GAB|cBc ABc d2A|BdB cAc BGE|GAB cAF G3:|'),
(16, 5, 'Session Version', 'G', '|:~G3 ~G3 GAB|~c3 ABc d2A|BdB cAc BGE|GAB cAF ~G3:|'),
(16, 10, 'Slow Air', 'G', '|:G2 D G2 D GA B|c2 c AB c d2 A|Bd B cA c BG E|GA B cA F G3:|');

-- Settings for The Lilting Banshee (tune_id 17)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(17, 2, 'Standard', 'G', '|:G2|DGGF G2 AB|cBAG AGEG|DGGF G2 Bd|egdB A2:|'),
(17, 6, 'Ornamented', 'G', '|:G2|~D3 F ~G3 B|cBAG AGEG|~D3 F G2 Bd|egdB A2:|'),
(17, 9, 'Double Jig', 'G', '|:G2|DG GF G2 AB|cB AG AG EG|DG GF G2 Bd|eg dB A2:|');

-- Settings for Paddy Clancy's Jig (tune_id 18)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(18, 4, 'Standard', 'D', '|:dAF DFA d2|edc BAG F2|dAF DFA dA|BAG FAD D2:|'),
(18, 1, 'Clare Style', 'D', '|:~d3 DFA d2|edc BAG ~F3|~d3 DFA dA|BAG FAD ~D3:|');

-- Settings for The Rocky Road to Dublin (tune_id 19)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(19, 3, 'Standard', 'Edor', '|:EBBA B2 EB|B2 AB dBAG|FDAD BDAD|FDAD dAFD:|'),
(19, 7, 'Fast Session', 'Edor', '|:~E3 A B2 EB|B2 AB dBAG|~F3 D BDAD|~F3 D dAFD:|'),
(19, 11, 'Slow Version', 'Edor', '|:E2 BB A2 EB|B2 AB dB AG|F2 DA BD AD|F2 DA dA FD:|');

-- Settings for The Foxhunter's Reel (tune_id 20)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(20, 2, 'Standard', 'G', '|:dGGB d2 Bd|gfed BdcB|AGAB cBAG|FGAF GFED:|'),
(20, 8, 'Ornamented', 'G', '|:dGGB d2 Bd|~g3 d BdcB|AGAB cBAG|~F3 G GFED:|'),
(20, 5, 'Low Octave', 'G', '|:DGGB D2 BD|GFED BdcB|AGAB cBAG|FGAF GFED:|');

-- Settings for The Gold Ring (tune_id 21, Slip Jig)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(21, 6, 'Standard', 'Edor', '|:EBBA B2 ef|g2 fe dBAF|DEFA B2 ef|gedB A3:|'),
(21, 3, 'Session Version', 'Edor', '|:~E3 A B2 ef|~g3 e dBAF|DEFA B2 ef|gedB ~A3:|');

-- Settings for The Drops of Brandy (tune_id 22, Slip Jig)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(22, 1, 'Standard', 'G', '|:G3 ABd|e2 dB A2|G3 GAB|dBAG E2:|'),
(22, 4, 'Ornamented', 'G', '|:~G3 ABd|e2 dB ~A3|~G3 GAB|dBAG ~E3:|'),
(22, 9, 'Slow Version', 'G', '|:G2 G AB d|e2 d BA A2|G2 G GA B|dB AG E2:|');

-- Settings for The Stack of Barley (tune_id 23, Polka)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(23, 2, 'Standard Polka', 'G', '|:GE|DGGF G2 AB|cBAG AGED|DGGF G2 Bd|egdB A2:|'),
(23, 7, 'Kerry Style', 'G', '|:GE|DG GF G2 AB|cB AG AG ED|DG GF G2 Bd|eg dB A2:|');

-- Settings for The Galway Rambler (tune_id 24, Polka)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(24, 5, 'Standard Polka', 'D', '|:FA|dFFE F2 GA|BAGF EDCE|dFFE F2 AB|dBAG FDD2:|'),
(24, 3, 'Session Cut', 'D', '|:FA|~d3 E F2 GA|BAGF EDCE|~d3 E F2 AB|dBAG FD D2:|'),
(24, 11, 'Slow Version', 'D', '|:F2 A|d2 FF EF GA|BA GF ED CE|d2 FF EF AB|dB AG FD D2:|');

-- Settings for The Londonderry Hornpipe (tune_id 25)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(25, 1, 'Standard Hornpipe', 'D', '|:A2 FA d2 fd|e2 ce a2 ea|f2 df a2 fa|gfed cBAG:|'),
(25, 6, 'Ornamented', 'D', '|:A2 FA ~d3 f|e2 ce ~a3 e|~f3 d ~a3 f|gfed cBAG:|'),
(25, 8, 'Slow Air', 'D', '|:A2 FA d2 fd|e2 ce a2 ea|f2 df a2 fa|gf ed cB AG:|');

INSERT INTO setting_vote (user_id, setting_id, vote_value)
SELECT * FROM (
    SELECT 
        u.user_id,
        s.setting_id,
        IF(RAND() > 0.5, 1, -1) AS vote_value
    FROM user u
    CROSS JOIN setting s
    WHERE s.tune_id BETWEEN 11 AND 25
) AS tmp
ON DUPLICATE KEY UPDATE vote_value = tmp.vote_value;