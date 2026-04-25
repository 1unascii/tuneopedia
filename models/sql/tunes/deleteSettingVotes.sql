DELETE FROM setting_vote WHERE setting_id IN (SELECT setting_id FROM setting WHERE tune_id = :id)
