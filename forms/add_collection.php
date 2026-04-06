<!--CSS-->
<link href="css/style.css" rel="stylesheet" type="text/css"/>

<div id="add-collection-wrapper">

    <h2>Add Collection from ABC</h2>

    <form method="POST" action="add_collection.php">

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
            <label for="description">Description:</label>
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
            <label for="abc_text">Paste ABC Notation:</label>
            <textarea id="abc_text" name="abc_text" rows="20" required
                      placeholder="X:1&#10;T:Tune Name&#10;M:6/8&#10;K:Dmaj&#10;..."></textarea>
        </div>

        <button type="submit" class="submit-btn">Import Collection</button>

    </form>

</div>