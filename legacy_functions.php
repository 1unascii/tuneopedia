<?php
// Legacy helper functions retained for reference.
// These are not currently used by the live application.

function curPageURL() {
    $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
            $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
    return $pageURL;
}

function sortByGET($get){
    if(!$get['sort']){
        $result = simpleQuery("SELECT * FROM tunes");
    }else if ($get['sort_title'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY tune_title ASC;");
    }else if ($get['sort_type'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY tune_type ASC;");
    }else if ($get['sort_metre'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY metre ASC;");
    }else if ($get['sort_length'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY default_note_length ASC;");
    }else if ($get['sort_composer'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY composer ASC;");
    }else if ($get['sort_source'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY source ASC;");
    }else if ($get['sort_transcriber'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY transcriber ASC;");
    }else if ($get['sort_country'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY country ASC;");
    }else if ($get['sort_author_id'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY author_id ASC;");
    }else if ($get['sort_instrument'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY instrument ASC;");
    }else if ($get['sort_key'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY `key` ASC;");
    }else if ($get['sort_body'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY body ASC;");
    }else if ($get['sort_rank'] == 'asc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY rank ASC;");    
    }else if ($get['sort_title'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY tune_title DESC;");
    }else if ($get['sort_type'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY type DESC;");
    }else if ($get['sort_metre'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY metre DESC;");
    }else if ($_GET['sort_length'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY length DESC;");
    }else if ($get['sort_composer'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY composer DESC;");
    }else if ($get['sort_nationality'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY nationality DESC;");
    }else if ($get['sort_author_id'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY author_id DESC;");
    }else if ($get['sort_instrument'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY instrument DESC;");
    }else if ($get['sort_key'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY `key` DESC;");
    }else if ($get['sort_body'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY body DESC;");
    }else if ($get['sort_rank'] == 'desc'){
        $result = simpleQuery("SELECT * FROM tunes ORDER BY rank DESC;");
    }
}

function tuneSort($column, $order){
    $result = simpleQuery("SELECT * FROM tunes ORDER BY $column $order");
}

function sanitizeString($input, $i){   
    if($input){       
        $input = filter_var($input, FILTER_SANITIZE_STRING);        
        return $input;    
    }else{
        $_SESSION['errors'][$i]['empty'] = "Empty";
        return false;
    }   
}

function sanitizeEmail($input, $i){   
    if($input){       
        $input = filter_var($input, FILTER_SANITIZE_EMAIL);        
        return $input;    
    }else{
        $_SESSION['errors'][$i]['empty'] = "Empty";
        return false;
    }      
}

function sanitizeInput($input, $type, $index, $message){
    
    if ($input){
        if ($type = 'string'){
            if(sanitizeString($input, $index)){
                $output = $input;
                return $output;
            } else {
                $_SESSION['errors'][$index]['invalid'] = $message;
                return false;
            }
        }
        if ($type = 'email'){
            if(sanitizeEmail($input, $index)){
                $output = $input;
                return $output;
            } else {
                $_SESSION['errors'][$index]['invalid'] = $message;
                return false;
            }
        }
    } else {
        $_SESSION['errors'][$index]['empty field'] = 'empty, '.$message;
        return false;
    }
}

function sanitizeRegInp($input){
    if ($input){        
        
        $output['first_name'] = sanitizeInput($input['first_name'], 'string', 'first_name', 'please enter a valid first name');
        $output['last_name'] = sanitizeInput($input['last_name'], 'string', 'last_name', 'please enter a valid last name');               
        $output['email'] = sanitizeInput($input['email'], 'email', 'email', 'please enter a valid email address');        
        $output['username'] = sanitizeInput($input['username'], 'string', 'user_name', 'please enter a valid user name');        
        
        if ($input['password']){
            if ($input['password'] == $input['password_confirm']){
                $output['password'] = $input['password'];
            } else if ($input['password'] !== $input['password_confirm']){
                $_SESSION['errors']['password'] = "Your passwords did not match";
            }
        }else{
            $_SESSION['errors']['password'] = "You did not enter a password";
        }
        
        $index_names = array(0=>'first_name', 1=>'last_name', 2=>'email', 3=>'username', 4=>'password');
        for ($i=0; $i < count($index_names); $i++){
            if (!$output[$index_names[$i]]){          
                unset($output[$index_names[$i]]);
            }
        }    
        return $output;
        
    }else{
        $_SESSION['errors'] = "The form was empty";
        return false;
    }
}

function tabularizeFormRow($label, $field){
    return "
    <tr>
        <td>
            <label>$label</label> 
        </td>
        <td>
            $field
        </td>
    </tr>";
}

function errorOutput($input){
    if (is_array($input)){
        foreach($input as $value){
            echo tabularizeFormRow('Error: ', $value);
        }
    } else if ($input){
        echo tabularizeFormRow('Error: ', $input);
    }   
    return $output;
}

function print_r_pre($input, $label){
    ?>
        <pre>
            <?php
            echo $label;
                print_r($input);
            ?>
        </pre>
    <?php
}

function displayTableNavButtons($get, $i, $label){
?>
    <td class="table_heading">
    
        <a href="index.php?<?php echo $i;?>=<?php if($get[$i] == 'asc')
            {echo 'desc';}else{echo 'asc';}?>&sort=1">

            <?php echo $label;?>
        </a>
    </td>
<?php
}

function createTableNavButtons($get){
    echo '<tr>';
    displaytablenavbuttons($get, 'sort_title', 'Title');
    displaytablenavbuttons($get, 'sort_type', 'Type');
    displaytablenavbuttons($get, 'sort_metre', 'Metre');
    displaytablenavbuttons($get, 'sort_length', 'Length');
    displaytablenavbuttons($get, 'sort_composer', 'Composer');
    displaytablenavbuttons($get, 'sort_nationality', 'Nationality');
    displaytablenavbuttons($get, 'sort_instrument', 'Instrument');
    displaytablenavbuttons($get, 'sort_key', 'Key');
    displaytablenavbuttons($get, 'sort_author_id', 'Submitted By');
    displaytablenavbuttons($get, 'sort_rank', 'Rank');
    echo '</tr>';
}
