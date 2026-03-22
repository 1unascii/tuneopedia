<?php
    session_start(); 
    include_once('connect.php');
    include_once('functions.php');//Only because the form is loaded with AJAX
    if ($_POST['register']){        
        $params = array(
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'user_name' => $_POST['user_name'],
            'email' => $_POST['email'],
            
            'password' => sha1($_POST['password'])
            );   
        
        //insert NULL for auto increment ID, first name, last name, email, username, unhex(sha1(password))
        $query = "INSERT INTO user VALUES(
                NULL,
                :param0,
                :param1,
                :param2,
                :param3,
                UNHEX(:param4)
            );";
        
        if(insertQuery($query,  $params)){
            echo 'Thank you for signing up';
        }else{
            if(queryWithParams("SELECT * FROM user WHERE email = :param", $params['email'])){
                echo 'The email you entered is already in use';
            } 
            if(queryWithParams("SELECT * FROM user WHERE user_name = :param", $params['user_name'])){
                echo 'The username you entered is already in use';
            }            
        }
        
    } else {
        echo "There was an unexpected error, or you don't have permission to access this page";
    }                
  
?>
