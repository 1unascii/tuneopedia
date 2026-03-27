-- =====================
-- REELS (tune_type_id 1) -- need 10 more
-- =====================
INSERT INTO tune (name, tune_type_id, composer) VALUES
('The Mason''s Apron', 1, 'Traditional'),
('The Morning Dew', 1, 'Traditional'),
('The Sailor''s Bonnet', 1, 'Traditional'),
('Jackson''s Reel', 1, 'Traditional'),
('The Longford Collector', 1, 'Traditional'),
('The Maid Behind the Bar', 1, 'Traditional'),
('Off to California', 1, 'Traditional'),
('The Templehouse Reel', 1, 'Traditional'),
('Tom Billy''s Reel', 1, 'Traditional'),
('The Contradiction Reel', 1, 'Traditional');
-- tune_ids 41-50

-- Settings for The Mason's Apron (41)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(41, 1, 'Standard', 'D', '|:d2 fd edfd|d2 fd edBA|d2 fd edfe|dBAF GFED:|'),
(41, 3, 'Ornamented', 'D', '|:~d3 f ed fd|~d3 f edBA|~d3 f ed fe|dBAF GFED:|'),
(41, 6, 'Session Cut', 'D', '|:d2fd edfd|d2fd edBA|Bdfd edfe|dBAF GFED:|');

-- Settings for The Morning Dew (42)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(42, 2, 'Standard', 'G', '|:DGGF GABd|edBd edBA|DGGF GABd|edBA GEE2:|'),
(42, 5, 'Donegal Style', 'G', '|:~D3 F GABd|edBd edBA|~D3 F GABd|edBA ~G3:|'),
(42, 8, 'Low Version', 'G', '|:DG GF GA Bd|ed Bd ed BA|DG GF GA Bd|ed BA GE E2:|');

-- Settings for The Sailor's Bonnet (43)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(43, 4, 'Standard', 'G', '|:g2 dg egdg|g2 dg edBA|GBdg egdg|edBA GEE2:|'),
(43, 7, 'Session Version', 'G', '|:~g3 d eg dg|~g3 d edBA|GBdg egdg|edBA ~G3:|'),
(43, 1, 'Slow Air', 'G', '|:g2 dg eg dg|g2 dg ed BA|GB dg eg dg|ed BA GE E2:|');

-- Settings for Jackson's Reel (44)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(44, 2, 'Standard', 'D', '|:AFDF AFDF|AFdF AFDE|FAAF dfed|BAFA d2ed:|'),
(44, 9, 'High Octave', 'D', '|:afdf afdf|afdf afde|faaf dfed|bafa d2ed:|'),
(44, 3, 'Clare Style', 'D', '|:AF DF AF DF|AF dF AF DE|FA AF df ed|BA FA d2 ed:|');

-- Settings for The Longford Collector (45)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(45, 5, 'Standard', 'D', '|:fded cAGE|FGAF GFED|fded cABc|dfec d2ed:|'),
(45, 1, 'Ornamented', 'D', '|:~f3 d cAGE|FGAF GFED|~f3 d cABc|dfec d2 ed:|'),
(45, 6, 'Session Cut', 'D', '|:fd ed cA GE|FG AF GF ED|fd ed cA Bc|df ec d2 ed:|');

-- Settings for The Maid Behind the Bar (46)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(46, 2, 'Standard', 'D', '|:d2 AD BDAD|d2 AG FDD2|d2 AD BdAF|GFGE FDD2:|'),
(46, 4, 'Sligo Style', 'D', '|:~d3 A BDAD|~d3 G FDD2|~d3 A BdAF|GFGE FD D2:|'),
(46, 8, 'Slow Version', 'D', '|:d2 AD BD AD|d2 AG FD D2|d2 AD Bd AF|GF GE FD D2:|');

-- Settings for Off to California (47)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(47, 3, 'Standard', 'D', '|:ADFA dAFA|ADFA GEED|ADFA dAFd|edBA FDD2:|'),
(47, 7, 'Ornamented', 'D', '|:AD FA dA FA|AD FA GE ED|AD FA dA Fd|ed BA FD D2:|'),
(47, 11, 'Session Cut', 'D', '|:~A3 F dAFA|~A3 F GEED|~A3 F dAFd|edBA FD D2:|');

-- Settings for The Templehouse Reel (48)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(48, 1, 'Standard', 'Edor', '|:EFGA Beed|efge dBAG|EFGA Beed|BAGA BEE2:|'),
(48, 5, 'Session Version', 'Edor', '|:~E3 A Beed|efge dBAG|~E3 A Beed|BAGA ~B3:|'),
(48, 9, 'Slow Air', 'Edor', '|:EF GA Be ed|ef ge dB AG|EF GA Be ed|BA GA BE E2:|');

-- Settings for Tom Billy's Reel (49)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(49, 2, 'Standard', 'G', '|:BGAG FGAF|GBAG FDD2|BGAG FGAd|BAGA GEE2:|'),
(49, 6, 'Ornamented', 'G', '|:BG AG FG AF|GB AG FD D2|BG AG FG Ad|BA GA GE E2:|'),
(49, 4, 'Fast Session', 'G', '|:~B3 G ~F3 G|GBAG FD D2|~B3 G FGAd|BAGA ~G3:|');

-- Settings for The Contradiction Reel (50)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(50, 1, 'Standard', 'Amix', '|:eAAG A2 cd|eage dBGB|eAAG A2 cd|edge dBA2:|'),
(50, 3, 'Connacht Style', 'Amix', '|:~e3 G A2 cd|eage dBGB|~e3 G A2 cd|edge dB ~A3:|'),
(50, 8, 'Session Cut', 'Amix', '|:eA AG A2 cd|ea ge dB GB|eA AG A2 cd|ed ge dB A2:|');

