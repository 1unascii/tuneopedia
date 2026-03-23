<?php
session_start();
include_once('connect.php');

// Ensure user is logged in
if (!array_key_exists('Authenticated', $_SESSION) || empty($_SESSION['Authenticated']) || !isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$tune_id = $_POST['tune_id'];
$user_id = $_SESSION['user_id'];

// Call the fixed function
if (deleteTune($tune_id, $user_id)) {
    echo "Deleted successfully.";
} else {
    echo "You don't have permission to delete this.";
}
?>