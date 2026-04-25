<link href="css/tunes.css?v=4" rel="stylesheet" type="text/css"/>

<script>
    $(function() {
        $( "#tabs" ).tabs();
        if (typeof window.initializeTunesPage === 'function') {
            window.initializeTunesPage();
        }
    });
</script>
<div id="pagination-and-search">
    <span class="filter-bar">
        <label for="tune-filter">Search: </label>
        <input type="text" id="tune-filter" placeholder="Filter tunes by title..." />
    </span >

    <span class="filter-bar">
        <label for="key-filter">Key: </label>
        <select id="key-filter">
            <option value="">All Keys</option>
        </select>
    </span>

    <span class="filter-bar">
        <label for="show-no-setting">
            <input type="checkbox" id="show-no-setting" />
            Show tunes with no settings
        </label>
    </span>

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

<div id="tabs">
    <ul>
        <?php foreach ($groupedTunes as $id => $items): ?>
            <li><a class="tabs" href="#tabs-<?= $id ?>"><?= htmlspecialchars($tune_type_names[$id]) ?>s</a></li>
        <?php endforeach; ?>
    </ul>

<?php foreach ($groupedTunes as $id => $categoryItems): ?>

<div id="tabs-<?= $id ?>">
<div class="pagination-controls" id="pagination-<?= str_replace(' ', '-', mb_strtolower($tune_type_names[$id])) ?>"></div>
<table id="<?= str_replace(' ', '-', mb_strtolower($tune_type_names[$id])); ?>">

    <thead class="ui-state-default">
        <tr>
            <th>Title</th>
            <th>Key</th>
            <th>Add Favorite</th>
        </tr>
    </thead>
    <tbody class="ui-state-default">

        <?php foreach ($categoryItems as $t): ?>
        <tr class="tune_data_row<?= empty($t['setting_id']) ? ' no-setting' : '' ?>" id="<?= $t['tune_id'] ?>" data-key="<?= htmlspecialchars($t['key_signature'] ?? '') ?>">

            <td>
                <span class="<?= empty($t['setting_id']) ? 'dead_link' : 'show_abc' ?>" id="<?php echo ($t['setting_id']) ?>">
                    <i class="fa-solid fa-magnifying-glass-music music_note_icon<?= empty($t['setting_id']) ? ' no-setting-icon' : '' ?>"></i>
                </span>
                <span class="tune_title" id="<?php echo ($t['tune_id']) ?>">
                    <?= htmlspecialchars($t['tune_name']) ?>
                </span>
            </td>

            <td>
                <?= htmlspecialchars($t['key_signature'] ?? 'N/A') ?>
            </td>

            <td class="tune-favorite-col">
                <span class="favorite-toggle" data-user-id="<?= $_SESSION['user_id'] ?? 0 ?>">
                    <i class="<?= !empty($t['is_favorited']) ? 'fa-solid fa-square-check favorited' : 'fa-regular fa-square' ?> favorite-icon"></i>
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
	<p id="select-tune-prompt">Select the tune you want to display. </p>
	<div id="paper"></div>
</div>
