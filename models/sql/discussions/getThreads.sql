SELECT
    dt.discussion_thread_id,
    dt.title,
    dt.created_at,
    dt.updated_at,
    u.user_name,
    u.user_id,
    (SELECT COUNT(*) FROM post p
     WHERE p.thread_id = dt.discussion_thread_id) AS post_count,
    (SELECT MAX(p2.created_at) FROM post p2
     WHERE p2.thread_id = dt.discussion_thread_id) AS last_post_at
FROM discussion_thread dt
JOIN user u ON u.user_id = dt.user_id
ORDER BY COALESCE(
    (SELECT MAX(p3.created_at) FROM post p3
     WHERE p3.thread_id = dt.discussion_thread_id),
    dt.created_at
) DESC
