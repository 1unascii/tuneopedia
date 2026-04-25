SELECT
    p.post_id,
    p.body,
    p.created_at,
    p.user_id,
    u.user_name
FROM post p
JOIN user u ON u.user_id = p.user_id
WHERE p.thread_id = :thread_id
ORDER BY p.created_at ASC
