SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM artist_album;
DELETE FROM collection_tune;
DELETE FROM setting_vote;
DELETE FROM tune_alias;
DELETE FROM tune_track;
DELETE FROM tune_video;
DELETE FROM tunebook;
DELETE FROM reply;
DELETE FROM post;
DELETE FROM discussion_thread;
DELETE FROM relationship;
DELETE FROM track;
DELETE FROM album;
DELETE FROM artist;
DELETE FROM setting;
DELETE FROM collection;
DELETE FROM tune;

-- Remove duplicate tune types (keep the lowest ID for each name)
DELETE t1 FROM tune_type t1
INNER JOIN tune_type t2 
WHERE t1.tune_type_id > t2.tune_type_id 
AND LOWER(t1.name) = LOWER(t2.name);

ALTER TABLE artist_album AUTO_INCREMENT = 1;
ALTER TABLE collection_tune AUTO_INCREMENT = 1;
ALTER TABLE setting_vote AUTO_INCREMENT = 1;
ALTER TABLE tune_alias AUTO_INCREMENT = 1;
ALTER TABLE tune_track AUTO_INCREMENT = 1;
ALTER TABLE tune_video AUTO_INCREMENT = 1;
ALTER TABLE tunebook AUTO_INCREMENT = 1;
ALTER TABLE reply AUTO_INCREMENT = 1;
ALTER TABLE post AUTO_INCREMENT = 1;
ALTER TABLE discussion_thread AUTO_INCREMENT = 1;
ALTER TABLE relationship AUTO_INCREMENT = 1;
ALTER TABLE track AUTO_INCREMENT = 1;
ALTER TABLE album AUTO_INCREMENT = 1;
ALTER TABLE artist AUTO_INCREMENT = 1;
ALTER TABLE setting AUTO_INCREMENT = 1;
ALTER TABLE collection AUTO_INCREMENT = 1;
ALTER TABLE tune AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;