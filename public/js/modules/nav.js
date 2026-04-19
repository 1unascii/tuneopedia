$(document).ready(function () {

    var base       = (typeof APP_BASE    !== 'undefined') ? APP_BASE    : '';
    var initialSrc = (typeof INITIAL_SRC !== 'undefined') ? INITIAL_SRC : 'home';

    var routes = {
        'home'           : 'page/home',
        'tunes'          : 'page/tunes',
        'collections'    : 'page/collections',
        'login'          : 'fragment/login',
        'register'       : 'fragment/registration',
        'add-tune'       : 'fragment/abc-editor',
        'my-tunes'       : 'page/my-tunes',
        'discussion'     : 'page/discussion',
        'add-collection' : 'fragment/add-collection',
    };

    function loadContent(src, urlPath, push) {
        $('#content').fadeTo(300, 0, function () {
            $('#content').load(src, function () {
                $('#content').fadeTo(300, 1);
            });
        });
        if (push) {
            history.pushState({ src: src }, '', base + '/' + urlPath);
        }
    }

    // Resolve the initial route to an AJAX-loadable URL
    var currentPath = window.location.pathname.replace(base, '').replace(/^\//, '');
    var threadMatch = currentPath.match(/^discussion\/(\d+)$/);
    var initialRoute;

    if (threadMatch) {
        initialRoute = 'page/discussion-thread?thread_id=' + threadMatch[1];
    } else {
        initialRoute = routes[initialSrc] || ('page/' + initialSrc);
    }

    history.replaceState({ src: initialRoute }, '', window.location.pathname);

    if (routes[currentPath] && currentPath !== initialSrc && currentPath !== '') {
        $('#content').load(routes[currentPath]);
    }

    $(window).on('popstate', function (e) {
        var state = e.originalEvent.state;
        if (state && state.src) {
            $('#content').fadeTo(300, 0, function () {
                $('#content').load(state.src, function () {
                    $('#content').fadeTo(300, 1);
                });
            });
        }
    });

    $('#home_link').on('click', function () {
        loadContent('page/home', 'home', true);
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
        loadContent('page/my-tunes', 'my-tunes', true);
    });

    $('#discussion_link').on('click', function () {
        loadContent('page/discussion', 'discussion', true);
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
