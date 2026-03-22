<?php
session_start();
include_once('connect.php');
$db = connect(); 

  // User is logging in
  
  /*if (isset($_POST['login'])) {
      if(!array_key_exists('Authenticated', $_SESSION)){
          if(authenticateUser($_POST['user_name'], sha1($_POST['password']))){
              $user = authenticateUser($_POST['user_name'], sha1($_POST['password']));              
              $_SESSION['user_id'] = $user['user_id'];
              $_SESSION['Authenticated'] = true; 
              $response = "Welcome back " . $user['user_name'];
              echo $response;
          }else{
              echo "Your username and/or password were not accepted";          
          }
      } else {
          echo "You are already logged in";
      }*/
  /*if (isset($_POST['login'])) {
    if (!isset($_SESSION['Authenticated'])) {
        // Capture the result in one variable
        $user = authenticateUser($_POST['user_name'], $_POST['password']); 
        
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name']; // Store name for greeting
            $_SESSION['Authenticated'] = true; 
            echo "Welcome back " . $user['user_name'];
        } else {
            echo "Your username and/or password were not accepted";          
        }
    } else {
        echo "You are already logged in";
    }  */
  if (isset($_POST['login'])) {
    $user = authenticateUser($_POST['user_name'], $_POST['password'], $db);
    
    if ($user) {
        // 1. Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // 2. Set your session variables
        $_SESSION['Authenticated'] = true;
        $_SESSION['user_id'] = $user['id']; // Store the ID from your database
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
        //$_SESSION['Authenticated'] = false;  
        header("Location: index.php");
        //session_write_close();
        //header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
  }
     

  
   
  
?>