-- =====================
-- JIGS (tune_type_id 2) -- need 14 more
-- =====================
INSERT INTO tune (name, tune_type_id, composer) VALUES
('The Pipe on the Hob', 2, 'Traditional'),
('The Humours of Tulla', 2, 'Traditional'),
('Saddle the Pony', 2, 'Traditional'),
('The Geese in the Bog', 2, 'Traditional'),
('Banish Misfortune', 2, 'Traditional'),
('The Rambling Pitchfork', 2, 'Traditional'),
('The Walls of Liscarroll', 2, 'Traditional'),
('Garryowen', 2, 'Traditional'),
('The Irish Washerwoman', 2, 'Traditional'),
('The Fermoy Lasses', 2, 'Traditional'),
('Tell Her I Am', 2, 'Traditional'),
('The Lark in the Morning', 2, 'Traditional'),
('Sean Sa Cheo', 2, 'Traditional'),
('The Hare in the Corn', 2, 'Traditional');
-- tune_ids 51-64

-- Settings for The Pipe on the Hob (51)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(51, 1, 'Standard', 'D', '|:dAA ABd|efe dAG|FAA ABd|efe d3:|'),
(51, 4, 'Ornamented', 'D', '|:~d3 ABd|efe dAG|~f3 ABd|efe ~d3:|'),
(51, 7, 'Session Version', 'D', '|:dA A AB d|ef e dA G|fA A AB d|ef e d3:|');

-- Settings for The Humours of Tulla (52)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(52, 2, 'Standard', 'Edor', '|:EFGA Beed|Beed B2 AB|EFGA Beed|BAGA BEE2:|'),
(52, 5, 'Clare Style', 'Edor', '|:~E3 A Beed|Beed B2 AB|~E3 A Beed|BAGA ~B3:|'),
(52, 9, 'Slow Version', 'Edor', '|:EF GA Be ed|Be ed B2 AB|EF GA Be ed|BA GA BE E2:|');

-- Settings for Saddle the Pony (53)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(53, 3, 'Standard', 'G', '|:GFG GAB|dBd dBG|GFG GAB|dge d3:|'),
(53, 6, 'Session Cut', 'G', '|:~G3 GAB|dBd dBG|~G3 GAB|dge ~d3:|'),
(53, 11, 'Ornamented', 'G', '|:GF G GA B|dB d dB G|GF G GA B|dg e d3:|');

-- Settings for The Geese in the Bog (54)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(54, 1, 'Standard', 'Edor', '|:BEED E2 FG|ABcA BEED|BEED E2 FG|ABcA BEE2:|'),
(54, 8, 'Ornamented', 'Edor', '|:~B3 D E2 FG|ABcA ~B3 D|~B3 D E2 FG|ABcA ~B3:|'),
(54, 4, 'Session Version', 'Edor', '|:BE ED E2 FG|AB cA BE ED|BE ED E2 FG|AB cA BE E2:|');

-- Settings for Banish Misfortune (55)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(55, 2, 'Standard', 'Dmix', '|:dcd AGE|GED GAG|dcd AGE|GEA GEE:|'),
(55, 7, 'Ornamented', 'Dmix', '|:~d3 AGE|GED GAG|~d3 AGE|GEA ~G3:|'),
(55, 3, 'Clare Style', 'Dmix', '|:dc d AG E|GE D GA G|dc d AG E|GE A GE E:|');

-- Settings for The Rambling Pitchfork (56)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(56, 5, 'Standard', 'G', '|:GBd gdB|GBd g2 a|bgd gdB|dBA GEE:|'),
(56, 1, 'Session Version', 'G', '|:GBd gdB|GBd ~g3|bgd gdB|dBA ~G3:|'),
(56, 9, 'Slow Version', 'G', '|:GB d gd B|GB d g2 a|bg d gd B|dB A GE E:|');

-- Settings for The Walls of Liscarroll (57)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(57, 6, 'Standard', 'Edor', '|:efed BFAF|EFGA Beed|efed BFAF|EGFA BEE2:|'),
(57, 2, 'Ornamented', 'Edor', '|:~e3 d BFAF|EFGA Beed|~e3 d BFAF|EGFA ~B3:|'),
(57, 8, 'Session Cut', 'Edor', '|:ef ed BF AF|EF GA Be ed|ef ed BF AF|EG FA BE E2:|');

-- Settings for Garryowen (58)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(58, 1, 'Standard', 'G', '|:GEE GEE|GBd dBG|GEE GEE|DFA d3:|'),
(58, 4, 'Session Version', 'G', '|:~G3 ~G3|GBd dBG|~G3 ~G3|DFA ~d3:|'),
(58, 7, 'Slow Version', 'G', '|:GE E GE E|GB d dB G|GE E GE E|DF A d3:|');

-- Settings for The Irish Washerwoman (59)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(59, 3, 'Standard', 'G', '|:GGFG AGEG|GGFG A2 Bd|eded cBAG|ABAG EDD2:|'),
(59, 5, 'High Version', 'G', '|:ggfg ageg|ggfg a2 bd|eded cBAG|ABAG ED D2:|'),
(59, 10, 'Session Cut', 'G', '|:~G3 G AG EG|~G3 G A2 Bd|ed ed cB AG|AB AG ED D2:|');

-- Settings for The Fermoy Lasses (60)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(60, 2, 'Standard', 'G', '|:GBdg gdBd|GBdg dBAG|GBdg gdBd|egdB A2 GE:|'),
(60, 6, 'Ornamented', 'G', '|:GBdg gdBd|GBdg dBAG|GBdg gdBd|egdB ~A3:|'),
(60, 11, 'Session Version', 'G', '|:GB dg gd Bd|GB dg dB AG|GB dg gd Bd|eg dB A2 GE:|');

