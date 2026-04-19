<?php
define('BASE_PATH', realpath(__DIR__ . '/..'));

require_once(BASE_PATH . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

require_once(BASE_PATH . '/controllers/TuneController.php');
require_once(BASE_PATH . '/controllers/CollectionController.php');
require_once(BASE_PATH . '/controllers/SettingController.php');
require_once(BASE_PATH . '/controllers/AuthController.php');
require_once(BASE_PATH . '/controllers/DiscussionController.php');

$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base = preg_replace('#/public$#', '', $scriptDir);

$route = trim(
    substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($base)),
    '/'
);

// ── API endpoints (AJAX) ─────────────────────────────────────────────────────
$apiRoutes = [
    'auth'           => ['AuthController',       'login'],
    'add-tune'       => ['TuneController',       'create'],
    'remove-tune'    => ['TuneController',       'delete'],
    'get-tune-body'  => ['TuneController',       'getBody'],
    'favorite-tune'   => ['TuneController',       'toggleFavorite'],
    'remove-favorite' => ['TuneController',       'removeFavorite'],
    'edit-setting'   => ['SettingController',     'edit'],
    'vote-setting'   => ['SettingController',     'vote'],
    'register'       => ['AuthController',        'register'],
    'test-cleanup'   => ['AuthController',        'testCleanup'],
    'add-collection'            => ['CollectionController',  'create'],
    'create-collection-from-favorites' => ['CollectionController',  'createFromFavorites'],
    'add-to-existing-collection'       => ['CollectionController',  'addToExisting'],
    'create-thread'  => ['DiscussionController',  'createThread'],
    'create-post'    => ['DiscussionController',  'createPost'],
    'delete-thread'  => ['DiscussionController',  'deleteThread'],
    'delete-post'    => ['DiscussionController',  'deletePost'],
];

if ($route === 'api/auth' && isset($_GET['logout'])) {
    (new AuthController)->logout();
    exit;
}

if (preg_match('#^api/(.+)$#', $route, $m) && isset($apiRoutes[$m[1]])) {
    [$class, $method] = $apiRoutes[$m[1]];
    (new $class)->$method();
    exit;
}

// ── Fragment endpoints (form views loaded via .load()) ───────────────────────
$fragmentRoutes = [
    'login'          => '/views/auth/login.php',
    'registration'   => '/views/auth/register.php',
    'abc-editor'     => '/views/tunes/create.php',
    'add-collection' => '/views/collections/create.php',
];

if (preg_match('#^fragment/(.+)$#', $route, $m)) {
    $fragKey = $m[1];

    if (preg_match('#^mode-options/(major|minor|dorian|mixolydian)$#', $fragKey, $mm)) {
        include BASE_PATH . '/views/mode_options/' . $mm[1] . '.php';
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

// ── Page content (loaded via .load() or server-rendered) ─────────────────────
$pageRoutes = [
    'tunes'       => ['TuneController',       'index'],
    'collections' => ['CollectionController',  'index'],
    'home'               => ['TuneController',       'home'],
    'tune-page'          => ['TuneController',       'show'],
    'my-tunes'           => ['TuneController',       'favorites'],
    'discussion'         => ['DiscussionController',  'index'],
    'discussion-thread'  => ['DiscussionController',  'show'],
];

if (preg_match('#^page/(.+)$#', $route, $m) && isset($pageRoutes[$m[1]])) {
    [$class, $method] = $pageRoutes[$m[1]];
    (new $class)->$method();
    exit;
}

// ── Full page rendering ──────────────────────────────────────────────────────
$serverRoutes = [
    ''            => ['TuneController',       'home'],
    'home'        => ['TuneController',       'home'],
    'tunes'       => ['TuneController',       'index'],
    'collections' => ['CollectionController',  'index'],
    'discussion'  => ['DiscussionController',  'index'],
];

if (preg_match('#^tune/(\d+)$#', $route, $m)) {
    $_GET['tune_id'] = (int)$m[1];
    $contentAction = ['TuneController', 'show'];
} elseif (preg_match('#^discussion/(\d+)$#', $route, $m)) {
    $_GET['thread_id'] = (int)$m[1];
    $contentAction = ['DiscussionController', 'show'];
} else {
    $contentAction = $serverRoutes[$route] ?? ['TuneController', 'index'];
}

include_once(BASE_PATH . '/views/partials/header.php');
include_once(BASE_PATH . '/config/database.php');
include_once(BASE_PATH . '/helpers/tune_helpers.php');
?>

<script>
var APP_BASE    = <?= json_encode($base) ?>;
var INITIAL_SRC = <?= json_encode($route ?: 'tunes') ?>;
</script>

<?php include_once(BASE_PATH . '/views/partials/nav.php'); ?>

<div id='content' class='main-content'>
<?php
    [$class, $method] = $contentAction;
    (new $class)->$method();
?>
</div>

<?php include_once(BASE_PATH . '/views/partials/footer.php'); ?>
