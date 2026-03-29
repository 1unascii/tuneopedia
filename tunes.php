<!--CSS-->
<link href="css/tunes.css" rel="stylesheet" type="text/css"/>

<!--FETCH THE TUNES AND ORGANIZE THEM BY TYPE-->
<?php

    include_once('connect.php');

    $tunes = simpleQuery(file_get_contents('sql/show_tunebook.sql'));

    $groupedTunes = [];
    $tune_type_names = [];

    foreach ($tunes as $row) {
        $typeId = $row['tune_type_id'];
        if (!isset($groupedTunes[$typeId])) {
            $groupedTunes[$typeId] = [];
            $tune_type_names[$typeId] = $row['tune_type_name'];
        }
        $groupedTunes[$typeId][] = $row;
    }
        
?>

<!--jQuery TABS CALL-->
<script>
    $(function() {
         $( "#tabs" ).tabs();
     }); 
</script>
<div id="pagination-and-search">
<!--SEARCH BAR-->
    <span class="filter-bar">
        <label for="tune-filter">Search: </label>
        <input type="text" id="tune-filter" placeholder="Filter tunes by title..." />
    </span >

    <!--PAGINATION-->
    <span class="pagination-top">
        <label for="per-page-select">Tunes per page: </label>
        
        <select id="per-page-select">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </span>
</div>

<!--CREATE TABS HTML/OUTER TABS DIV-->
<div id="tabs">
    <ul>
        <?php foreach ($groupedTunes as $id => $items): ?>
            <li><a class="tabs" href="#tabs-<?= $id ?>"><?= htmlspecialchars($tune_type_names[$id]) ?>s</a></li>
        <?php endforeach; ?>
    </ul>

<!--PHP FOR EACH-->
<?php foreach ($groupedTunes as $id => $categoryItems): ?>
    
<!--INNER TAB DIV FOR EACH TUNE. EACH DIV IS A TAB-->
<div id="tabs-<?= $id ?>">
<!-- Each tab gets its own pagination controls -->
<div class="pagination-controls" id="pagination-<?= str_replace(' ', '-', mb_strtolower($tune_type_names[$id])) ?>"></div>
<!--TUNES TABLE-->
<!--table id="<?= mb_strtolower($tune_type_names[$id]) ?>"-->
<table id="<?= str_replace(' ', '-', mb_strtolower($tune_type_names[$id])); ?>">

    <!--TABLE HEADERS-->
    <thead class="ui-state-default">
        <tr>
            <th>Title</th>
            <th>Key</th>
            <th>Add Favorite</th>
        </tr>
    </thead>
    <tbody class="ui-state-default">

        <!--PHP FOR EACH-->
        <?php foreach ($categoryItems as $t): ?>
        <tr class="tune_data_row" id="<?= $t['tune_id'] ?>">

            <!--TUNE TITLE AND SHOW ABC BUTTON-->
            <td>
                <span class="show_abc" id="<?php echo ($t['setting_id']) ?>">
                    <img class="music_note_icon" src="images/notes.gif" alt="shows_abc_for_most_popular_setting" /> 
                </span>
                <span class="tune_title" id="<?php echo ($t['tune_id']) ?>">
                    <?= htmlspecialchars($t['tune_name']) ?>
                </span>
            </td>

            <!--KEY SIGNATURE -->
            <td>
                <?= htmlspecialchars($t['key_signature'] ?? 'N/A') ?>
            </td>
            
            <!--ADD FAVORITE-->
            <td class="tune-favorite-col">
                <span class="ui-icon ui-icon-star tune-favorite-icon" id="user-info" data-user-id="<?php echo $_SESSION['user_id']; ?>">
                </span>
            </td>

        </tr>
        <?php endforeach; ?>
        <!--END FOR EACH-->

    </tbody>
</table>
<!--END TUNES TABLE-->
</div>
<!--END INNER TABS DIV-->
<?php endforeach; ?>
<!--END FOR EACH-->
</div>
<!--END OUTER TABS DIV-->

<!--DISPLAY SHEET MUSIC HERE-->
<div class="container">
	<p>Select the tune you want to display. </p> 
	<div id="paper"></div>
</div>
                   

    
            