-- Settings for Tell Her I Am (61)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(61, 1, 'Standard', 'D', '|:fdd dcd|fdd dAG|fdd dcd|ecA AGE:|'),
(61, 4, 'Clare Style', 'D', '|:~f3 dcd|~f3 dAG|~f3 dcd|ecA AGE:|'),
(61, 8, 'Session Version', 'D', '|:fd d dc d|fd d dA G|fd d dc d|ec A AG E:|');

-- Settings for The Lark in the Morning (62)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(62, 2, 'Standard', 'Ador', '|:AAGA Bcd|eaag a2 ge|dBGA Bcd|egdB A3:|'),
(62, 7, 'Session Version', 'Ador', '|:~A3 A Bcd|eaag a2 ge|dBGA Bcd|egdB ~A3:|'),
(62, 3, 'Slow Air', 'Ador', '|:AA GA Bc d|ea ag a2 ge|dB GA Bc d|eg dB A3:|');

-- Settings for Sean Sa Cheo (63)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(63, 5, 'Standard', 'Edor', '|:BEED EFGE|BEED B2 AB|GFGA BAGE|DEGA BEE2:|'),
(63, 9, 'Ornamented', 'Edor', '|:~B3 D EFGE|~B3 D B2 AB|GFGA BAGE|DEGA ~B3:|'),
(63, 1, 'Session Cut', 'Edor', '|:BE ED EF GE|BE ED B2 AB|GF GA BA GE|DE GA BE E2:|');

-- Settings for The Hare in the Corn (64)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(64, 6, 'Standard', 'G', '|:dGGG GABd|eBBB Bdef|gfed BGAG|FGAF GEE2:|'),
(64, 2, 'Session Version', 'G', '|:~d3 G GABd|~e3 B Bdef|gfed BGAG|FGAF ~G3:|'),
(64, 4, 'Slow Version', 'G', '|:dG GG GA Bd|eB BB Bd ef|gf ed BG AG|FG AF GE E2:|');

-- =====================
-- POLKAS (tune_type_id 3) -- need 18 more
-- =====================
INSERT INTO tune (name, tune_type_id, composer) VALUES
('The Ballydesmond Polka', 3, 'Traditional'),
('Denis Murphy''s Polka', 3, 'Traditional'),
('Johnny Mickey''s Polka', 3, 'Traditional'),
('The Kerry Polka', 3, 'Traditional'),
('Maggie in the Woods', 3, 'Traditional'),
('The Coalminer''s Polka', 3, 'Traditional'),
('John Ryan''s Polka', 3, 'Traditional'),
('The Killorglin Polka', 3, 'Traditional'),
('Sean Reid''s Polka', 3, 'Traditional'),
('The Brosna Polka', 3, 'Traditional'),
('Larry Redican''s Polka', 3, 'Traditional'),
('The Connacht Polka', 3, 'Traditional'),
('Nora Criona', 3, 'Traditional'),
('The Cat Came Back', 3, 'Traditional'),
('The Clare Polka', 3, 'Traditional'),
('The Sligo Polka', 3, 'Traditional'),
('Peggy''s Polka', 3, 'Traditional'),
('The Twelve Pins', 3, 'Traditional');
-- tune_ids 65-82

-- Settings for The Ballydesmond Polka (65)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(65, 1, 'Standard', 'D', '|:AFDF AFDF|AFDF AGFE|AFDF AFAd|BAFA d2ed:|'),
(65, 3, 'Kerry Style', 'D', '|:AF DF AF DF|AF DF AG FE|AF DF AF Ad|BA FA d2 ed:|'),
(65, 6, 'Session Cut', 'D', '|:~A3 F ~A3 F|~A3 F AGFE|~A3 F AFAd|BAFA d2 ed:|');

-- Settings for Denis Murphy's Polka (66)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(66, 2, 'Standard', 'D', '|:dAFA dAFA|dAFA GEE2|dAFA dAFd|edBA FDD2:|'),
(66, 5, 'Ornamented', 'D', '|:~d3 A ~d3 A|~d3 A GEE2|~d3 A dAFd|edBA FD D2:|'),
(66, 8, 'Session Version', 'D', '|:dA FA dA FA|dA FA GE E2|dA FA dA Fd|ed BA FD D2:|');

-- Settings for Johnny Mickey's Polka (67)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(67, 4, 'Standard', 'G', '|:GBGB dGBd|GBGB AGEG|GBGB dGBd|egdB A2 GE:|'),
(67, 7, 'Kerry Style', 'G', '|:GB GB dG Bd|GB GB AG EG|GB GB dG Bd|eg dB A2 GE:|'),
(67, 10, 'Session Cut', 'G', '|:~G3 B ~d3 B|~G3 B AGEG|~G3 B ~d3 B|egdB A2 GE:|');

-- Settings for The Kerry Polka (68)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(68, 1, 'Standard', 'Edor', '|:EFGA Beed|efge dBAF|EFGA Beed|BAGA BEE2:|'),
(68, 3, 'Session Version', 'Edor', '|:~E3 A Beed|efge dBAF|~E3 A Beed|BAGA ~B3:|'),
(68, 9, 'Slow Version', 'Edor', '|:EF GA Be ed|ef ge dB AF|EF GA Be ed|BA GA BE E2:|');

-- Settings for Maggie in the Woods (69)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(69, 2, 'Standard', 'G', '|:GEEd GEED|GEEd GEE2|GEEd GEEd|BAGA GEE2:|'),
(69, 6, 'Ornamented', 'G', '|:~G3 d ~G3 D|~G3 d GEE2|~G3 d ~G3 d|BAGA ~G3:|'),
(69, 11, 'Session Cut', 'G', '|:GE Ed GE ED|GE Ed GE E2|GE Ed GE Ed|BA GA GE E2:|');

