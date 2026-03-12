$(document).ready(function(){           
    $('#login_btn').click(function(){
        
          $.post(
              "auth.php",
              {
                "user_name":$("#user_name").val(),
                "password":$("#password").val(),
                "login":true
              },
              function(data){
                  //if the user landed on auth.php then put out a dummy 404 error 
                  if(data == "<h1>Not Found</h1><p>The requested URL /mytunebook/auth.php was not found on this server.</p><hr><address>Apache/2.2.22 (Ubuntu) Server at localhost Port 80</address>"){
                  $('warnings').html('<h1>Not Found</h1><p>The requested URL /mytunebook/auth.php was not found on this server.</p><hr><address>Apache/2.2.22 (Ubuntu) Server at localhost Port 80</address>')
                  }else{
                      var resp = String(data);//must be converted to a string
                      if(resp.substring(12,0) == "Welcome back" || resp.substring(25,0) == "You are already logged in"){//Just test for the welcome string
                          alert(data);
                          $("#save_or_login").html('<input type="button" value="save" id="save"/>');
                          
                          //This functionality already exists in the abc_editor.js file but it has to be reinstantiated because the element was reloaded after the user logged in
                          $('#save').on('click', function(){
                              var tune_body = $('#abc').val().replace(/\n/g, '<br />');//force line breaks
                              $.post(
                                  "add_tune.php",
                                  {
                                    "tune_title":$("#tune_title").val(),
                                    "tune_type":$("#tune_type").val(),
                                    "composer":$("#composer").val(),
                                    "metre":$('#metre').val(),
                                    "default_note_length":'1/8',
                                    "tune_key":$('#key').val(),
                                    "tune_body": tune_body
                                  },
                                  function(data){
                                    if(data == "Thank you. Your tune was submitted"){
                                        alert(data);
                                        $('#content').load('tunes.php');
                                    }else{
                                        alert(data);
                                    }
                  
                  
                                  }
                              );
                          })
                      }else{
                          alert(data);
                      }                      
                  } 
              }
          );
          
         
    });
    //Enter button on password input field
    $("#password").keyup(function(e){
        if(e.keyCode == 13){
            $("#login_btn").click();
        }
    });
    //Enter button on user_name input field
    $("#user_name").keyup(function(e){
        if(e.keyCode == 13){
            $("#login_btn").click();
        }
    });    
})


