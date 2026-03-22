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
                  $('body').html('<h1>Not Found</h1><p>The requested URL /mytunebook/auth.php was not found on this server.</p><hr><address>Apache/2.2.22 (Ubuntu) Server at localhost Port 80</address>')
                  }else{
                      var resp = String(data);//must be converted to a string
                      if(resp.substring(12,0) == "Welcome back" || resp.substring(25,0) == "You are already logged in"){//Just test for the welcome string
                          alert(data);
                          window.location.href ="index.php";
                      }else{
                          alert(data);
                          window.location.href ="index.php";
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


