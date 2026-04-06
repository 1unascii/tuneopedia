/*$('#add-collection-form').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'add_collection.php',
        method: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(data) {
            var results = $(data).find('#add-collection-wrapper');
            $('#add-collection-form').hide();
            $('#collection-results').html(results.html());
        }
    });
});*/

$('#add-collection-form').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'add_collection.php',
        method: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(data) {
            $('#content').html(data);
        }
    });
});