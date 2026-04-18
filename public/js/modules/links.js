$(document).ready(function () {

    var base       = (typeof APP_BASE    !== 'undefined') ? APP_BASE    : '';
    var initialSrc = (typeof INITIAL_SRC !== 'undefined') ? INITIAL_SRC : 'tunes';

    var routes = {
        'tunes'          : 'page/tunes',
        'collections'    : 'page/collections',
        'login'          : 'fragment/login',
        'register'       : 'fragment/registration',
        'add-tune'       : 'fragment/abc-editor',
        'my-tunes'       : 'my_tunes.php',
        'discussion'     : 'forum.php',
        'add-collection' : 'fragment/add-collection',
    };

    function loadContent(src, urlPath, push) {
        $('#content').load(src);
        if (push) {
            history.pushState({ src: src }, '', base + '/' + urlPath);
        }
    }

    history.replaceState({ src: initialSrc }, '', window.location.pathname);

    var currentPath = window.location.pathname.replace(base, '').replace(/^\//, '');
    if (routes[currentPath] && initialSrc === 'tunes' && currentPath !== 'tunes' && currentPath !== '') {
        $('#content').load(routes[currentPath]);
    }

    $(window).on('popstate', function (e) {
        var state = e.originalEvent.state;
        if (state && state.src) {
            $('#content').load(state.src);
        }
    });

    $('#tunes_link').on('click', function () {
        loadContent('page/tunes', 'tunes', true);
    });

    $('#collections_link').on('click', function () {
        loadContent('page/collections', 'collections', true);
    });

    $('#login_link').on('click', function () {
        loadContent('fragment/login', 'login', true);
    });

    $('#register_link').on('click', function () {
        loadContent('fragment/registration', 'register', true);
    });

    $('#add_tune_link').on('click', function () {
        loadContent('fragment/abc-editor', 'add-tune', true);
    });

    $('#my_tunes_link').on('click', function () {
        loadContent('my_tunes.php', 'my-tunes', true);
    });

    $('#discussion_link').on('click', function () {
        loadContent('forum.php', 'discussion', true);
    });

    $('#add_collection_link').on('click', function () {
        loadContent('fragment/add-collection', 'add-collection', true);
    });

    $('#logout_link').on('click', function () {
        $.get(
            'api/auth',
            { logout: true },
            function (data) {
                $('<div class="alert-box">' + data + '</div>')
                    .appendTo('#pop_up')
                    .delay(500)
                    .fadeOut(150, function () {
                        $(this).remove();
                        window.location.href = base + '/';
                    });
            }
        );
    });

});
