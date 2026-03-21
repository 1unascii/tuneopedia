SELECT 
    tt.tune_type_id, 
    tt.name
FROM tune_type tt
WHERE EXISTS (
    SELECT 1 
    FROM tune t 
    WHERE t.tune_type_id = tt.tune_type_id
);