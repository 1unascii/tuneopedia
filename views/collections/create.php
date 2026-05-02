<link href="css/tune-page.css?v=8" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="js/modules/collections/create.js?v=3"></script>

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
                <input type="checkbox" name="parse_annotations" value="1" checked />
                Parse all-caps tune annotations (for annotated collections)
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
