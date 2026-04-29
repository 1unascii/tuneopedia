// ══════════════════════════════════════════════════════════════════════════════
// Client-Side Router (nav.js)
//
// This is a Single Page Application (SPA) router. After the initial full page
// load, all navigation happens via AJAX — only the #content div is swapped.
//
// How it works:
//   1. User clicks a nav link (e.g. "Tunes")
//   2. loadContent() fades out #content, fetches "page/tunes" via $.load(),
//      fades the new content in, and pushes a history entry
//   3. The server's "page/*" route returns ONLY the content HTML (no header/nav)
//   4. Browser back/forward triggers popstate, which reloads from the saved state
//
// The `routes` object maps URL paths to server endpoints:
//   - "page/*"     → page routes (content HTML from controllers)
//   - "fragment/*" → fragment routes (form HTML from view files)
//
// On initial page load, the server renders the full page (header + nav +
// content + footer). INITIAL_SRC tells this script what was rendered so it
// doesn't re-fetch it. If the browser URL doesn't match INITIAL_SRC (e.g.
// user bookmarked /collections but server always renders the route's content),
// the script detects the mismatch and loads the correct content.
// ══════════════════════════════════════════════════════════════════════════════

$(document).ready(function () {

    // APP_BASE is set by the server in a <script> tag (e.g. "/tuneopedia")
    // INITIAL_SRC is the route the server rendered on this page load (e.g. "home", "tunes")
    var base       = (typeof APP_BASE    !== 'undefined') ? APP_BASE    : '';
    var initialSrc = (typeof INITIAL_SRC !== 'undefined') ? INITIAL_SRC : 'home';

    // ── Route table ──────────────────────────────────────────────────────────
    // Maps clean URL paths to the server endpoint that returns the content.
    // "page/*" endpoints return content-only HTML (no layout wrapper).
    // "fragment/*" endpoints return form HTML snippets.
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

    // ── Active nav highlighting ──────────────────────────────────────────────
    // Maps URL paths to the jQuery selector for their nav link element.
    // Used to highlight the current page's link in orange.
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

    // Remove nav-active from all links, then add it to the matching one
    function setActiveNav(urlPath) {
        $('.nav').removeClass('nav-active');
        var selector = navLinkMap[urlPath];
        if (selector) {
            $(selector).addClass('nav-active');
        }
    }

    // ── Core navigation function ─────────────────────────────────────────────
    // Fades out #content, loads new content via AJAX, fades it back in.
    // If push=true, adds a browser history entry so back/forward work.
    function loadContent(src, urlPath, push) {
        setActiveNav(urlPath);
        var loadUrl = src.indexOf('://') !== -1 ? src : base + '/' + src;
        $('#content').fadeTo(300, 0, function () {
            $('#content').load(loadUrl, function () {
                $('#content').fadeTo(300, 1);
            });
        });
        if (push) {
            // Store both the server endpoint (src) and the clean URL path
            // so popstate can reload content AND restore the active nav
            history.pushState({ src: src, urlPath: urlPath }, '', base + '/' + urlPath);
        }
    }

    // ── Initial page setup ───────────────────────────────────────────────────
    // Determine what the server rendered and set up the history state.

    // Extract the current URL path relative to the app base
    // e.g. "/tuneopedia/tunes" → "tunes", "/tuneopedia/discussions/3" → "discussions/3"
    var currentPath = window.location.pathname.replace(base, '').replace(/^\//, '');

    // Check for deep links with IDs in the URL
    var threadMatch = currentPath.match(/^discussions\/(\d+)$/);
    var initialRoute;

    if (threadMatch) {
        // Deep link to a specific thread — resolve to the page route with query param
        initialRoute = 'page/discussion-thread?thread_id=' + threadMatch[1];
    } else {
        // Normal route — look up in the routes table, or construct a page/* URL
        initialRoute = routes[initialSrc] || ('page/' + initialSrc);
    }

    // Replace the current history entry with one that has our state data.
    // This ensures that if the user navigates away and comes back, popstate
    // has the correct src and urlPath to reload.
    history.replaceState({ src: initialRoute, urlPath: initialSrc }, '', window.location.pathname);

    // Highlight the active nav link for whatever page was rendered
    setActiveNav(currentPath || initialSrc);

    // If the URL path doesn't match what the server rendered (e.g. user
    // navigated to /collections but server rendered home), load the correct
    // content via AJAX to fix the mismatch.
    if (routes[currentPath] && currentPath !== initialSrc && currentPath !== '') {
        setActiveNav(currentPath);
        $('#content').load(base + '/' + routes[currentPath]);
    }

    // ── Browser back/forward handling ────────────────────────────────────────
    // When the user clicks back/forward, the browser fires popstate with the
    // state object we stored in pushState/replaceState. We use it to reload
    // the correct content and restore the active nav highlight.
    $(window).on('popstate', function (e) {
        var state = e.originalEvent.state;
        if (state && state.src) {
            if (state.urlPath) setActiveNav(state.urlPath);
            var popUrl = state.src.indexOf('://') !== -1 ? state.src : base + '/' + state.src;
            $('#content').fadeTo(300, 0, function () {
                $('#content').load(popUrl, function () {
                    $('#content').fadeTo(300, 1);
                });
            });
        }
    });

    // ── Nav link click handlers ──────────────────────────────────────────────
    // Each nav link calls loadContent() with:
    //   - The server endpoint to fetch (e.g. "page/tunes")
    //   - The clean URL path for the address bar (e.g. "tunes")
    //   - push=true to add a history entry

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

    // Logout is special — it hits the API, shows an alert, then does a full
    // page reload (not an AJAX content swap) to clear the session state
    $('#logout_link').on('click', function () {
        $.post(
            'api/auth/logout',
            {},
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
    // Swaps between dark mode (ui-darkness theme) and light mode (ui-smoothness).
    // The preference is saved in localStorage so it persists across sessions.
    // The header has an inline script that applies the saved theme immediately
    // on page load (before this script runs) to prevent a flash of wrong theme.
    // This script then sets the correct icon and handles click events.

    var darkThemeUrl = base + '/css/themes/ui-darkness/jquery-ui-1.10.3.custom.css';
    var lightThemeUrl = base + '/css/themes/ui-smoothness/jquery-ui.min.css';

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

    // Set the correct icon on page load (body class was already set by header script)
    if (localStorage.getItem('tuneopedia-theme') === 'light') {
        $('#theme-icon').attr('class', 'fa-solid fa-moon');
    }

    $('#theme-toggle').on('click', function () {
        var current = $('body').hasClass('light-mode') ? 'light' : 'dark';
        applyTheme(current === 'light' ? 'dark' : 'light');
    });

});
