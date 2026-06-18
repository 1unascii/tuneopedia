SELECT COALESCE(SUM(vote_value), 0) FROM setting_vote WHERE setting_id = :setting_id
