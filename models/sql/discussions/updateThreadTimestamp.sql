UPDATE discussion_thread SET updated_at = NOW()
WHERE discussion_thread_id = :thread_id
