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
            $collections[$cid]['tunes'][] = [
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

        <div id="collections-accordion">

            <?php foreach ($collections as $col): ?>

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
                <table class="collection-tunes-table">
                    <thead class="ui-state-default">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Key</th>
                            <th>Add Favorite</th>
                        </tr>
                    </thead>
                    <tbody class="ui-state-default">
                        <?php foreach ($col['tunes'] as $t): ?>
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
        $("#collections-accordion").accordion({
            collapsible: true,
            active: false,
            heightStyle: "content"
        });
    });
</script>