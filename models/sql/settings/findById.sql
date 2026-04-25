SELECT 
    s.*, 
    t.name, 
    t.composer 
FROM setting s
INNER JOIN tune t ON s.tune_id = t.tune_id
WHERE s.setting_id = :param;