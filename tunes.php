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
                                <span class="ui-icon ui-icon-star tune-favorite-icon" id="user-info" data-user-id="<?php echo $_SESSION['user_id']; ?>"></span>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>
<!--/div-->
    
            