<link href="js/lib/src/jquery.password.css" rel="stylesheet" type="text/css"/>
<link href="css/registration.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="js/lib/src/jquery.password.js"></script>
<?php
    include_once(__DIR__ . '/../../helpers/tune_helpers.php')
?>
<script type="text/javascript" src="js/modules/register.js"></script>
<h3>Registration Form</h3>
<table id="registration_form" class="form">
<form>
    <label>First Name: </label>    
    <span ><input id="first_name" type="text" name="first_name"/></span><br /> 
    <label>Last Name: </label>
    <span><input id="last_name" type="text" name="last_name"/></span><br />
    <label>Email: </label>    
    <span><input id="email" type="text" name="email"/></span><br />
    <label>Username: </label>
    <span><input id="user_name" type="text" name="username"/></span><br />
    <label>Password: </label>
    <span id="pass_span"><input id="password" type="password" name="password"/><br />
    <span id="strength_meter">
    </span></span><br />
    <label>Confirm Password: </label>
    <span><input id="pass_conf" type="password" name="password_confirm"/></span>
    <input type="hidden" id="register" value="true"/><br /> 
    <input id="register_btn" type="button" value="Sign me up"/>
    
    
</form>
</table>
<br />



