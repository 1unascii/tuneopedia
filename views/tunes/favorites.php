<link href="css/tunes.css?v=4" rel="stylesheet" type="text/css"/>
<link href="css/tune-page.css?v=8" rel="stylesheet" type="text/css"/>

<script>
    $(function() {
        $("#my-tunes-tabs").tabs();
        if (typeof window.initializeTunesPage === 'function') {
            window.initializeTunesPage();
        }
    });
</script>


<?php if (empty($groupedTunes)): ?>
    <p class="discussion-empty">You haven't added any tunes to your favorites yet.</p>
<?php else: ?>

<div id="pagination-and-search">
    <span class="filter-bar">
        <label for="tune-filter">Search: </label>
        <input type="text" id="tune-filter" placeholder="Filter tunes by title..." />
    </span>

    <span class="filter-bar">
        <label for="key-filter">Key: </label>
        <select id="key-filter">
            <option value="">All Keys</option>
        </select>
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

<div id="my-tunes-tabs">
    <ul>
        <?php foreach ($groupedTunes as $typeId => $items): ?>
            <li><a class="tabs" href="#my-tunes-tabs-<?= $typeId ?>"><?= htmlspecialchars($tune_type_names[$typeId]) ?>s</a></li>
        <?php endforeach; ?>
    </ul>

<?php foreach ($groupedTunes as $typeId => $categoryItems): ?>

<div id="my-tunes-tabs-<?= $typeId ?>">
<div class="pagination-controls" id="pagination-<?= str_replace(' ', '-', mb_strtolower($tune_type_names[$typeId])) ?>"></div>
<table id="<?= str_replace(' ', '-', mb_strtolower($tune_type_names[$typeId])); ?>">

    <thead class="ui-state-default">
        <tr>
            <th>Title</th>
            <th>Key</th>
            <th>Settings</th>
            <th>Add to Collection</th>
            <th>Remove Favorite</th>
        </tr>
    </thead>
    <tbody class="ui-state-default">

        <?php foreach ($categoryItems as $tune): ?>
        <tr class="tune_data_row<?= empty($tune['setting_id']) ? ' no-setting' : '' ?>" id="<?= $tune['tune_id'] ?>"
            data-key="<?= htmlspecialchars($tune['key_signature'] ?? '') ?>"
            data-tune-name="<?= htmlspecialchars($tune['tune_name']) ?>"
            data-key-signature="<?= htmlspecialchars($tune['key_signature'] ?? '') ?>"
            data-time-signature="<?= htmlspecialchars($tune['time_signature'] ?? '4/4') ?>"
            data-abc="<?= htmlspecialchars($tune['abc_transcription'] ?? '') ?>"
            data-setting-id="<?= $tune['setting_id'] ?>">

            <td>
                <span class="<?= empty($tune['setting_id']) ? 'dead_link' : 'show_abc' ?>" id="<?= $tune['setting_id'] ?>">
                    <i class="fa-solid fa-magnifying-glass-music music_note_icon<?= empty($tune['setting_id']) ? ' no-setting-icon' : '' ?>"></i>
                </span>
                <span class="tune_title" id="<?= $tune['tune_id'] ?>">
                    <?= htmlspecialchars($tune['tune_name']) ?>
                </span>
            </td>

            <td>
                <?= htmlspecialchars($tune['key_signature'] ?? 'N/A') ?>
            </td>

            <td class="tune-settings-count-col">
                <?= (int)($tune['settings_count'] ?? 0) ?>
            </td>

            <td class="collection-select-col">
                <i class="fa-regular fa-square collection-select-icon" title="Add to collection"></i>
            </td>

            <td class="tune-favorite-col">
                <i class="fa-solid fa-circle-xmark remove-favorite-icon"></i>
            </td>

        </tr>
        <?php endforeach; ?>

    </tbody>
</table>
</div>
<?php endforeach; ?>
</div>

<div id="collection-selection-bar" style="display: none;">
    <div><span id="collection-selection-count">0</span> Tune(s) Selected</div>
    <div>Move to Collection: <i class="fa-sharp fa-solid fa-floppy-disk" id="save-collection-btn" title="Move to Collection"></i>
    Clear: <i class="fa-sharp fa-solid fa-trash" id="clear-collection-btn" title="Clear"></i></div>
</div>

<div id="save-collection-form" style="display: none;">
    <div id="form_wrapper">
        <h2 style="display: inline;">Create Collection</h2>
        <label style="margin-left: 15px;">
            <input type="checkbox" id="remove-from-favorites" name="remove_from_favorites" value="1" checked />
            Remove from favorites
        </label>
        <form id="favorites-collection-form" class="edit-setting-form">

            <div class="edit-field edit-field-wide">
                <label for="collection-mode">Add to</label>
                <select id="collection-mode">
                    <option value="new">New Collection</option>
<?php foreach ($userCollections as $collection): ?>
                    <option value="<?= $collection['collection_id'] ?>"><?= htmlspecialchars($collection['name']) ?></option>
<?php endforeach; ?>
                </select>
            </div>

            <div id="new-collection-fields">
                <div class="edit-field">
                    <label for="fav-collection-name">Collection Name</label>
                    <input type="text" id="fav-collection-name" name="collection_name"
                           placeholder="e.g. My Session Set" />
                </div>

                <div class="edit-field">
                    <label for="fav-author">Author</label>
                    <input type="text" id="fav-author" name="author"
                           placeholder="e.g. Alan Lomax" />
                </div>

                <div class="edit-field edit-field-wide">
                    <label for="fav-description">Description / Notes</label>
                    <textarea id="fav-description" name="description" rows="3"
                              placeholder="Enter a description for this collection..."></textarea>
                </div>

                <div class="edit-field edit-field-wide">
                    <label>
                        <input type="checkbox" name="is_shared" value="1" />
                        Make this collection public
                    </label>
                </div>
            </div>

            <div class="edit-field edit-field-wide">
                <label for="fav-abc-text">ABC Notation (pre-loaded from selected tunes)</label>
                <textarea id="fav-abc-text" rows="20" readonly></textarea>
            </div>

            <div class="edit-form-actions">
                <button type="submit" class="edit-save-btn" id="collection-submit-btn">Create Collection</button>
                <button type="button" id="cancel-collection-form-btn" class="edit-save-btn">Cancel</button>
            </div>

        </form>
    </div>
</div>

<div class="container">
    <div id="paper"></div>
</div>

<?php endif; ?>
