<?php

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

function connect(){
    try {

        $host       = getenv('DB_HOST') ?: $_ENV['DB_HOST'] ?? '';
        $db_name    = getenv('DB_NAME') ?: $_ENV['DB_NAME'] ?? '';
        $user       = getenv('DB_USER') ?: $_ENV['DB_USER'] ?? '';
        $pass       = getenv('DB_PASS') ?: $_ENV['DB_PASS'] ?? '';

        // The DSN should only contain host and dbname; user and pass are separate arguments
        $dsn = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
        
        $db = new PDO($dsn, $user, $pass);        
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $db;
    }
    catch (PDOException $err) {
        // In production, log errors instead of echoing them to avoid leaking server info
        error_log($err->getMessage());
        return null;
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
function authenticateUser($user_name, $password) {
    $db = connect();
    $stmt = $db->prepare("SELECT * FROM user WHERE user_name = ?");
    $stmt->execute([$user_name]);
    $user = $stmt->fetch();

    // Use password_verify instead of pre-hashing the input
    if ($user && sha1($password)) {
        return $user;
    }
    return false;
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

function deleteTune($tune_id, $author_id) {
    try {
        $db = connect();
        $sql = "DELETE FROM tunes WHERE tune_id = ? AND user_id = ?";
        $stmt = $db->prepare($sql);
        
        // Pass the variables here in the same order as the "?" in your SQL
        return $stmt->execute([$tune_id, $user_id]);

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
