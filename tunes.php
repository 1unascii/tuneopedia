<script src='node_modules/abcjs/dist/abcjs-basic.js' type='text/javascript'></script>
<div onload="load()">
<!--script type = 'text/javascript'>
    var abc = "X:1\n" +
			"T: Cooley's\n" +
			"M: 4/4\n" +
			"L: 1/8\n" +
			"R: reel\n" +
			"K: Emin\n" +
			"|:D2|EB{c}BA B2 EB|~B2 AB dBAG|FDAD BDAD|FDAD dAFD|\n" +
			"EBBA B2 EB|B2 AB defg|afe^c dBAF|DEFD E2:|\n" +
			"|:gf|eB B2 efge|eB B2 gedB|A2 FA DAFA|A2 FA defg|\n" +
			"eB B2 eBgB|eB B2 defg|afe^c dBAF|DEFD E2:|\n\n" +
			"X:2\n" +
			"T: Greensleeves\n" +
			"M: 6/8\n" +
			"L: 1/8\n" +
			"K: Ador\n" +
			"A|c2d e>fe|d2B G>AB|c2A A>GA|B2GE2|\n" +
			"A|c2d e>fe|d2B G>AB|c>BA ^G>FG|A3A2:|\n" +
			"z|g2g gfe|d2B G>AB|c2A A>GA|B6|\n" +
			"g2g gfe|d2B G>AB|c>BA ^G>FG|A3A2:|\n" +
			"A/B/|c2c cde|dBG GAB|cBA ABc|BGE E2 A/B/|\n" +
			"c2c cde|dBG GAB|cBA GE^G|A3A2:|\n" +
			"e/f/|gag gfe|dBG GBd|aba aba|gee e2e/f/|\n" +
			"gag gfe|dBG GAB|cBA GE^G|A3A2:|\n"

    function load() {
        var tuneBook = new ABCJS.TuneBook(abc)
        var select = document.getElementById("tune-selector")
        var option = document.createElement("option");
        var optionContent = document.createTextNode("-- select tune --");
        option.appendChild(optionContent);
        select.appendChild(option)
        for (var i = 0; i < tuneBook.tunes.length; i++) {
            option = document.createElement("option");
            optionContent = document.createTextNode(tuneBook.tunes[i].title);
            option.appendChild(optionContent);
            option.setAttribute('value', i)
            select.appendChild(option)
        }
        select.addEventListener("change", setTune)
    }

    function setTune() {
        var index = this.value
        ABCJS.renderAbc("paper", abc, { startingTune: index});
    }

</script-->

<?php
    
    //Start a session if one has not been started yet
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }

    include_once('connect.php');

    $sql = file_get_contents('sql/show_tune_types.sql');
    $tune_types = simpleQuery($sql);

    $tune_type_ids = array();
    $tune_type_names = array();
    $query_array = array();
    $results_array = array();

    //extract the id and the name of each tune type
    $count = 0;
    foreach($tune_types as $value){
        $tune_type_ids[$count] = $value["tune_type_id"];
        $tune_type_names[$count] = $value["name"];
        $count++;
    }    

    //construct an array to get all the tunes for each type, based on ID
    $count = 0;

    $sql = file_get_contents('sql/show_tunebook.sql');
    $tunes = simpleQuery($sql);

    
    $groupedTunes = [];
    $tune_type_names = [];

    // 1. Organize the data we already fetched into categories
    foreach ($tunes as $row) {
        $typeId = $row['tune_type_id'];
        
        if (!isset($groupedTunes[$typeId])) {
            $groupedTunes[$typeId] = [];
            // Store the name so we can use it for the Tab labels
            $tune_type_names[$typeId] = $row['tune_type_name'];
        }
        
        $groupedTunes[$typeId][] = $row;
    }
        
?>

<script>
    $(function() {
         $( "#tabs" ).tabs();
     }); 
</script>
<link href="css/tunes.css" rel="stylesheet" type="text/css"/>
<!--START A TABLE-->
<!--div class='main-content'-->
<div id="tabs">
    <ul>
        <?php foreach ($groupedTunes as $id => $items): ?>
            <li><a class="tabs" href="#tabs-<?= $id ?>"><?= htmlspecialchars($tune_type_names[$id]) ?>s</a></li>
        <?php endforeach; ?>
    </ul>

    <?php foreach ($groupedTunes as $id => $categoryItems): ?>
        <div id="tabs-<?= $id ?>">
            <table id="<?= mb_strtolower($tune_type_names[$id]) ?>">
                <thead class="ui-state-default">
                    <tr>
                        <th>Tune ID</th>
                        <th>Title</th>
                        <th>Uploader</th>
                        <th>Composer</th>
                        <th>Key</th>
                        <th>Add Favorite</th>

                    </tr>
                </thead>
                <tbody class="ui-state-default">
                    <?php foreach ($categoryItems as $t): ?>
                        <tr class="tune_data_row" id="<?= $t['tune_id'] ?>">
                            <td>
                                <?php echo $t['tune_id'];?>
                            </td>
                            <td>
                                
                                <span class="tune_title" id="<?php echo ($t['tune_id']) ?>">
                                    <?= htmlspecialchars($t['tune_name']) ?>
                                </span>
                                <span class="show_abc" id="<?php echo ($t['setting_id']) ?>">
                                    <img class="music_note_icon" src="images/notes.gif" alt="shows_abc_for_most_popular_setting" /> 
                                </span>
                            </td>

                            <!-- Transcriber/Uploader Name -->
                            <td>
                                <?= htmlspecialchars($t['user_name'] ?? 'System') ?>
                            </td>

                            <!-- Composer -->
                            <td>
                                <?= htmlspecialchars($t['composer'] ?? 'Traditional') ?>
                            </td>

                            <!-- Key -->
                            <td>
                                <?= htmlspecialchars($t['key_signature'] ?? 'N/A') ?>
                            </td>
                           
                            <td class="tune-favorite-col">
                                <span class="ui-icon ui-icon-star tune-favorite-icon" id="user-info" data-user-id="<?php echo $_SESSION['user_id']; ?>">

                                    
                                </span>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>
<div class="container">
	<p>Select the tune you want to display. </p>
	<!--select id="tune-selector"></select-->
	<div id="paper"></div>
</div>
                    </div>
<!--/div-->
    
            