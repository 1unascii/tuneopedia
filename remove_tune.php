<?php
    /*session_start();
    include_once('connect.php');
    $tune_id = $_POST['tune_id'];
    $query = "DELETE FROM tunes WHERE tune_id = $tune_id;";
    if(!deleteQuery($query)){
        echo "You don't have permission to delete this";
    }*/

    
?>
<?php
session_start();
include_once('connect.php');

// Ensure user is logged in
if (!isset($_SESSION['author_id'])) {
    die("Unauthorized");
}

$tune_id = $_POST['tune_id'];
$user_id = $_SESSION['author_id'];

// Call the fixed function
if (deleteTune($tune_id, $user_id)) {
    echo "Deleted successfully.";
} else {
    echo "You don't have permission to delete this.";
}
?>