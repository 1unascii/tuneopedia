<link href="css/main.css" rel="stylesheet" type="text/css"/>
<link href="css/tunes.css?v=3" rel="stylesheet" type="text/css"/>

<h2>Collections</h2>
<div id="collections-content" data-current-user-id="<?= $currentUserId ?>">

    <?php if (empty($collections)): ?>
        <p>No collections found.</p>
    <?php else: ?>

        <div id="collections-pagination-and-search">
            <span class="filter-bar">
                <label for="collection-filter">Search collections: </label>
                <input type="text" id="collection-filter" placeholder="Filter collections by title..." />
            </span>

            <span class="filter-bar">
                <label for="show-no-setting">
                    <input type="checkbox" id="show-no-setting" />
                    Show tunes with no settings
                </label>
            </span>

            <span class="filter-bar">
                <label for="collection-visibility">Visibility: </label>
                <select id="collection-visibility">
                    <option value="all">All</option>
                    <option value="public">Public</option>
                    <option value="private">Private</option>
                </select>
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

            <h3 class="collection-header" data-shared="<?= $col['is_shared'] ? '1' : '0' ?>" data-owner-id="<?= (int) $col['user_id'] ?>">
                <?php if ($col['cover_image']): ?>
                    <img class="collection-cover" src="<?= htmlspecialchars($col['cover_image']) ?>" alt="cover" />
                <?php endif; ?>
                <span class="collection-title"><?= htmlspecialchars($col['name']) ?></span>
                <?php if ($col['author']): ?>
                    <span class="collection-meta">by <?= htmlspecialchars($col['author']) ?></span>
                <?php endif; ?>
            </h3>

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
                                    <tr class="tune_data_row<?= empty($t['setting_id']) ? ' no-setting' : '' ?>" id="<?= $t['tune_id'] ?>">

                                        <td><?= htmlspecialchars($t['position']) ?></td>

                                        <td>
                                            <span class="<?= empty($t['setting_id']) ? 'dead_link' : 'show_abc' ?>" id="<?= $t['setting_id'] ?>">
                                                <i class="fa-solid fa-magnifying-glass-music music_note_icon<?= empty($t['setting_id']) ? ' no-setting-icon' : '' ?>"></i>
                                            </span>
                                            <span class="tune_title" id="<?= $t['tune_id'] ?>">
                                                <?= htmlspecialchars($t['tune_name']) ?>
                                            </span>
                                        </td>

                                        <td><?= htmlspecialchars($t['key_signature'] ?? 'N/A') ?></td>

                                        <td class="tune-favorite-col">
                                            <span class="favorite-toggle" data-user-id="<?= $_SESSION['user_id'] ?? 0 ?>">
                                                <i class="fa-sharp fa-solid fa-plus favorite-icon"></i>
                                            </span>
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

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<div class="container">
    <p>Select the tune you want to display.</p>
    <div id="paper"></div>
</div>

<script>
    $(function() {
        if (typeof window.initializeCollectionsPage === "function") {
            window.initializeCollectionsPage();
        }
    });
</script>
