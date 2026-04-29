// Login form handlers are delegated on $(document) so they work when
// forms/login.php is injected into #content via jQuery .load().

$(document).on('click', '#login_btn', function () {
    $.post(
        'api/auth/login',
        {
            user_name: $('#user_name').val(),
            password:  $('#password').val(),
            login:     true
        },
        function (data) {
            $('<div class="alert-box">' + data + '</div>')
                .appendTo('#pop_up')
                .delay(1500)
                .fadeOut(300, function () {
                    $(this).remove();
                    if (data === 'Login successful!') {
                        window.location.href = '.';
                    }
                });
        }
    );
});

$(document).on('keyup', '#password, #user_name', function (e) {
    if (e.keyCode === 13) {
        $('#login_btn').click();
    }
});
