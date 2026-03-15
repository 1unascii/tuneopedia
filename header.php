<?php
    session_start();  
?>
<html>
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
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<link href="css/overcast/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css"/>
<link href="css/style.css" rel="stylesheet" type="text/css"/>
<script src='node_modules/abcjs/dist/abcjs-basic.js' type='text/javascript'></script>

<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="js/tunes.js"></script>
    <!--script src='js/abcjs-plugin-min.js' type='text/javascript'></script-->
<!--SCRIPTS-->

<!--PAGE TITLE-->
<title>My Tunebook</title>
<!--PAGE TITLE-->

<!--HEADING-->
<div align = "center">
    <h2><a href='index.php'>My Tunebook</a></h2>
    <!--PUT LOGO HERE
        <img id="logo" align = "center" src=''/>
    LOGO-->
</div>
<!--HEADING-->

<!--PUT FAVICON HERE and close comments-->
    <link rel="shortcut icon" href="images/notes.gif" />
<!--FAVICON-->
</head>
<body>

