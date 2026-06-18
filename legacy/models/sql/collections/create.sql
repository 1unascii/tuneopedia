INSERT INTO collection (user_id, name, author, description, is_shared, created_at)
VALUES (:user_id, :name, :author, :description, :is_shared, NOW())