-- Settings for The Coalminer's Polka (70)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(70, 5, 'Standard', 'D', '|:fdBd fdBd|fdBd AFDF|fdBd fdBd|BAFA d2ed:|'),
(70, 1, 'Session Version', 'D', '|:fd Bd fd Bd|fd Bd AF DF|fd Bd fd Bd|BA FA d2 ed:|'),
(70, 4, 'Ornamented', 'D', '|:~f3 d ~f3 d|~f3 d AFDF|~f3 d ~f3 d|BAFA d2 ed:|');

-- Settings for John Ryan's Polka (71)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(71, 2, 'Standard', 'D', '|:e2 fe dBAF|E2 FE DEFA|d2 fd edBA|BAFA d2ed:|'),
(71, 7, 'Kerry Style', 'D', '|:e2 fe dBAF|E2 FE DE FA|d2 fd ed BA|BA FA d2 ed:|'),
(71, 3, 'Session Cut', 'D', '|:~e3 f dBAF|~E3 F DEFA|~d3 f edBA|BAFA d2 ed:|');

-- Settings for The Killorglin Polka (72)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(72, 8, 'Standard', 'G', '|:dBAG FGAF|GBAG FDD2|dBAG FGAd|BAGA GEE2:|'),
(72, 1, 'Ornamented', 'G', '|:dBAG ~F3 F|GBAG FD D2|dBAG FGAd|BAGA ~G3:|'),
(72, 5, 'Session Version', 'G', '|:dB AG FG AF|GB AG FD D2|dB AG FG Ad|BA GA GE E2:|');

-- Settings for Sean Reid's Polka (73)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(73, 6, 'Standard', 'D', '|:AFDE FDEF|AFDE FDD2|AFDE FDed|BAFA d2ed:|'),
(73, 2, 'Session Cut', 'D', '|:AF DE FD EF|AF DE FD D2|AF DE FD ed|BA FA d2 ed:|'),
(73, 9, 'Ornamented', 'D', '|:~A3 E ~F3 F|~A3 E FDD2|~A3 E FDed|BAFA d2 ed:|');

-- Settings for The Brosna Polka (74)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(74, 3, 'Standard', 'G', '|:GFG GAG|GFG GED|GFG GABd|egdB A2:|'),
(74, 7, 'Kerry Style', 'G', '|:~G3 GAG|~G3 GED|~G3 GABd|egdB ~A3:|'),
(74, 4, 'Session Version', 'G', '|:GF G GA G|GF G GE D|GF G GA Bd|eg dB A2:|');

-- Settings for Larry Redican's Polka (75)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(75, 1, 'Standard', 'D', '|:DFAF DFAF|DFAd edBA|DFAF DFAd|BAFA d2ed:|'),
(75, 5, 'Ornamented', 'D', '|:~D3 F ~D3 F|~D3 F edBA|~D3 F DFAd|BAFA d2 ed:|'),
(75, 8, 'Session Cut', 'D', '|:DF AF DF AF|DF Ad ed BA|DF AF DF Ad|BA FA d2 ed:|');

-- Settings for The Connacht Polka (76)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(76, 2, 'Standard', 'Ador', '|:AAGE A2 cd|eaag a2 ge|dBGA Bcde|gedB A3:|'),
(76, 6, 'Session Version', 'Ador', '|:~A3 E A2 cd|eaag a2 ge|dBGA Bcde|gedB ~A3:|'),
(76, 10, 'Ornamented', 'Ador', '|:AA GE A2 cd|ea ag a2 ge|dB GA Bc de|ge dB A3:|');

-- Settings for Nora Criona (77)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(77, 4, 'Standard', 'Edor', '|:BEED EFGE|DEED DEGA|BEED EFGE|DEGA BEE2:|'),
(77, 1, 'Ornamented', 'Edor', '|:~B3 D EFGE|~D3 D DEGA|~B3 D EFGE|DEGA ~B3:|'),
(77, 7, 'Session Cut', 'Edor', '|:BE ED EF GE|DE ED DE GA|BE ED EF GE|DE GA BE E2:|');

-- Settings for The Cat Came Back (78)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(78, 3, 'Standard', 'G', '|:d2 Bd g2 Bg|d2 Bd edBA|GABd gdBd|egdB A2 GE:|'),
(78, 5, 'Kerry Style', 'G', '|:d2 Bd ~g3 g|d2 Bd edBA|GABd gdBd|egdB A2 GE:|'),
(78, 9, 'Session Version', 'G', '|:d2 Bd g2 Bg|d2 Bd ed BA|GA Bd gd Bd|eg dB A2 GE:|');

-- Settings for The Clare Polka (79)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(79, 2, 'Standard', 'D', '|:dBAG FGAF|GBdg edBA|dBAG FGAd|BAFA d2ed:|'),
(79, 6, 'Session Cut', 'D', '|:dB AG FG AF|GB dg ed BA|dB AG FG Ad|BA FA d2 ed:|'),
(79, 11, 'Ornamented', 'D', '|:dBAG ~F3 F|GBdg edBA|dBAG FGAd|BAFA d2 ed:|');

-- Settings for The Sligo Polka (80)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(80, 1, 'Standard', 'G', '|:GEDE GEDE|GBAG FDED|GEDE GEGd|BGAG GEE2:|'),
(80, 4, 'Ornamented', 'G', '|:~G3 E ~G3 E|GBAG FDED|~G3 E GEGd|BGAG ~G3:|'),
(80, 8, 'Session Version', 'G', '|:GE DE GE DE|GB AG FD ED|GE DE GE Gd|BG AG GE E2:|');

