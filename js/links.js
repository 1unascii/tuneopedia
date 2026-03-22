$(document).ready(function(){
        
    if($('#login_link').length){
        $('#login_link').click(function(){
            $('#content').load('forms/login.php');
        })
    }
    if($('#register_link').length){
        $('#register_link').click(function(){
            $('#content').load('forms/registration.php');
        })
    }
    if($('#add_tune_link').length){
        $('#add_tune_link').click(function(){
            $('#content').load('forms/abc_editor.php');
        })
    }
    if($('#my_tunes_link').length){
        $('#add_tune_link').click(function(){
            $('#content').load('forms/abc_editor.php');
        })
    }
    if($('#discussion_link').length){
        $('#add_tune_link').click(function(){
            $('#content').load('forms/abc_editor.php');
        })
    }
    




    if($('#logout_link').length){
        $('#logout_link').click(function(){
            $.get(
              "auth.php",
              {
                "logout":true
              },
              function(data){
                alert(data);
                window.location.href ="index.php";
              }
          );
        //$(document).load('index.php');
        })
    }
})
    
