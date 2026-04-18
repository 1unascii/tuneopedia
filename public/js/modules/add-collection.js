var selectedFiles = [];

function renderFileList() {
    var $list = $('#file-list');
    $list.empty();
    selectedFiles.forEach(function(file, i) {
        var $item = $('<li class="file-list-item">'
            + '<span class="file-list-name"></span>'
            + '<span class="ui-icon ui-icon-circle-close file-clear-icon"></span>'
            + '</li>');
        $item.find('.file-list-name').text(file.name);
        $item.find('.file-clear-icon').on('click', function() {
            selectedFiles.splice(i, 1);
            renderFileList();
        });
        $list.append($item);
    });
}

$(document).on('change', '#abc_file', function() {
    var existingNames = selectedFiles.map(function(f) { return f.name; });
    Array.from(this.files).forEach(function(file) {
        if (existingNames.indexOf(file.name) === -1) {
            selectedFiles.push(file);
        }
    });
    this.value = ''; // reset so the same file can be re-added after removal
    renderFileList();
});

$(document).on('submit', '#add-collection-form', function(e) {
    e.preventDefault();
    var $btn = $(this).find('.edit-save-btn');
    if ($btn.prop('disabled')) return;
    $btn.prop('disabled', true).text('Importing...');

    var formData = new FormData(this);
    selectedFiles.forEach(function(file) {
        formData.append('abc_files[]', file);
    });
    $.ajax({
        url: 'api/add-collection',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(data) {
            selectedFiles = [];
            $('#content').html(data);
        },
        error: function() {
            $btn.prop('disabled', false).text('Import Collection');
        }
    });
});