-- Settings for Peggy's Polka (81)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(81, 5, 'Standard', 'D', '|:FADA FADA|FABd edBA|FADA FABd|BAFA d2ed:|'),
(81, 2, 'Session Cut', 'D', '|:FA DA FA DA|FA Bd ed BA|FA DA FA Bd|BA FA d2 ed:|'),
(81, 7, 'Ornamented', 'D', '|:~F3 A ~F3 A|FABd edBA|~F3 A FABd|BAFA d2 ed:|');

-- Settings for The Twelve Pins (82)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(82, 3, 'Standard', 'G', '|:GBdB GBdB|GBdg edBA|GBdB GBdg|edBA GEE2:|'),
(82, 6, 'Connacht Style', 'G', '|:GB dB GB dB|GB dg ed BA|GB dB GB dg|ed BA GE E2:|'),
(82, 9, 'Session Version', 'G', '|:~G3 B ~G3 B|GBdg edBA|~G3 B GBdg|edBA ~G3:|');

-- =====================
-- SLIP JIGS (tune_type_id 5) -- need 18 more
-- =====================
INSERT INTO tune (name, tune_type_id, composer) VALUES
('The Rocky Road', 5, 'Traditional'),
('Hardiman the Fiddler', 5, 'Traditional'),
('The Foxchase', 5, 'Traditional'),
('Morgan Magan', 5, 'Traditional'),
('The Piper''s Despair', 5, 'Traditional'),
('Drops of Little Whiskey', 5, 'Traditional'),
('The Exile''s Return', 5, 'Traditional'),
('Planxty Irwin', 5, 'Traditional'),
('The Humours of Whiskey', 5, 'Traditional'),
('The Swaggering Jig', 5, 'Traditional'),
('The Sporting Pitchfork', 5, 'Traditional'),
('The Kid on the Mountain', 5, 'Traditional'),
('The Morning Dew Slip', 5, 'Traditional'),
('Dinny Delaney''s', 5, 'Traditional'),
('The Moving Cloud', 5, 'Traditional'),
('The Hag with the Money', 5, 'Traditional'),
('Hardiman''s Fancy', 5, 'Traditional'),
('The Fairy Reel Slip', 5, 'Traditional');
-- tune_ids 83-100

-- Settings for The Rocky Road (83)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(83, 1, 'Standard', 'Edor', '|:B2E G2E F2E|B2E G2E FED|B2E G2E F2A|B2c d2B AFD:|'),
(83, 4, 'Session Version', 'Edor', '|:B2E ~G3 F2E|B2E ~G3 FED|B2E ~G3 F2A|B2c d2B AFD:|'),
(83, 7, 'Ornamented', 'Edor', '|:B2 EG 2E F2 E|B2 EG 2E FE D|B2 EG 2E F2 A|B2 cd 2B AF D:|');

-- Settings for Hardiman the Fiddler (84)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(84, 2, 'Standard', 'G', '|:G2B d2B cBA|G2B d2g fed|G2B d2B cBc|d2e fed B3:|'),
(84, 5, 'Session Cut', 'G', '|:~G3 d2B cBA|~G3 d2g fed|~G3 d2B cBc|d2e fed ~B3:|'),
(84, 9, 'Slow Version', 'G', '|:G2 Bd 2B cB A|G2 Bd 2g fe d|G2 Bd 2B cB c|d2 ef ed B3:|');

-- Settings for The Foxchase (85)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(85, 3, 'Standard', 'Ador', '|:A2a a2g e2d|A2a age dBA|A2a a2g e2f|gedB A3:|'),
(85, 6, 'Ornamented', 'Ador', '|:A2a ~a3 e2d|A2a age dBA|A2a ~a3 e2f|gedB ~A3:|'),
(85, 10, 'Session Version', 'Ador', '|:A2 aa 2g e2 d|A2 aa ge dB A|A2 aa 2g e2 f|ge dB A3:|');

-- Settings for Morgan Magan (86)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(86, 1, 'Standard', 'G', '|:G2d B2d cBA|G2d B2g fed|G2d B2d cBc|d2B AGE G3:|'),
(86, 4, 'Session Cut', 'G', '|:~G3 B2d cBA|~G3 B2g fed|~G3 B2d cBc|d2B AGE ~G3:|'),
(86, 8, 'Ornamented', 'G', '|:G2 dB 2d cB A|G2 dB 2g fe d|G2 dB 2d cB c|d2 BA GE G3:|');

-- Settings for The Piper's Despair (87)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(87, 2, 'Standard', 'Edor', '|:e2B G2B F2E|e2B G2e fed|e2B G2B F2A|B2c d2e B3:|'),
(87, 7, 'Session Version', 'Edor', '|:~e3 G2B F2E|~e3 G2e fed|~e3 G2B F2A|B2c d2e ~B3:|'),
(87, 11, 'Slow Version', 'Edor', '|:e2 BG 2B F2 E|e2 BG 2e fe d|e2 BG 2B F2 A|B2 cd 2e B3:|');

-- Settings for Drops of Little Whiskey (88)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(88, 5, 'Standard', 'G', '|:d2g g2f e2d|d2g g2a bag|d2g g2f e2f|gedB A2 G2:|'),
(88, 1, 'Ornamented', 'G', '|:d2g ~g3 e2d|d2g ~g3 bag|d2g ~g3 e2f|gedB A2 G2:|'),
(88, 3, 'Session Cut', 'G', '|:d2 gg 2f e2 d|d2 gg 2a ba g|d2 gg 2f e2 f|ge dB A2 G2:|');

