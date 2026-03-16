<?php
    
    //Start a session if one has not been started yet
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }

    include_once('connect.php');
    $tune_types = simpleQuery('SELECT * FROM tune_types');
    $tune_type_ids = array();
    $tune_type_names = array();
    $query_array = array();
    $results_array = array();

    //extract the id and the name of each tune type
    $count = 0;
    foreach($tune_types as $value){
        $tune_type_ids[$count] = $value["tune_type_id"];
        $tune_type_names[$count] = $value["tune_type"];
        $count++;
    }    

    //construct an array to get all the tunes for each type, based on ID
    $count = 0;
    foreach($tune_type_ids as $value){
        $type_id = $tune_type_ids[$count];//int
        $query_array[$count] = "SELECT * FROM tunes WHERE tune_type_id = $type_id";
        $count++;
    }

    //run each query saving the results into an array
    $count = 0;
    foreach($query_array as $value){
        $results_array[$count] = simpleQuery($query_array[$count]);
        $count++;
    }
?>

<script>
    $(function() {
         $( "#tabs" ).tabs();
     }); 
</script>
<link href="css/tunes.css" rel="stylesheet" type="text/css"/>
<!--START A TABLE-->

<div id="tabs">
    <ul>
        <?php foreach ($tune_type_names as $index => $name): ?>
            <li><a href="#tabs-<?= $index ?>"><?= htmlspecialchars($name) ?>s</a></li>
        <?php endforeach; ?>
    </ul>

    <?php foreach ($results_array as $tab_index => $tunes): ?>
        <?php $lowerCaseTuneType = mb_strtolower($tune_type_names[$tab_index]); ?>
        
        <div id="tabs-<?= $tab_index ?>">
            <table id="<?= $lowerCaseTuneType ?>">
                <thead class="ui-state-default">
                    <tr>
                        <th>Title</th>
                        <th>Transcriber</th>
                        <th>Composer</th>
                        <th>Key</th>
                        <?php if (isset($_SESSION['Authenticated'])): ?>
                            <th style="display:none;"></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                
                <tbody class="ui-state-default">
                    <?php if (is_array($tunes)): ?>
                        <?php foreach ($tunes as $row_index => $t): ?>
                            <?php 
                                $t_id = $t['tune_id'];
                                $author_id = $t['author_id'];
                            ?>
                            
                            <tr class="tune_data_row" style="--i: <?php echo $t_id *9999; ?>;">
                                <!-- Title -->
                                <td>
                                    <span class="tune_title" id="<?php echo $t_id; ?>">
                                        <img class="music_note_icon" src="images/notes.gif" alt="music" style="padding-top: 5px"/>
                                        <?= htmlspecialchars($t['tune_title']) ?>
                                    </span>
                                </td>

                                <!-- Author/Transcriber -->
                                <td>
                                    <?php 
                                        $author = simpleQuery("SELECT user_name FROM users WHERE user_id = $author_id");
                                        echo htmlspecialchars($author[0]['user_name']); 
                                    ?>
                                </td>

                                <!-- Composer -->
                                <td>
                                    <?php if (isset($t['composer_id'])): ?>
                                        <?php 
                                            $composer_id = $t['composer_id'];
                                            $composer = simpleQuery("SELECT composer_name FROM composers WHERE composer_id = $composer_id");
                                            echo htmlspecialchars($composer[0]['composer_name']); 
                                        ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Key -->
                                <td><?= htmlspecialchars($t['key']) ?></td>

                                <!-- Delete Icon -->
                                <?php if (isset($_SESSION['Authenticated'])): ?>
                                    <td>
                                        <?php if ($_SESSION['author_id'] == $author_id): ?>
                                            <span class="ui-icon ui-icon-trash" id=<?php echo $t_id;?> style="display: inline-block;"></span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</div>      

    
            