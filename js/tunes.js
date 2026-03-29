$(document).ready(function(){

   //$('.show_abc').click(function() {
    $(document).on('click', '.show_abc', function() { 
        var setting_id = $(this).attr('id');
        var $thisSpan = $(this); // ← must be here, BEFORE $.post
        //git rid of any old close icons
        $('.show-abc-close').remove();


        $.post("get_tune_body.php", { "setting_id": setting_id }, function(data) {
            if (data) {
                var tune = jQuery.parseJSON(data);

                //ABCJS.renderAbc("paper", tuneAbc);
                ABCJS.renderAbc("paper", 
                    "X:" + tune.setting_id + "\n" +
                    "T:" + tune.name + "\n" +
                    "M:" + tune.time_signature + "\n" +
                    "L: 1/8 \n" +
                    "K:" + tune.key_signature + "\n" +
                    tune.abc_transcription
                );
                // Insert close button only after the specific clicked span
                $thisSpan.after("<span class='ui-icon ui-icon-circle-close show-abc-close' style='display: inline-block;'></span>");

                // Scope the click handler to only the close button we just added
                $thisSpan.next(".show-abc-close").on("click", function() {
                    $("#paper").empty();
                    $(this).remove();
                });
            }
        });
    });
    
    //$(".tune-favorite-icon").on("click", function () {
    $(document).on('click', '.tune-favorite-icon', function() {
        var result = confirm("Are you sure you want to add this tune to your favorites?");    
        var userId = $('#user-info').data('user-id');
        if(result){
            $.post(
                "favorite_tune.php",
                {
                    "tune_id":$(this).parent().parent().attr("id"),
                    "user_id":userId
                },
                
                function(data){
                    //display a custom popup to the screen
                    alert(data);
                    
                }
            );         
        }

    });

    var rowsPerPage = 10;
    
    function paginateTable(tableId, page) {
        var $rows = $('#' + tableId + ' tbody tr');
        var total = $rows.length;
        var totalPages = Math.ceil(total / rowsPerPage);
        var start = (page - 1) * rowsPerPage;
        var end = start + rowsPerPage;
    
        // Reset all rows first, then hide/show the correct slice
       $rows.hide().slice(start, end).show();
    
        var $controls = $('#pagination-' + tableId);
        $controls.empty();
    
        for (var i = 1; i <= totalPages; i++) {
            $controls.append(
                $('<a>')
                    .text(i)
                    .addClass('page-link' + (i === page ? ' active' : ''))
                    .attr('data-page', i)
                    .attr('data-table', tableId)
            );
        }
    }

    function paginateAll(page) {
        $('table').each(function() {
            paginateTable($(this).attr('id'), page);
        });
    }

    function filterAndPaginate(tableId, filter, page) {
        var $allRows = $('#' + tableId + ' tbody tr');
    
        $allRows.each(function() {
            var title = $(this).find('.tune_title').text().toLowerCase();
            $(this).toggle(filter === '' || title.indexOf(filter) !== -1);
        });
    
        var $visibleRows = $('#' + tableId + ' tbody tr:visible');
        var totalPages = Math.ceil($visibleRows.length / rowsPerPage);
        var start = (page - 1) * rowsPerPage;
        $visibleRows.hide().slice(start, start + rowsPerPage).show();
    
        var $controls = $('#pagination-' + tableId);
        $controls.empty();
        if (totalPages > 1) {
            for (var i = 1; i <= totalPages; i++) {
                $controls.append(
                    $('<a>')
                        .text(i)
                        .addClass('page-link' + (i === page ? ' active' : ''))
                        .attr('data-page', i)
                        .attr('data-table', tableId)
                );
            }
        }
    }

    // Initialize
    paginateAll(1);

    $(document).on('change', '#per-page-select', function() {
        rowsPerPage = parseInt($(this).val());
        var filter = $('#tune-filter').val().toLowerCase();
        var tableId = $("#tabs .ui-tabs-panel:visible table").attr('id');
    
        if (filter === '') {
            paginateAll(1);
        } else {
            filterAndPaginate(tableId, filter, 1);
        }
    });

    $(document).on('click', '.page-link', function() {
        var page = parseInt($(this).data('page'));
        var tableId = $(this).data('table');
        var filter = $('#tune-filter').val().toLowerCase();
    
        if (filter === '') {
            paginateTable(tableId, page);
        } else {
            filterAndPaginate(tableId, filter, page);
        }
    });

    $('#tune-filter').on('input', function() {
        var filter = $(this).val().toLowerCase();
        var tableId = $("#tabs .ui-tabs-panel:visible table").attr('id');
    
        if (filter === '') {
            paginateTable(tableId, 1);
        } else {
            filterAndPaginate(tableId, filter, 1);
        }
    });

    $("#tabs").on("tabsactivate", function(event, ui) {
        var tableId = ui.newPanel.find('table').attr('id');
        var filter = $('#tune-filter').val().toLowerCase();
    
        if (filter === '') {
            paginateTable(tableId, 1);
        } else {
            filterAndPaginate(tableId, filter, 1);
        }
    });
    
});