-- Settings for The Exile's Return (89)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(89, 6, 'Standard', 'Ador', '|:a2e c2e d2c|a2e c2a gec|a2e c2e d2f|ecgA A3:|'),
(89, 2, 'Session Version', 'Ador', '|:~a3 c2e d2c|~a3 c2a gec|~a3 c2e d2f|ecgA ~A3:|'),
(89, 9, 'Ornamented', 'Ador', '|:a2 ec 2e d2 c|a2 ec 2a ge c|a2 ec 2e d2 f|ec gA A3:|');

-- Settings for Planxty Irwin (90)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(90, 4, 'Standard', 'G', '|:B2d g2e d2B|B2d g2b age|B2d g2e d2e|fedB A2 G2:|'),
(90, 7, 'Session Cut', 'G', '|:B2d ~g3 d2B|B2d ~g3 age|B2d ~g3 d2e|fedB A2 G2:|'),
(90, 1, 'Slow Version', 'G', '|:B2 dg 2e d2 B|B2 dg 2b ag e|B2 dg 2e d2 e|fe dB A2 G2:|');

-- Settings for The Humours of Whiskey (91)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(91, 3, 'Standard', 'D', '|:f2d A2d B2A|f2d A2f edB|f2d A2d B2c|dBfA A3:|'),
(91, 6, 'Ornamented', 'D', '|:~f3 A2d B2A|~f3 A2f edB|~f3 A2d B2c|dBfA ~A3:|'),
(91, 8, 'Session Version', 'D', '|:f2 dA 2d B2 A|f2 dA 2f ed B|f2 dA 2d B2 c|dB fA A3:|');

-- Settings for The Swaggering Jig (92)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(92, 5, 'Standard', 'G', '|:G2B B2c d2B|G2B B2d edB|G2B B2c d2e|dBGE E2 G2:|'),
(92, 2, 'Session Cut', 'G', '|:~G3 B2c d2B|~G3 B2d edB|~G3 B2c d2e|dBGE E2 G2:|'),
(92, 10, 'Slow Version', 'G', '|:G2 BB 2c d2 B|G2 BB 2d ed B|G2 BB 2c d2 e|dB GE E2 G2:|');

-- Settings for The Sporting Pitchfork (93)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(93, 1, 'Standard', 'Edor', '|:f2e B2e c2B|f2e B2f edc|f2e B2e c2d|eBfB B3:|'),
(93, 4, 'Ornamented', 'Edor', '|:~f3 B2e c2B|~f3 B2f edc|~f3 B2e c2d|eBfB ~B3:|'),
(93, 7, 'Session Version', 'Edor', '|:f2 eB 2e c2 B|f2 eB 2f ed c|f2 eB 2e c2 d|eB fB B3:|');

-- Settings for The Kid on the Mountain (94)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(94, 2, 'Standard', 'Edor', '|:B2E E2F G2E|B2E E2G FED|B2E E2F GFG|A2B ABA GED:|'),
(94, 6, 'Session Cut', 'Edor', '|:~B3 E2F G2E|~B3 E2G FED|~B3 E2F GFG|A2B ABA GED:|'),
(94, 9, 'Slow Version', 'Edor', '|:B2 EE 2F G2 E|B2 EE 2G FE D|B2 EE 2F GF G|A2 BA BA GE D:|');

-- Settings for The Morning Dew Slip (95)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(95, 3, 'Standard', 'G', '|:d2B G2B A2G|d2B G2d BAG|d2B G2B A2B|cBAG E2 D2:|'),
(95, 5, 'Ornamented', 'G', '|:d2B ~G3 A2G|d2B ~G3 BAG|d2B ~G3 A2B|cBAG E2 D2:|'),
(95, 11, 'Session Version', 'G', '|:d2 BG 2B A2 G|d2 BG 2d BA G|d2 BG 2B A2 B|cB AG E2 D2:|');

-- Settings for Dinny Delaney's (96)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(96, 1, 'Standard', 'Ador', '|:e2A A2B c2A|e2A A2e dcB|e2A A2B c2d|ecBc A3:|'),
(96, 4, 'Session Cut', 'Ador', '|:~e3 A2B c2A|~e3 A2e dcB|~e3 A2B c2d|ecBc ~A3:|'),
(96, 8, 'Ornamented', 'Ador', '|:e2 AA 2B c2 A|e2 AA 2e dc B|e2 AA 2B c2 d|ec Bc A3:|');

-- Settings for The Moving Cloud (97)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(97, 2, 'Standard', 'G', '|:g2d B2d c2B|g2d B2g fed|g2d B2d c2d|edBd G3:|'),
(97, 6, 'Session Version', 'G', '|:~g3 B2d c2B|~g3 B2g fed|~g3 B2d c2d|edBd ~G3:|'),
(97, 10, 'Slow Version', 'G', '|:g2 dB 2d c2 B|g2 dB 2g fe d|g2 dB 2d c2 d|ed Bd G3:|');

-- Settings for The Hag with the Money (98)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(98, 5, 'Standard', 'Edor', '|:e2f g2e f2e|e2f g2b age|e2f g2e f2g|agfe B3:|'),
(98, 3, 'Ornamented', 'Edor', '|:~e3 g2e f2e|~e3 g2b age|~e3 g2e f2g|agfe ~B3:|'),
(98, 7, 'Session Cut', 'Edor', '|:e2 fg 2e f2 e|e2 fg 2b ag e|e2 fg 2e f2 g|ag fe B3:|');

-- Settings for Hardiman's Fancy (99)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(99, 1, 'Standard', 'G', '|:d2g f2g e2d|d2g g2b age|d2g f2g e2f|gedB G3:|'),
(99, 4, 'Session Version', 'G', '|:d2g ~f3 e2d|d2g ~g3 age|d2g ~f3 e2f|gedB ~G3:|'),
(99, 9, 'Ornamented', 'G', '|:d2 gf 2g e2 d|d2 gg 2b ag e|d2 gf 2g e2 f|ge dB G3:|');

