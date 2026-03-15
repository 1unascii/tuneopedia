<?php
session_start();
include_once('connect.php');
    $tune_type      = $_POST['tune_type'];
    $query          = "SELECT * FROM tune_types WHERE tune_type = '$tune_type'" ;
    $tune_type      = simpleQuery($query);
    $tune_type_id   = $tune_type[0]['tune_type_id'];    
    $composer       = $_POST['composer'];
    $query          = "SELECT * FROM composers WHERE composer_name = '$composer'";
    $composer       = simpleQuery($query);
    
    if(isset($composer[0])){
       $composer_id = $composer[0]['composer_id']; 
       $params = array(    
                'tune_title'            => $_POST['tune_title'],        
                'tune_type'             => $tune_type_id,
                'author_id'             => $_SESSION['author_id'],    
                'composer_id'           => $composer_id,
                'metre'                 => $_POST['metre'],   
                'default_note_length'   => $_POST['default_note_length'],
                'tune_key'              => $_POST['tune_key'],          
                'tune_body'             => $_POST['tune_body'],
                'audio'                 => 'None',                          
                'video'                 => 'None'); 
    } else {
    $params = array(    
                'tune_title'            => $_POST['tune_title'],        
                'tune_type'             => $tune_type_id,
                'author_id'             => $_SESSION['author_id'],    
                'composer_id'           => NULL,
                'metre'                 => $_POST['metre'],   
                'default_note_length'   => $_POST['default_note_length'],
                'tune_key'              => $_POST['tune_key'],          
                'tune_body'             => $_POST['tune_body'],
                'audio'                 => 'None',                          
                'video'                 => 'None'); 
    }
    $query = "INSERT INTO tunes VALUES(NULL,:param0,:param1,:param2,:param3,:param4,:param5,
        :param6,:param7,:param8,:param9);";    
    
    if(insertQuery($query,  $params)){
        echo 'Thank you. Your tune was submitted';
        //echo $tune_type_id;
    }else{
        echo 'There was an error with your tune submission';
    }
?>
