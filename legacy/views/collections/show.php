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

        <label for="collection-key-filter-<?= $collectionId ?>">Key: </label>
        <select
            class="collection-key-filter"
            id="collection-key-filter-<?= $collectionId ?>"
            data-collection-id="<?= $collectionId ?>"
        >
            <option value="">All Keys</option>
        </select>

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
                        <tr class="tune_data_row<?= empty($t['setting_id']) ? ' no-setting' : '' ?>" id="<?= $t['tune_id'] ?>" data-key="<?= htmlspecialchars($t['key_signature'] ?? '') ?>">

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
    <?php else: ?>
        <p>No tunes in this collection yet.</p>
    <?php endif; ?>

</div>
