INSERT INTO post (thread_id, user_id, body, created_at)
VALUES (:thread_id, :user_id, :body, NOW())
