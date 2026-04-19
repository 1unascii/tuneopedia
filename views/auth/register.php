<link href="css/tune-page.css?v=6" rel="stylesheet" type="text/css"/>
<link href="css/registration.css?v=2" rel="stylesheet" type="text/css"/>
<link href="js/lib/src/jquery.password.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="js/lib/src/jquery.password.js"></script>
<?php
    include_once(__DIR__ . '/../../helpers/tune_helpers.php')
?>
<script type="text/javascript" src="js/modules/register.js"></script>

<div id="form_wrapper">
    <h2>Registration</h2>
    <form id="registration_form" class="edit-setting-form">

        <div class="edit-field">
            <label for="first_name">First Name</label>
            <input id="first_name" type="text" name="first_name" required />
        </div>

        <div class="edit-field">
            <label for="last_name">Last Name</label>
            <input id="last_name" type="text" name="last_name" required />
        </div>

        <div class="edit-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" required />
        </div>

        <div class="edit-field">
            <label for="user_name">Username</label>
            <input id="user_name" type="text" name="username" required />
        </div>

        <div class="edit-field">
            <label for="password">Password</label>
            <span id="pass_span">
                <input id="password" type="password" name="password" required />
            </span>
            <span id="strength_meter"></span>
        </div>

        <div class="edit-field">
            <label for="pass_conf">Confirm Password</label>
            <input id="pass_conf" type="password" name="password_confirm" required />
        </div>

        <input type="hidden" id="register" value="true" />

        <div class="edit-form-actions">
            <button type="button" id="register_btn" class="edit-save-btn">Sign me up</button>
        </div>

    </form>
</div>
