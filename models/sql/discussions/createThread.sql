INSERT INTO discussion_thread (user_id, title, created_at, updated_at)
VALUES (:user_id, :title, NOW(), NOW())
