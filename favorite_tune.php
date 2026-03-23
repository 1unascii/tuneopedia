<?php
/**
 * Adds one tune to the logged-in user's tunebook (favorites).
 */
session_start();
include_once 'connect.php';

header('Content-Type: text/plain; charset=utf-8');

if (!array_key_exists('Authenticated', $_SESSION) || empty($_SESSION['Authenticated']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo 'Unauthorized';
    exit;
}

$tune_id = ($_POST['tune_id']);
$user_id = ($_POST['user_id']);
$sql = "SELECT * FROM tune WHERE tune_id = ?";

$db = connect();
if (!$db) {
    http_response_code(500);
    echo 'Database error';
    exit;
}

try {
    $stmt = $db->prepare($sql);
    $stmt->execute([$tune_id]);

    // rowCount() tells you if any rows actually matched your ID
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo 'Tune not found';
        exit;
    }
    
    // If you get here, the tune exists!
    $tune = $stmt->fetch(); 

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo 'Database error';
    exit;
}

$sql = sprintf(
    file_get_contents('sql/add_favorite.sql'),
    $user_id,
    $tune_id
);

$result = simpleQuery($sql);
if ($result === false) {
    http_response_code(500);
    echo 'Could not save favorite';
    exit;
}

echo 'OK';
