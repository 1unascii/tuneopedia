<!--link href="css/links.css" rel="stylesheet" type="text/css"/-->
<script type="text/javascript" src="js/modules/nav.js?v=13"></script>
    <div class="nav-container">

        <div class='nav' id='home_link'>Home</div>
        <div class='nav' id='tunes_link'>Tunes</div>
        <div class='nav' id='discussion_link'>Discussion</div>
<?php
    //IF LOGGED IN
    if(array_key_exists('Authenticated', $_SESSION)){ 
?>      
        <div class='nav' id='logout_link'>Log Out</div>
        <div class='nav' id='add_tune_link'>Add Tune</div>
        <div class='nav' id='my_tunes_link'>Favorites</div>
        <div class='nav' id='add_collection_link'>Add Collection</div>
<?php        
    //IF NOT LOGGED IN      
    }else{    
?>
        <div class='nav' id='login_link'>Log In</div>
        <div class='nav' id='register_link'>Register</div>
           
<?php        
    }
?>
    <div class='nav' id='collections_link'>Collections</div>
    
    </div>

