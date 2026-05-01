<!DOCTYPE html>

<html lang="en">
<?php
    session_start();
    // Compute the app base path for absolute asset URLs.
    // This ensures assets load correctly even on nested URLs like /tunes/5
    if (!isset($base)) {
        $base = preg_replace('#/public$#', '', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'));
    }
    $assetBase = $base . '/';
?>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
    <!--[if IE]>
       <link href="<?= $assetBase ?>css/themes/internet_explorer.css" rel="stylesheet" type="text/css"/>
    <![endif]-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.3/jquery-ui.min.js"></script>
<link id="jquery-ui-theme" href="<?= $assetBase ?>css/themes/ui-darkness/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css"/>
<link href="<?= $assetBase ?>css/main.css?v=43" rel="stylesheet" type="text/css"/>
<script src='<?= $assetBase ?>js/lib/abcjs-basic.js?v=4' type='text/javascript'></script>
<link href="<?= $assetBase ?>css/abcjs-audio.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?= $assetBase ?>js/lib/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?= $assetBase ?>js/lib/jquery.knob.min.js"></script>
<script type="text/javascript" src="<?= $assetBase ?>js/modules/users/auth_user.js?v=3"></script>
<script type="text/javascript" src="<?= $assetBase ?>js/modules/abc-editor.js?v=22"></script>
<script type="text/javascript" src="<?= $assetBase ?>js/modules/tunes/index.js?v=11"></script>
<script type="text/javascript" src="<?= $assetBase ?>js/modules/tunes/show.js?v=37"></script>
<script type="text/javascript" src="<?= $assetBase ?>js/modules/favorites/index.js?v=6"></script>
<script type="text/javascript" src="<?= $assetBase ?>js/modules/collections/index.js?v=4"></script>
<link href="<?= $assetBase ?>css/discussion.css?v=4" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?= $assetBase ?>js/modules/discussions/index.js?v=7"></script>
<script type="text/javascript" src="<?= $assetBase ?>js/modules/discussions/show.js?v=4"></script>
<script src="https://kit.fontawesome.com/5c647e36cb.js" crossorigin="anonymous"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fondamento:ital@0;1&family=Noto+Music&family=Rye&display=swap" rel="stylesheet">
<link rel="icon" href="<?= $assetBase ?>favicon.ico?v=8" />
<title class='site-title'>Tuneopedia</title>
</head>
<body>
<div id="page_loader">Please wait while we query the database</div>
<div align="center">
    <div id="pop_up">
        <img id="logo" class='banner-image' src='<?= $assetBase ?>images/music_scroll_4.png'/>
    </div>
</div>
<script>
// Apply saved theme immediately to prevent flash of wrong theme
(function() {
    var saved = localStorage.getItem('tuneopedia-theme');
    if (saved === 'light') {
        document.body.classList.add('light-mode');
        document.getElementById('jquery-ui-theme').href = '<?= $assetBase ?>css/themes/ui-smoothness/jquery-ui.min.css';
    }
})();
</script>

