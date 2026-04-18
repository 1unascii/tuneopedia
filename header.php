<!DOCTYPE html>

<html lang="en">
<?php
    session_start();  
?>              
<div id="page_loader">Please wait while we query the database</div>
<head>
<!--SUPPOSED TO HELP WITH IE compatability, doesn't seem to do anything on IE 9 at least-->
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
<!--INTERNET EXPLORER STYLES (just add between the green style tags)-->
    <!--[if IE]>
       <link href="css/internet_explorer.css" rel="stylesheet" type="text/css"/>
    <![endif]-->
<!--INTERNET EXPLORER STYLES-->

<!--UNIVERVERSAL STYLES-->    
<!--UNIVERSAL STYLES-->

<!--SCRIPTS-->
<!--script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script-->
<!--script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.3/jquery-ui.min.js"></script>
<link href="css/ui-darkness/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/style.css?v=3" rel="stylesheet" type="text/css"/>
<script src='node_modules/abcjs/dist/abcjs-basic.js' type='text/javascript'></script>
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-turtle.min.js"></script>
<script type="text/javascript" language="javascript" src="js/abc_editor.js?v=3"></script>
<script type="text/javascript" language="javascript" src="js/tunes.js?v=12"></script>
<script type="text/javascript" language="javascript" src="js/tune_page.js?v=5"></script><!--This forces cloudflared tunnel to reload this JS file after changes have been made-->
<!--script type="text/javascript" language="javascript" src="js/tunes.js"></script-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fondamento:ital@0;1&family=Rye&display=swap" rel="stylesheet">
    <!--script src='js/abcjs-plugin-min.js' type='text/javascript'></script-->
<!--SCRIPTS-->

<!--PAGE TITLE-->
<title class='site-title'>Tuneopedia</title>
<!--PAGE TITLE-->

<!--HEADING-->
<div align = "center">
    <div id="pop_up">
    <!--PUT LOGO HERE-->
        <img id="logo" class='banner-image'  src='images/music_scroll_4.png'/>
    </div>
    <!--LOGO-->
</div>
<!--HEADING-->

<!--PUT FAVICON HERE and close comments-->
    <link rel="shortcut icon" href="images/notes.gif" />
<!--FAVICON-->
</head>
<body>

