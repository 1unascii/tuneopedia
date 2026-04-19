$(document).ready(function(){
    var error = false;
    var errors = [];
    var password_err_bool = false;
    var password_strength_err = false;
    var password_strength_err_msg = "<span id='pass_strength_err' class='ui-state-error' style='padding-right: 5px;'>"
                + "<span class='ui-icon ui-icon-alert' style='display: inline-block; " 
                + "margin-left: 5px; margin-right: 5px;'></span><span>Your password did not meet requirements</span></span>";
    $('#password').password({
        minLength:8, //the minimum length of the password
        allowSpace:false, //whether or not a space character is allowed in the password
        strengthIndicator:$('#strength_meter'), //the element to turn into a strength meter
        //checklist:$('#myChecklist'), //the element to turn into a checklist
        dictionary:'js/src/dictionaries/en.js', //a JSON file which has a list of disallowed words
        //doubleType:$('#pass_conf'), //a second password field to allow for double typing checking //only works for checklist mode I think
        personalInformation:[ //personal information to stop the user from using
            $('#first_name'), 
            $('#last_name'),
            $('#email'),
            $('#user_name')
        ],
        change:function(score, issues, pass) { //the function which is called when the password changes
            if(!pass){
                password_strength_err= true;
            }else if(pass){
                password_strength_err = false;
                if($('#pass_strength_err').length){
                    $('#pass_strength_err').remove();
                }
            }
        }
    });
    
    //passwords match
    $('#pass_conf, #password').on('keyup', function(){
        if($('#pass_conf').val() !== $('#password').val() && !$('#pass_match_err').length){
            error = "<span id='pass_match_err' class='ui-state-error' style='padding-right: 5px;'>"
            + "<span class='ui-icon ui-icon-alert' style='display: inline-block; " 
            + "margin-left: 5px; margin-right: 5px;'></span><span>Passwords do not match</span></span>";
            $('#pass_conf').after(error);
        }else if($('#pass_conf').val() == $('#password').val() && $('#pass_match_err').length){
            $('#pass_match_err').remove();
            password_err_bool = false;
            error = false;
        }
    });
        
    $('#register_btn').click(function(){
            
        function validateTextBox(textBoxSelector, errorMsg){
            var errorId = textBoxSelector.substring(textBoxSelector.length, 1) + "_err";
            var errorSelector = "#" + errorId;
            if(!$(textBoxSelector).val() && !$(errorSelector).length){            
                error = "<span id=" + errorId + " class='ui-state-error' style='padding-right: 5px;'>"
                + "<span class='ui-icon ui-icon-alert' style='display: inline-block; " 
                + "margin-left: 5px; margin-right: 5px;'></span><span>" + errorMsg + "</span></span>";
                $(textBoxSelector).after(error);          
            }else if($(textBoxSelector).val() && $(errorSelector).length) {            
                $(errorSelector).remove();
                error = false;       
            }else if(!$(textBoxSelector).val() && $(errorSelector).length){
                error = true;
            }  
        }

        errors['first_name'] = validateTextBox("#first_name", "Please enter your first name");
        errors['last_name'] = validateTextBox("#last_name", "Please enter your last name");
        errors['email'] = validateTextBox("#email", "Please enter your email address");
        errors['user_name'] = validateTextBox("#user_name", "Please choose a username");

        validateTextBox("#password", "Please enter a password");
        if(password_strength_err && !$('#pass_strength_err').length){
            $('#strength_meter').after(password_strength_err_msg);
        }

        for(var i=0; i<errors.length; i++){
            if(errors[i] == true){
                error = true;
            }
        }
        
	    
        if(error == false && password_err_bool == false && password_strength_err == false){
            $.post(
                "api/register",
                {
                "first_name":$("#first_name").val(),
                "last_name":$("#last_name").val(),
                "email":$('#email').val(),
                "user_name":$('#user_name').val(),
                "password":$('#password').val(),
                "register":true
                },
                function(data){
                  if(data == "Thank you for signing up"){
                      alert(data);
                      $('#content').load('fragment/login');
                  }else{
                      alert(data);
                  }
                  
                }
            );    
        }
            
                
    });
    //Enter button on password input field
    $("#password_confirm").keyup(function(e){
        if(e.keyCode == 13){
            $("#register_btn").click();
        }
    });
})

