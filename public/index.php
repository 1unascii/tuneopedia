<?php
// ══════════════════════════════════════════════════════════════════════════════
// Front Controller — All requests are routed through this file via .htaccess
//
// The app has 4 types of routes, checked in this order:
//   1. API routes    (api/*)         — AJAX endpoints, return JSON or plain text
//   2. Fragment routes (fragment/*)  — partial HTML loaded into #content via $.load()
//   3. Page routes   (page/*)       — content-only HTML loaded via $.load()
//   4. Server routes (everything else) — full page render (header + nav + content + footer)
//
// If a route doesn't match any of these, a 404 is returned.
// API 404s return JSON. Page/fragment 404s return HTML. Server 404s render
// the full page layout with the 404 view inside #content.
// ══════════════════════════════════════════════════════════════════════════════

define('BASE_PATH', realpath(__DIR__ . '/..'));

require_once(BASE_PATH . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

require_once(BASE_PATH . '/controllers/TuneController.php');
require_once(BASE_PATH . '/controllers/CollectionController.php');
require_once(BASE_PATH . '/controllers/SettingController.php');
require_once(BASE_PATH . '/controllers/AuthController.php');
require_once(BASE_PATH . '/controllers/DiscussionController.php');
require_once(BASE_PATH . '/controllers/TestController.php');

// ── Compute the base path and current route ─────────────────────────────────
// $base is the app's root URL path (e.g. "/tuneopedia")
// $route is the path after $base, trimmed of slashes (e.g. "api/auth", "tunes", "discussion/3")
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base = preg_replace('#/public$#', '', $scriptDir);

$route = trim(
    substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($base)),
    '/'
);

// ══════════════════════════════════════════════════════════════════════════════
// 1. API ROUTES — RESTful endpoints called from JavaScript
//    Return JSON or plain text. No HTML layout.
//    Format: [HTTP_METHOD, url_pattern, ControllerClass, method]
//    URL params captured by regex groups are passed as method arguments.
// ══════════════════════════════════════════════════════════════════════════════
$apiRoutes = [
    // Auth
    ['POST',   'auth/login',                'AuthController',       'login'],
    ['POST',   'auth/logout',               'AuthController',       'logout'],
    ['POST',   'users',                     'AuthController',       'register'],

    // Tunes
    ['POST',   'tunes',                     'TuneController',       'create'],
    ['DELETE', 'tunes/(\d+)',               'TuneController',       'delete'],
    ['POST',   'tunes/(\d+)/favorite',      'TuneController',       'toggleFavorite'],
    ['DELETE', 'tunes/(\d+)/favorite',      'TuneController',       'removeFavorite'],
    ['POST',   'tunes/(\d+)/settings',      'TuneController',       'addSetting'],

    // Settings
    ['GET',    'settings/(\d+)',            'TuneController',       'getBody'],
    ['PUT',    'settings/(\d+)',            'SettingController',     'update'],
    ['POST',   'settings/(\d+)/vote',       'SettingController',     'vote'],

    // Collections
    ['POST',   'collections',               'CollectionController',  'create'],
    ['POST',   'collections/from-favorites','CollectionController',  'createFromFavorites'],
    ['POST',   'collections/(\d+)/tunes',   'CollectionController',  'addToExisting'],

    // Threads / Posts
    ['POST',   'threads',                   'DiscussionController',  'createThread'],
    ['DELETE', 'threads/(\d+)',             'DiscussionController',  'deleteThread'],
    ['POST',   'threads/(\d+)/posts',       'DiscussionController',  'createPost'],
    ['DELETE', 'posts/(\d+)',               'DiscussionController',  'deletePost'],

    // Test
    ['POST',   'test/cleanup',              'TestController',        'cleanup'],
    ['POST',   'test/reset-ids',            'TestController',        'resetIds'],
];