-- Settings for The Fairy Reel Slip (100)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(100, 2, 'Standard', 'Edor', '|:B2e e2f g2e|B2e e2g fed|B2e e2f g2f|edBA B3:|'),
(100, 6, 'Ornamented', 'Edor', '|:B2e ~e3 g2e|B2e ~e3 fed|B2e ~e3 g2f|edBA ~B3:|'),
(100, 8, 'Session Cut', 'Edor', '|:B2 ee 2f g2 e|B2 ee 2g fe d|B2 ee 2f g2 f|ed BA B3:|');

-- =====================
-- HORNPIPES (tune_type_id 4) -- need 18 more
-- =====================
INSERT INTO tune (name, tune_type_id, composer) VALUES
('The Rights of Man', 4, 'Traditional'),
('The Flowers of Edinburgh', 4, 'Traditional'),
('The Boys of Bluehill', 4, 'Traditional'),
('The Lads of Laois', 4, 'Traditional'),
('The King of the Fairies', 4, 'Traditional'),
('The Plains of Boyle', 4, 'Traditional'),
('O''Keefe''s Hornpipe', 4, 'Traditional'),
('The Greencastle Hornpipe', 4, 'Traditional'),
('The Galway Hornpipe', 4, 'Traditional'),
('Cronin''s Hornpipe', 4, 'Traditional'),
('The Liverpool Hornpipe', 4, 'Traditional'),
('The Sailor''s Hornpipe', 4, 'Traditional'),
('Petronella', 4, 'Traditional'),
('The College Hornpipe', 4, 'Traditional'),
('The Stack of Wheat', 4, 'Traditional'),
('The Limerick Hornpipe', 4, 'Traditional'),
('The Wexford Hornpipe', 4, 'Traditional'),
('The Clare Hornpipe', 4, 'Traditional');
-- tune_ids 101-118

-- Settings for The Rights of Man (101)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(101, 1, 'Standard', 'Edor', '|:e2 fe dBAG|FGAF GFED|E2 FE DEFA|Beed B2 AG:|'),
(101, 3, 'Ornamented', 'Edor', '|:e2 fe dBAG|~F3 F GFED|E2 FE DEFA|Beed B2 AG:|'),
(101, 6, 'Session Cut', 'Edor', '|:e2fe dBAG|FGAF GFED|E2FE DEFA|Beed B2AG:|');

-- Settings for The Flowers of Edinburgh (102)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(102, 2, 'Standard', 'D', '|:d2 fd edfd|d2 fd edBA|d2 fd edfe|dBAF GFED:|'),
(102, 5, 'Ornamented', 'D', '|:~d3 f ed fd|~d3 f edBA|~d3 f ed fe|dBAF GFED:|'),
(102, 8, 'Session Version', 'D', '|:d2fd edfd|d2fd edBA|d2fd edfe|dBAF GFED:|');

-- Settings for The Boys of Bluehill (103)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(103, 4, 'Standard', 'G', '|:G2 BG dGBG|cBAc BGGE|DGGF GABc|dBAG GEDE:|'),
(103, 7, 'Session Cut', 'G', '|:~G3 G dGBG|cBAc BGGE|~D3 F GABc|dBAG GEDE:|'),
(103, 1, 'Slow Version', 'G', '|:G2 BG dG BG|cB Ac BG GE|DG GF GA Bc|dB AG GE DE:|');

-- Settings for The Lads of Laois (104)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(104, 2, 'Standard', 'D', '|:AFDF AFDF|ABde fdBA|dfec dBAF|GFGE FDD2:|'),
(104, 6, 'Ornamented', 'D', '|:~A3 F ~A3 F|ABde fdBA|dfec dBAF|GFGE FD D2:|'),
(104, 9, 'Session Version', 'D', '|:AF DF AF DF|AB de fd BA|df ec dB AF|GF GE FD D2:|');

-- Settings for The King of the Fairies (105)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(105, 3, 'Standard', 'Edor', '|:BEED EFGE|DEED DEGA|BEED EFGE|dBAG BEE2:|'),
(105, 5, 'Session Cut', 'Edor', '|:~B3 D EFGE|~D3 D DEGA|~B3 D EFGE|dBAG ~B3:|'),
(105, 10, 'Slow Version', 'Edor', '|:BE ED EF GE|DE ED DE GA|BE ED EF GE|dB AG BE E2:|');

-- Settings for The Plains of Boyle (106)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(106, 1, 'Standard', 'G', '|:d2 Bd gdBd|g2 fg edBA|GABd gdBg|dBAG GEE2:|'),
(106, 4, 'Ornamented', 'G', '|:d2 Bd ~g3 d|~g3 g edBA|GABd gdBg|dBAG ~G3:|'),
(106, 7, 'Session Version', 'G', '|:d2Bd gdBd|g2fg edBA|GABd gdBg|dBAG GEE2:|');

-- Settings for O'Keefe's Hornpipe (107)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(107, 2, 'Standard', 'D', '|:fded cAGE|FGAF GFED|fded cABc|dBAG FDD2:|'),
(107, 6, 'Session Cut', 'D', '|:~f3 d cAGE|FGAF GFED|~f3 d cABc|dBAG FD D2:|'),
(107, 11, 'Ornamented', 'D', '|:fd ed cA GE|FG AF GF ED|fd ed cA Bc|dB AG FD D2:|');

-- Settings for The Greencastle Hornpipe (108)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(108, 5, 'Standard', 'G', '|:GBdg bgdg|GBdg edBA|GBdg bgdB|GABA GEE2:|'),
(108, 1, 'Session Version', 'G', '|:GBdg bgdg|GBdg edBA|GBdg bgdB|GABA ~G3:|'),
(108, 3, 'Ornamented', 'G', '|:GB dg bg dg|GB dg ed BA|GB dg bg dB|GA BA GE E2:|');

