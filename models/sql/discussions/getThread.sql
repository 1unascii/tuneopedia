SELECT
    dt.discussion_thread_id,
    dt.title,
    dt.created_at,
    dt.user_id,
    u.user_name
FROM discussion_thread dt
JOIN user u ON u.user_id = dt.user_id
WHERE dt.discussion_thread_id = :thread_id
