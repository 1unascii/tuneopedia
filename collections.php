<!--CSS-->
<link href="css/style.css" rel="stylesheet" type="text/css"/>

<!--FETCH COLLECTIONS AND ORGANIZE BY COLLECTION-->
<?php

    include_once('connect.php');

    $rows = simpleQuery(file_get_contents('sql/show_collections.sql'));

    $collections = [];

    foreach ($rows as $row) {
        $cid = $row['collection_id'];
        if (!isset($collections[$cid])) {
            $collections[$cid] = [
                'collection_id'  => $row['collection_id'],
                'name'           => $row['collection_name'],
                'author'         => $row['author'],
                'publisher'      => $row['publisher'],
                'published_date' => $row['published_date'],
                'description'    => $row['description'],
                'cover_image'    => $row['cover_image'],
                'created_at'     => $row['created_at'],
                'tunes'          => []
            ];
        }

        // Only add a tune row if a tune actually exists for this collection
        if ($row['tune_id']) {
            $typeId = $row['tune_type_id'] ?: 0;
            $typeName = $row['tune_type_name'] ?: 'Other';

            if (!isset($collections[$cid]['tunes'][$typeId])) {
                $collections[$cid]['tunes'][$typeId] = [
                    'type_id' => $typeId,
                    'type_name' => $typeName,
                    'items' => []
                ];
            }

            $collections[$cid]['tunes'][$typeId]['items'][] = [
                'tune_id'         => $row['tune_id'],
                'tune_name'       => $row['tune_name'],
                'setting_id'      => $row['setting_id'],
                'key_signature'   => $row['key_signature'],
                'time_signature'  => $row['time_signature'],
                'abc_transcription' => $row['abc_transcription'],
                'position'        => $row['position']
            ];
        }
    }

?>

<div id="collections-content">

    <?php if (empty($collections)): ?>
        <p>No collections found.</p>
    <?php else: ?>

        <div id="collections-pagination-and-search">
            <span class="filter-bar">
                <label for="collection-filter">Search collections: </label>
                <input type="text" id="collection-filter" placeholder="Filter collections by title..." />
            </span>

            <span class="pagination-top">
                <label for="collections-per-page-select">Collections per page: </label>
                <select id="collections-per-page-select">
                    <option value="5" selected>5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </span>
        </div>

        <div class="pagination-controls" id="collections-pagination-controls"></div>
        <p id="collections-empty-state" style="display: none;">No collections match your search.</p>

        <div id="collections-accordion">

            <?php foreach ($collections as $col): ?>
            <?php $collectionId = (int) $col['collection_id']; ?>

            <!--COLLECTION HEADER (ACCORDION TRIGGER)-->
            <h3 class="collection-header">
                <?php if ($col['cover_image']): ?>
                    <img class="collection-cover" src="<?= htmlspecialchars($col['cover_image']) ?>" alt="cover" />
                <?php endif; ?>
                <span class="collection-title"><?= htmlspecialchars($col['name']) ?></span>
                <?php if ($col['author']): ?>
                    <span class="collection-meta">by <?= htmlspecialchars($col['author']) ?></span>
                <?php endif; ?>
            </h3>

            <!--COLLECTION BODY (ACCORDION CONTENT)-->
            <div class="collection-body">

                <?php if ($col['description']): ?>
                    <p class="collection-description"><?= htmlspecialchars($col['description']) ?></p>
                <?php endif; ?>

                <?php if ($col['publisher'] || $col['published_date']): ?>
                    <p class="collection-meta-detail">
                        <?php if ($col['publisher']): ?>
                            Publisher: <?= htmlspecialchars($col['publisher']) ?>
                        <?php endif; ?>
                        <?php if ($col['published_date']): ?>
                            &nbsp;|&nbsp; Published: <?= htmlspecialchars($col['published_date']) ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($col['tunes'])): ?>
                <div class="collection-tunes-toolbar">
                    <label for="collection-tune-filter-<?= $collectionId ?>">Search tunes: </label>
                    <input
                        type="text"
                        class="collection-tune-filter"
                        id="collection-tune-filter-<?= $collectionId ?>"
                        data-collection-id="<?= $collectionId ?>"
                        placeholder="Filter tunes by title..."
                    />

                    <label for="collection-tunes-per-page-<?= $collectionId ?>">Tunes per page: </label>
                    <select
                        class="collection-tunes-per-page"
                        id="collection-tunes-per-page-<?= $collectionId ?>"
                        data-collection-id="<?= $collectionId ?>"
                    >
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <div class="collection-tabs" id="collection-tabs-<?= $collectionId ?>" data-collection-id="<?= $collectionId ?>">
                    <ul>
                        <?php foreach ($col['tunes'] as $type): ?>
                            <?php $panelId = 'collection-' . $collectionId . '-type-' . (int) $type['type_id']; ?>
                            <li>
                                <a class="tabs" href="#<?= $panelId ?>"><?= htmlspecialchars($type['type_name']) ?>s</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php foreach ($col['tunes'] as $type): ?>
                        <?php $panelId = 'collection-' . $collectionId . '-type-' . (int) $type['type_id']; ?>
                        <?php $tableId = $panelId . '-table'; ?>
                        <div id="<?= $panelId ?>" class="collection-tab-panel">
                            <div class="pagination-controls collection-tunes-pagination" id="pagination-<?= $tableId ?>"></div>
                            <table class="collection-tunes-table" id="<?= $tableId ?>" data-collection-id="<?= $collectionId ?>">
                                <thead class="ui-state-default">
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Key</th>
                                        <th>Add Favorite</th>
                                    </tr>
                                </thead>
                                <tbody class="ui-state-default">
                                    <?php foreach ($type['items'] as $t): ?>
                                    <tr class="tune_data_row" id="<?= $t['tune_id'] ?>">

                                        <!--POSITION-->
                                        <td><?= htmlspecialchars($t['position']) ?></td>

                                        <!--TUNE TITLE AND SHOW ABC BUTTON-->
                                        <td>
                                            <span class="show_abc" id="<?= $t['setting_id'] ?>">
                                                <img class="music_note_icon" src="images/notes.gif" alt="show abc notation" />
                                            </span>
                                            <span class="tune_title" id="<?= $t['tune_id'] ?>">
                                                <?= htmlspecialchars($t['tune_name']) ?>
                                            </span>
                                        </td>

                                        <!--KEY SIGNATURE-->
                                        <td><?= htmlspecialchars($t['key_signature'] ?? 'N/A') ?></td>

                                        <!--ADD FAVORITE-->
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
                <?php else: ?>
                    <p>No tunes in this collection yet.</p>
                <?php endif; ?>

            </div>
            <!--END COLLECTION BODY-->

            <?php endforeach; ?>

        </div>
        <!--END ACCORDION-->

    <?php endif; ?>

</div>
<!--END COLLECTIONS CONTENT-->

<!--DISPLAY SHEET MUSIC HERE-->
<div class="container">
    <p>Select the tune you want to display.</p>
    <div id="paper"></div>
</div>

<!--INIT ACCORDION-->
<script>
    $(function() {
        if (typeof window.initializeCollectionsPage === "function") {
            window.initializeCollectionsPage();
        }
    });
</script>
