<link href="css/tune-page.css?v=8" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="js/modules/collections/create.js?v=3"></script>

<?php if (!empty($success)): ?>
<!--RESULTS-->
<div id="add-collection-wrapper">
    <h2>Add Collection from ABC</h2>

    <?php
        $newTunes = array_filter($results, fn($r) => $r['status'] === 'inserted');
        $existingTunes = array_filter($results, fn($r) => $r['status'] === 'existing_tune');
        $additionalSettings = array_filter($results, fn($r) => $r['status'] === 'additional_setting');
        $skipped = array_filter($results, fn($r) => $r['status'] === 'skipped');

        $multiSettingTunes = [];
        foreach ($results as $r) {
            if ($r['status'] === 'additional_setting' && isset($r['tune_id'])) {
                $multiSettingTunes[$r['tune_id']][] = $r['tune'];
            }
        }
    ?>
    <div class="success-message">
        <p>Collection <strong><?= htmlspecialchars($collectionName) ?></strong> created.</p>
        <ul>
            <li><?= count($newTunes) ?> new tune(s) added</li>
            <?php if (!empty($additionalSettings)): ?>
            <li><?= count($multiSettingTunes) ?> tune(s) with multiple settings (<?= count($additionalSettings) ?> additional setting(s) total)</li>
            <?php endif; ?>
            <?php if (!empty($existingTunes)): ?>
            <li><?= count($existingTunes) ?> existing tune(s) — new setting added:
                <ul>
                    <?php foreach ($existingTunes as $et): ?>
                    <li><?= htmlspecialchars($et['tune']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
            <?php if (!empty($skipped)): ?>
            <li><?= count($skipped) ?> skipped</li>
            <?php endif; ?>
        </ul>
        <table class="collection-results-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tune</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $i => $r): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($r['tune']) ?></td>
                    <td>
                        <?php if ($r['status'] === 'inserted'): ?>
                            New tune added
                        <?php elseif ($r['status'] === 'existing_tune'): ?>
                            Existing tune — new setting added
                        <?php elseif ($r['status'] === 'additional_setting'): ?>
                            Additional setting added
                        <?php else: ?>
                            Skipped: <?= htmlspecialchars($r['reason'] ?? '') ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php elseif (!empty($error)): ?>
<div id="add-collection-wrapper">
    <h2>Add Collection from ABC</h2>
    <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <button onclick="history.back()">Go Back</button>
</div>

<?php else: ?>
<!-- FORM -->
<div id="form_wrapper">

    <h2>Add Collection from ABC</h2>

    <form method="POST" id="add-collection-form" class="edit-setting-form" enctype="multipart/form-data">

        <div class="edit-field">
            <label for="collection_name">Collection Name</label>
            <input type="text" id="collection_name" name="collection_name" required
                   placeholder="e.g. O'Neill's 1001" />
        </div>

        <div class="edit-field">
            <label for="author">Author</label>
            <input type="text" id="author" name="author"
                   placeholder="e.g. Francis O'Neill" />
        </div>

        <div class="edit-field edit-field-wide">
            <label for="description">Description / Notes</label>
            <textarea id="description" name="description" rows="3"
                      placeholder="Enter a description for this collection..."></textarea>
        </div>

        <div class="edit-field edit-field-wide">
            <label>
                <input type="checkbox" name="normalize_abc" value="1" checked />
                Automatically normalize ABC line formatting
            </label>
        </div>

        <div class="edit-field edit-field-wide">
            <label>
                <input type="checkbox" name="is_shared" value="1" checked />
                Make this collection public
            </label>
        </div>

        <div class="edit-field edit-field-wide">
            <label for="abc_file">Upload ABC File(s)</label>
            <input type="file" id="abc_file" accept=".abc,.txt" multiple />
        </div>

        <ul id="file-list" class="file-list edit-field-wide"></ul>

        <div class="edit-field edit-field-wide">
            <label for="abc_text">Or Paste ABC Notation</label>
            <textarea id="abc_text" name="abc_text" rows="20"
                      placeholder="X:1&#10;T:Tune Name&#10;M:6/8&#10;K:Dmaj&#10;..."></textarea>
        </div>

        <div class="edit-form-actions">
            <button type="submit" class="edit-save-btn">Import Collection</button>
        </div>

    </form>

</div>
<?php endif; ?>
