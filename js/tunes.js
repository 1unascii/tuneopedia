$(document).ready(function(){

   $('.show_abc').click(function() {
        var setting_id = $(this).attr('id');
        var $thisSpan = $(this); // ← must be here, BEFORE $.post
        $.post("get_tune_body.php", { "setting_id": setting_id }, function(data) {
            if (data) {
                var tune = jQuery.parseJSON(data);

                //ABCJS.renderAbc("paper", tuneAbc);
                ABCJS.renderAbc("paper", 
                    "X:" + tune.setting_id + "\n" +
                    "T:" + tune.name + "\n" +
                    "M:" + tune.time_signature + "\n" +
                    "L: 1/8<br />" +
                    "K:" + tune.key_signature + "\n" +
                    tune.abc_transcription
                );

                
                // Insert close button only after the specific clicked span
                $thisSpan.after("<span class='ui-icon ui-icon-circle-close show-abc-close' style='display: inline-block;'></span>");

                // Scope the click handler to only the close button we just added
                $thisSpan.next(".show-abc-close").on("click", function() {
                    $("#paper").empty();
                    $(this).remove();
                });
            }
        });
    });

    
    
    
    $(".tune-favorite-icon").on("click", function () {
        var result = confirm("Are you sure you want to add this tune to your favorites?");    
        var userId = $('#user-info').data('user-id');
        if(result){
            $.post(
                "favorite_tune.php",
                {
                    "tune_id":$(this).parent().parent().attr("id"),
                    "user_id":userId
                },
                
                function(data){
                    //display a custom popup to the screen
                    alert(data);
                    
                }
            );         
        }

    });
    
});
