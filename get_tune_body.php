<?php
    session_start();
    include_once('connect.php');
    
    if (isset($_POST['setting_id'])) {
        $setting_id = $_POST['setting_id'];
    
        try {
            // 1. Get the contents of your .sql file
            $sql = file_get_contents('sql/get_setting.sql');
            $tune = queryWithParams($sql, $setting_id);
    
            if ($tune) {
                echo json_encode($tune);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Tune details not found']);
            }
    
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing setting_id']);
    }

    
?>
