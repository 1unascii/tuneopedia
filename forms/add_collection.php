<!--CSS-->
<link href="css/style.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="js/add_collection.js"></script>

<div id="add-collection-wrapper">

    <h2>Add Collection from ABC</h2>

    <form method="POST" id="add-collection-form" enctype="multipart/form-data">

        <div class="form-group">
            <label for="collection_name">Collection Name:</label>
            <input type="text" id="collection_name" name="collection_name" required
                   placeholder="e.g. O'Neill's 1001" />
        </div>

        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" id="author" name="author"
                placeholder="e.g. Francis O'Neill" />
        </div>

        <div class="form-group">
            <label for="description">Description/Notes:</label>
            <textarea id="description" name="description" rows="4"
                      placeholder="Enter a description for this collection..."></textarea>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="normalize_abc" value="1" checked />
                Automatically normalize ABC line formatting
            </label>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="parse_annotations" value="1" checked/>
                Parse all-caps tune annotations (for annotated collections)
            </label>
        </div>

        <div class="form-group">
            <label for="abc_file">Upload ABC File:</label>
            <input type="file" id="abc_file" name="abc_file" accept=".abc,.txt" />
        </div>

        <div class="form-group">
            <label for="abc_text">Or Paste ABC Notation:</label>
            <textarea id="abc_text" name="abc_text" rows="20"
                      placeholder="X:1&#10;T:Tune Name&#10;M:6/8&#10;K:Dmaj&#10;..."></textarea>
        </div>

        <button type="submit" class="submit-btn">Import Collection</button>

    </form>

    <div id="collection-results"></div>

</div>