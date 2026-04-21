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
                <?php $tuneCount = 0; foreach ($col['tunes'] as $type) { $tuneCount += count($type['items']); } ?>
                <span class="collection-title"><?= htmlspecialchars($col['name']) ?></span>
                <span class="collection-tune-count">(<?= $tuneCount ?> tune<?= $tuneCount !== 1 ? 's' : '' ?>)</span>
                <?php if ($col['author']): ?>
                    <span class="collection-meta">by <?= htmlspecialchars($col['author']) ?></span>
                <?php endif; ?>
            </h3>

            <?php include __DIR__ . '/show.php'; ?>

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