if (preg_match('#^api/(.+)$#', $route, $apiMatch)) {
    $apiPath = $apiMatch[1];
    $httpMethod = $_SERVER['REQUEST_METHOD'];

    // Support PUT/DELETE from jQuery via _method override
    if ($httpMethod === 'POST' && !empty($_POST['_method'])) {
        $httpMethod = strtoupper($_POST['_method']);
    }

    $matched = false;
    foreach ($apiRoutes as [$routeMethod, $pattern, $class, $action]) {
        if ($httpMethod === $routeMethod && preg_match('#^' . $pattern . '$#', $apiPath, $params)) {
            array_shift($params);
            (new $class)->$action(...$params);
            $matched = true;
            break;
        }
    }

    if (!$matched) {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
    }
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
// 2. FRAGMENT ROUTES — Partial HTML snippets loaded via $.load()
//    Used for forms (login, register, add tune, add collection)
//    These return raw HTML with no page layout wrapper.
// ══════════════════════════════════════════════════════════════════════════════
$fragmentRoutes = [
    'login'          => '/views/auth/login.php',
    'registration'   => '/views/auth/register.php',
    'abc-editor'     => '/views/tunes/create.php',
    'add-collection' => '/views/collections/create.php',
];

if (preg_match('#^fragment/(.+)$#', $route, $m)) {
    $fragKey = $m[1];

    // Sub-route for ABC editor mode options (major, minor, dorian, mixolydian)
    if (preg_match('#^mode-options/(major|minor|dorian|mixolydian)$#', $fragKey, $mm)) {
        include BASE_PATH . '/views/mode_options/' . $mm[1] . '.php';
        exit;
    }

    // Dynamic fragment routes for settings
    if (preg_match('#^settings/(\d+)/edit$#', $fragKey, $fm)) {
        $_GET['setting_id'] = (int)$fm[1];
        (new SettingController)->edit();
        exit;
    }
    if (preg_match('#^tunes/(\d+)/add-setting$#', $fragKey, $fm)) {
        $_GET['tune_id'] = (int)$fm[1];
        (new SettingController)->addForm();
        exit;
    }

    if (isset($fragmentRoutes[$fragKey])) {
        include BASE_PATH . $fragmentRoutes[$fragKey];
        exit;
    }

    http_response_code(404);
    echo 'Fragment not found';
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
// 3. PAGE ROUTES — Content loaded into #content via $.load()
//    Called by nav.js when clicking nav links. Returns HTML without
//    the header/nav/footer — just the page content that goes inside #content.
// ══════════════════════════════════════════════════════════════════════════════
$pageRoutes = [
    'home'               => ['TuneController',       'home'],
    'tunes'              => ['TuneController',       'index'],
    'tune-page'          => ['TuneController',       'show'],
    'my-tunes'           => ['TuneController',       'favorites'],
    'collections'        => ['CollectionController',  'index'],
    'discussions'        => ['DiscussionController',  'index'],
    'discussion-thread'  => ['DiscussionController',  'show'],
];

// Match page/* routes — returns content HTML or 404 HTML
if (preg_match('#^page/(.+)$#', $route, $m)) {
    if (isset($pageRoutes[$m[1]])) {
        [$class, $method] = $pageRoutes[$m[1]];
        (new $class)->$method();
    } else {
        http_response_code(404);
        include BASE_PATH . '/views/errors/404.php';
    }
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
// 4. SERVER ROUTES — Full page rendering (direct URL access)
//    Used when a user navigates directly to a URL (e.g. typing it in the
//    address bar, refreshing the page, or following a bookmark).
//    Renders the complete page: header → nav → content → footer.
// ══════════════════════════════════════════════════════════════════════════════
$serverRoutes = [
    ''            => ['TuneController',       'home'],       // Landing page
    'home'        => ['TuneController',       'home'],
    'tunes'       => ['TuneController',       'index'],
    'collections' => ['CollectionController',  'index'],
    'discussions'  => ['DiscussionController',  'index'],
];

// Dynamic routes with parameters extracted from the URL path
if (preg_match('#^tunes/(\d+)$#', $route, $m)) {
    $_GET['tune_id'] = (int)$m[1];
    $contentAction = ['TuneController', 'show'];
} elseif (preg_match('#^tune/(\d+)$#', $route, $m)) {
    $_GET['tune_id'] = (int)$m[1];
    $contentAction = ['TuneController', 'show'];
} elseif (preg_match('#^discussions/(\d+)$#', $route, $m)) {
    $_GET['thread_id'] = (int)$m[1];
    $contentAction = ['DiscussionController', 'show'];
} else {
    // Look up the route in the server routes table
    // If not found, $contentAction is null → triggers the 404 page
    $contentAction = $serverRoutes[$route] ?? null;
}

// ── Render the full page layout ─────────────────────────────────────────────
include_once(BASE_PATH . '/views/partials/header.php');
include_once(BASE_PATH . '/config/database.php');
?>

<script>
// APP_BASE: the URL path prefix for this app (e.g. "/tuneopedia")
// INITIAL_SRC: tells nav.js what content was server-rendered so it doesn't re-load it
var APP_BASE    = <?= json_encode($base) ?>;
var INITIAL_SRC = <?= json_encode($contentAction ? (preg_replace('#/\d+$#', '', $route) ?: 'home') : 'home') ?>;
</script>

<?php include_once(BASE_PATH . '/views/partials/nav.php'); ?>

<div id='content' class='main-content'>
<?php
    if ($contentAction) {
        // Valid route — render the controller action's view
        [$class, $method] = $contentAction;
        (new $class)->$method();
    } else {
        // No matching route — show the 404 error page inside the layout
        http_response_code(404);
        include BASE_PATH . '/views/errors/404.php';
    }
?>
</div>

<?php include_once(BASE_PATH . '/views/partials/footer.php'); ?>
