<?php

/*CONNECT TO THE DATABASE, return connection object*/
function connect(){
    try{
        $db = new PDO('mysql:dbname=tunebook;host=localhost', 'student', 'student');        
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
    catch ( PDOException $err){
        echo $err->getCode();
        echo $err->getMessage(); 
    }
}

//creates an array of incremental numbers based on the size of the array passed to it
function createParamArray($params){
    $i = 0;
    foreach ($params as $value){
        $result[] = "$i";
        $i++;
    }
    return $result;
}

/*AUTHENTICATE USER, needs a special function for security purposes*/
function authenticateUser($user_name, $password){
    
    try {
        $db = connect();//get the connection object
        $query = $db->prepare("SELECT * FROM users WHERE user_name = :user_name AND password = UNHEX(:password)");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query->bindValue(':user_name', $user_name);
        $query->bindValue(':password', $password);
        
        if($query->execute()){
            while ($row = $query->fetch(PDO::FETCH_ASSOC)){
                return $row;
            }
            //return $row;
        }else{
            return false;
        }
    }
    catch (PDOException $err){
        echo $err->getCode();
        echo $err->getMessage(); 
    }  
}

function simpleQuery($query){
    try{
        $db = connect();
        $query = $db->prepare($query);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if($query->execute()){
            while ($row = $query->fetchALL(PDO::FETCH_ASSOC)){
                return $row;
            }            
        }else{
            return false;
        }       
    }    
    catch ( PDOException $err){
        echo $err->getCode();
        echo $err->getMessage(); 
    }
}

/*function deleteQuery($query){
    try{
        $db = connect();
        $query = $db->prepare($query);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if($query->execute()){
            return true;
        }else{
            return false;
        }       
    }    
    catch ( PDOException $err){
        echo $err->getCode();
        echo $err->getMessage(); 
    }
}*/

function deleteTune($tune_id, $author_id) {
    try {
        $db = connect();
        $sql = "DELETE FROM tunes WHERE tune_id = ? AND author_id = ?";
        $stmt = $db->prepare($sql);
        
        // Pass the variables here in the same order as the "?" in your SQL
        return $stmt->execute([$tune_id, $author_id]);

    } catch (PDOException $err) {
        // Keeping your error reporting for debugging
        echo $err->getCode() . " " . $err->getMessage(); 
        return false; 
    }
}

function queryWithParams($query, $params){
    try{
        $db = connect();
        $q = $db->prepare($query);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $i = 0;
        if (is_array($params)){//allows for an array of parameters to be passed through
            $param_arr = createParamArray($params);//creates string indexes to append to param
            $i = 0;
            foreach ($params as $value){
                $q->bindValue(":param". $param_arr[$i], $value);//append the next string index to the :param
                $i++;
            }        
        } else {
            $q->bindValue(":param", $params);//$parameter is not an array so pass it through normally
        }
        if($q->execute()){ 
            while ($row = $q->fetch(PDO::FETCH_ASSOC)){
                return $row;
            }     
        }else{
            return false;
        }
    }
    catch ( PDOException $err){
        echo $err->getCode();
        echo $err->getMessage(); 
    }
}
function insertQuery($query, $params){
    try{
        $db = connect();
        $q = $db->prepare($query);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $i = 0;
        if (is_array($params)){//allows for an array of parameters to be passed through
            $param_arr = createParamArray($params);//creates string indexes to append to param
            $i = 0;
            foreach ($params as $value){
                $q->bindValue(":param". $param_arr[$i], $value);//append the next string index to the :param
                $i++;
            }        
        } else {
            $q->bindValue(":param", $params);//$parameter is not an array so pass it through normally
        }
        if($q->execute()){ 
            return true;
        }else{
            return false;
        }
    }
    catch ( PDOException $err){
        //echo $err->getCode();
        //echo $err->getMessage(); 
    }
}
?>
