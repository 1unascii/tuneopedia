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
        'discussions'    : 'page/discussions',
        'add-collection' : 'fragment/add-collection',
    };

    // Map URL paths to their nav link IDs
    var navLinkMap = {
        'home':           '#home_link',
        'tunes':          '#tunes_link',
        'collections':    '#collections_link',
        'discussions':    '#discussion_link',
        'my-tunes':       '#my_tunes_link',
        'login':          '#login_link',
        'register':       '#register_link',
        'add-tune':       '#add_tune_link',
        'add-collection': '#add_collection_link',
    };

    function setActiveNav(urlPath) {
        $('.nav').removeClass('nav-active');
        var selector = navLinkMap[urlPath];
        if (selector) {
            $(selector).addClass('nav-active');
        }
    }

    function loadContent(src, urlPath, push) {
        setActiveNav(urlPath);
        $('#content').fadeTo(300, 0, function () {
            $('#content').load(src, function () {
                $('#content').fadeTo(300, 1);
            });
        });
        if (push) {
            history.pushState({ src: src, urlPath: urlPath }, '', base + '/' + urlPath);
        }
    }

    // Resolve the initial route to an AJAX-loadable URL
    var currentPath = window.location.pathname.replace(base, '').replace(/^\//, '');
    var threadMatch = currentPath.match(/^discussions\/(\d+)$/);
    var initialRoute;

    if (threadMatch) {
        initialRoute = 'page/discussion-thread?thread_id=' + threadMatch[1];
    } else {
        initialRoute = routes[initialSrc] || ('page/' + initialSrc);
    }

    history.replaceState({ src: initialRoute, urlPath: initialSrc }, '', window.location.pathname);

    // Set the active nav for the initial page
    setActiveNav(currentPath || initialSrc);

    if (routes[currentPath] && currentPath !== initialSrc && currentPath !== '') {
        setActiveNav(currentPath);
        $('#content').load(routes[currentPath]);
    }

    $(window).on('popstate', function (e) {
        var state = e.originalEvent.state;
        if (state && state.src) {
            if (state.urlPath) setActiveNav(state.urlPath);
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
        loadContent('page/discussions', 'discussions', true);
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

    // ── Theme toggle ─────────────────────────────────────────────────────────

    var darkThemeUrl = 'css/themes/ui-darkness/jquery-ui-1.10.3.custom.css';
    var lightThemeUrl = 'css/themes/ui-smoothness/jquery-ui.min.css';

    function applyTheme(theme) {
        if (theme === 'light') {
            $('body').addClass('light-mode');
            $('#jquery-ui-theme').attr('href', lightThemeUrl);
            $('#theme-icon').attr('class', 'fa-solid fa-moon');
        } else {
            $('body').removeClass('light-mode');
            $('#jquery-ui-theme').attr('href', darkThemeUrl);
            $('#theme-icon').attr('class', 'fa-solid fa-sun');
        }
        localStorage.setItem('tuneopedia-theme', theme);
    }

    // Apply icon state on load (body class already applied by inline script in header)
    if (localStorage.getItem('tuneopedia-theme') === 'light') {
        $('#theme-icon').attr('class', 'fa-solid fa-moon');
    }

    $('#theme-toggle').on('click', function () {
        var current = $('body').hasClass('light-mode') ? 'light' : 'dark';
        applyTheme(current === 'light' ? 'dark' : 'light');
    });

});
