INSERT INTO setting_vote (vote_id, user_id, setting_id, vote_value)
VALUES (:vote_id, :user_id, :setting_id, :vote_value)
ON DUPLICATE KEY UPDATE vote_value = VALUES(vote_value)
