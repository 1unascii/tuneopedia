<?php
session_start();
include_once('connect.php');
  
if (isset($_POST['login'])) {
  $user = authenticateUser($_POST['user_name'], $_POST['password']);
  
  if ($user) {
      // 1. Regenerate session ID to prevent session fixation
      session_regenerate_id(true);

      // 2. Set your session variables
      $_SESSION['Authenticated'] = true;
      $_SESSION['user_id'] = $user['user_id']; // Store the ID from your database
      $_SESSION['user_name'] = $user['user_name'];

      // 3. Return success response
      echo 'Login successful!';
  } else {
      // 4. Return failure response
      echo 'Invalid username or password.';
  }
  exit();
   
    
// User is logging out
} else if(isset($_GET['logout']) && array_key_exists('Authenticated', $_SESSION)) {
      session_destroy(); 
      echo "You have been logged out";
      
//Just landing on the page, right now it logs out and redirects to index.php
//You could make this code echo some 404 Not found error to make a dummy and hide your file name from hackers...  
} else{  
      header("Location: index.php");
}
     

  
   
  
?>
