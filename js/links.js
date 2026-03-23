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
        $('#my_tunes_link').click(function(){
            $('#content').load('my_tunes.php');
        })
    }
    if($('#discussion_link').length){
        $('#discussion_link').click(function(){
            $('#content').load('forum.php');
        })
    }
    if($('#tunes_link').length){
        $('#tunes_link').click(function(){
            $('#content').load('tunes.php');
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

                $('<div class="alert-box">' + data + '</div>')
                .appendTo('#pop_up')
                .delay(500) // Stay visible for 3 seconds
                .fadeOut(150, function() {
                    $(this).remove(); // Clean up the HTML after it's invisible
                    window.location.href = "index.php"; // Redirect AFTER the fade
                });
                //alert(data);
                //window.location.href ="index.php";
              }
          );
        //$(document).load('index.php');
        })
    }
})
    
