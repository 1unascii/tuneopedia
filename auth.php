<?php
session_start();
include_once('connect.php');
  
  // User is logging in
  
  if (isset($_POST['login'])) {
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
      }
      
      
  // User is logging out
  } else if(isset($_GET['logout']) && $_SESSION['Authenticated'] == true) {
        session_destroy(); 
        echo "You have been logged out";
        $_SESSION['Authenticated'] = false;
  //Just landing on the page, right now it logs out and redirects to index.php
  //You could make this code echo some 404 Not found error to make a dummy and hide your file name from hackers...  
  } else{  
        $_SESSION['Authenticated'] = false;  
        //header("Location: index.php");
        session_write_close();
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
  }
     

  
   
  
?>