-- Settings for The Galway Hornpipe (109)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(109, 2, 'Standard', 'G', '|:dGBG dGBG|dGBG AGEG|dGBG dGBd|egdB A2 GE:|'),
(109, 7, 'Session Cut', 'G', '|:dGBG dGBG|dGBG AGEG|dGBG dGBd|egdB A2 GE:|'),
(109, 9, 'Slow Version', 'G', '|:dG BG dG BG|dG BG AG EG|dG BG dG Bd|eg dB A2 GE:|');

-- Settings for Cronin's Hornpipe (110)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(110, 4, 'Standard', 'D', '|:ADFA dAFA|ADFA GEED|ADFA dAFd|edBA FDD2:|'),
(110, 8, 'Ornamented', 'D', '|:~A3 F dAFA|~A3 F GEED|~A3 F dAFd|edBA FD D2:|'),
(110, 1, 'Session Version', 'D', '|:AD FA dA FA|AD FA GE ED|AD FA dA Fd|ed BA FD D2:|');

-- Settings for The Liverpool Hornpipe (111)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(111, 3, 'Standard', 'D', '|:d2 AF DFAF|d2 AF GEED|d2 AF DFAd|BAFA d2ed:|'),
(111, 6, 'Session Cut', 'D', '|:d2AF DFAF|d2AF GEED|d2AF DFAd|BAFA d2ed:|'),
(111, 10, 'Ornamented', 'D', '|:d2 AF DF AF|d2 AF GE ED|d2 AF DF Ad|BA FA d2 ed:|');

-- Settings for The Sailor's Hornpipe (112)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(112, 5, 'Standard', 'D', '|:eAAG A2 cd|eaag a2 ge|dBGB d2 Bd|edBA GEE2:|'),
(112, 2, 'Session Version', 'D', '|:~e3 G A2 cd|eaag a2 ge|dBGB d2 Bd|edBA ~G3:|'),
(112, 8, 'Ornamented', 'D', '|:eA AG A2 cd|ea ag a2 ge|dB GB d2 Bd|ed BA GE E2:|');

-- Settings for Petronella (113)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(113, 1, 'Standard', 'D', '|:d2 dA BAFA|d2 dA GEED|d2 dA BABd|BAFA d2ed:|'),
(113, 4, 'Session Cut', 'D', '|:d2dA BAFA|d2dA GEED|d2dA BABd|BAFA d2ed:|'),
(113, 7, 'Ornamented', 'D', '|:d2 dA BA FA|d2 dA GE ED|d2 dA BA Bd|BA FA d2 ed:|');

-- Settings for The College Hornpipe (114)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(114, 2, 'Standard', 'D', '|:e2 fe dBAG|FDEF GFED|E2 FG ABcA|BAFA GEED:|'),
(114, 6, 'Session Version', 'D', '|:e2fe dBAG|FDEF GFED|E2FG ABcA|BAFA GEED:|'),
(114, 9, 'Ornamented', 'D', '|:e2 fe dB AG|FD EF GF ED|E2 FG AB cA|BA FA GE ED:|');

-- Settings for The Stack of Wheat (115)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(115, 3, 'Standard', 'G', '|:GBAG FGAF|GBAG FDD2|GBAd BGAG|FGAF GEE2:|'),
(115, 5, 'Session Cut', 'G', '|:GBAG ~F3 F|GBAG FD D2|GBAd BGAG|~F3 F GEE2:|'),
(115, 11, 'Ornamented', 'G', '|:GB AG FG AF|GB AG FD D2|GB Ad BG AG|FG AF GE E2:|');

-- Settings for The Limerick Hornpipe (116)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(116, 1, 'Standard', 'D', '|:AFDF AFDF|ABde fdBA|dfec dBAF|AFEG FDD2:|'),
(116, 4, 'Ornamented', 'D', '|:~A3 F ~A3 F|ABde fdBA|dfec dBAF|AFEG FD D2:|'),
(116, 8, 'Session Version', 'D', '|:AF DF AF DF|AB de fd BA|df ec dB AF|AF EG FD D2:|');

-- Settings for The Wexford Hornpipe (117)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(117, 2, 'Standard', 'G', '|:GEEd BGGE|DGGF GABc|dBAG FGAF|GBAG GEE2:|'),
(117, 6, 'Session Cut', 'G', '|:~G3 d BGGE|~D3 F GABc|dBAG ~F3 F|GBAG ~G3:|'),
(117, 10, 'Slow Version', 'G', '|:GE Ed BG GE|DG GF GA Bc|dB AG FG AF|GB AG GE E2:|');

-- Settings for The Clare Hornpipe (118)
INSERT INTO setting (tune_id, user_id, name, key_signature, abc_transcription) VALUES
(118, 5, 'Standard', 'D', '|:fded cAGE|FGAF GFED|fded cABc|BAFA d2ed:|'),
(118, 3, 'Ornamented', 'D', '|:~f3 d cAGE|FGAF GFED|~f3 d cABc|BAFA d2 ed:|'),
(118, 7, 'Session Version', 'D', '|:fd ed cA GE|FG AF GF ED|fd ed cA Bc|BA FA d2 ed:|');

-- =====================
-- VOTES for all new settings
-- =====================
INSERT INTO setting_vote (user_id, setting_id, vote_value)
SELECT * FROM (
    SELECT 
        u.user_id,
        s.setting_id,
        IF(RAND() > 0.5, 1, -1) AS vote_value
    FROM user u
    CROSS JOIN setting s
    WHERE s.tune_id BETWEEN 41 AND 118
) AS tmp
ON DUPLICATE KEY UPDATE vote_value = tmp.vote_value;