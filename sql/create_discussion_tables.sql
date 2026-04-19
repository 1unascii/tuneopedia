-- Alter existing discussion_thread table to add missing columns
ALTER TABLE discussion_thread
    ADD COLUMN IF NOT EXISTS title VARCHAR(255) NOT NULL DEFAULT '' AFTER user_id,
    ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER title,
    ADD COLUMN IF NOT EXISTS updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Make user_id NOT NULL
ALTER TABLE discussion_thread MODIFY user_id INT(11) NOT NULL;

-- Posts table (already exists in DB, included here for reference)
CREATE TABLE IF NOT EXISTS post (
    post_id         INT AUTO_INCREMENT PRIMARY KEY,
    thread_id       INT NOT NULL,
    user_id         INT NOT NULL,
    body            TEXT NOT NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES discussion_thread(discussion_thread_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
