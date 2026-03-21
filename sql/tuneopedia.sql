-- 1. Standalone / Reference Tables
CREATE TABLE tune_type (
    tune_type_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE artist (
    artist_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100),
    middle_name VARCHAR(100),
    last_name VARCHAR(100)
);

CREATE TABLE user (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    user_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- 2. Core Entities
CREATE TABLE tune (
    tune_id INT PRIMARY KEY AUTO_INCREMENT,
    tune_type_id INT,
    composer VARCHAR(255),
    FOREIGN KEY (tune_type_id) REFERENCES tune_type(tune_type_id)
);

CREATE TABLE album (
    album_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    cover_art VARCHAR(255)
);

-- 3. Relationship / Intersection Tables
CREATE TABLE artist_album (
    artist_album_id INT PRIMARY KEY AUTO_INCREMENT,
    artist_id INT,
    album_id INT,
    FOREIGN KEY (artist_id) REFERENCES artist(artist_id),
    FOREIGN KEY (album_id) REFERENCES album(album_id)
);

CREATE TABLE track (
    track_id INT PRIMARY KEY AUTO_INCREMENT,
    album_id INT,
    name VARCHAR(255),
    track_number INT,
    tune_id INT,
    FOREIGN KEY (album_id) REFERENCES album(album_id),
    FOREIGN KEY (tune_id) REFERENCES tune(tune_id)
);

CREATE TABLE tune_track (
    tune_track_id INT PRIMARY KEY AUTO_INCREMENT,
    tune_id INT,
    track_id INT,
    FOREIGN KEY (tune_id) REFERENCES tune(tune_id),
    FOREIGN KEY (track_id) REFERENCES track(track_id)
);

-- 4. Community & Content Tables
CREATE TABLE setting (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    tune_id INT,
    user_id INT,
    name VARCHAR(255),
    key_signature VARCHAR(50),
    abc_transcription TEXT,
    file_type VARCHAR(50),
    file_url VARCHAR(255),
    FOREIGN KEY (tune_id) REFERENCES tune(tune_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE relationship (
    relationship_id INT PRIMARY KEY AUTO_INCREMENT,
    tune_id_1 INT,
    tune_id_2 INT,
    FOREIGN KEY (tune_id_1) REFERENCES tune(tune_id),
    FOREIGN KEY (tune_id_2) REFERENCES tune(tune_id)
);

-- 5. Discussion Forum Tables
CREATE TABLE discussion_thread (
    discussion_thread_id INT PRIMARY KEY AUTO_INCREMENT,
    tune_id INT,
    user_id INT,
    FOREIGN KEY (tune_id) REFERENCES tune(tune_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE post (
    post_id INT PRIMARY KEY AUTO_INCREMENT,
    thread_id INT,
    user_id INT,
    body TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES discussion_thread(discussion_thread_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE reply (
    reply_id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT,
    user_id INT,
    body TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES post(post_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id)
);

CREATE TABLE tunebook (
    tunebook_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    tune_id INT NOT NULL,
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- Ensure a user can't add the same tune to their book twice
    UNIQUE KEY unique_user_tune (user_id, tune_id), 
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (tune_id) REFERENCES tune(tune_id) ON DELETE CASCADE
);

CREATE TABLE setting_vote (
    vote_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    setting_id INT NOT NULL,
    vote_value TINYINT NOT NULL, -- 1 for Up, -1 for Down
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- Prevent a user from voting on the same setting multiple times
    UNIQUE KEY unique_user_vote (user_id, setting_id),
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE,
    FOREIGN KEY (setting_id) REFERENCES setting(setting_id) ON DELETE CASCADE
);

-- 1. Add the missing column
ALTER TABLE tune ADD COLUMN name VARCHAR(255) AFTER tune_id;

-- 2. Update your existing tunes with names
UPDATE tune SET name = 'The Drowsy Maggie' WHERE tune_id = 1;
UPDATE tune SET name = 'The Wind That Shakes the Barley' WHERE tune_id = 2;
UPDATE tune SET name = 'The Kesh Jig' WHERE tune_id = 3;
UPDATE tune SET name = 'Morrison\'s Jig' WHERE tune_id = 4;
UPDATE tune SET name = 'The Banshee' WHERE tune_id = 5;
UPDATE tune SET name = 'The Butterfly' WHERE tune_id = 7;
UPDATE tune SET name = 'Harvest Home' WHERE tune_id = 9;