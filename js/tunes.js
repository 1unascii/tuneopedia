$(document).ready(function(){

   //$('.show_abc').click(function() {
    $(document).on('click', '.tune_title', function() {
        var tune_id = $(this).attr('id');
        var $panel = $(this).closest('.ui-tabs-panel');
        if (!$panel.length) return;

        var $table = $panel.find('table');
        var tableId = $table.attr('id');
        var isCollectionTable = $table.hasClass('collection-tunes-table');
        var savedPage = isCollectionTable
            ? (collectionTunePages[tableId] || 1)
            : (currentPages[tableId] || 1);
        var savedHtml = $panel.html();

        // Store state on the panel so tab-switch can also restore it
        $panel.data('tunePageState', {
            savedHtml: savedHtml,
            savedPage: savedPage,
            tableId: tableId,
            isCollectionTable: isCollectionTable
        });

        $panel.load('tune_page.php?tune_id=' + tune_id, function() {
            var $backBtn = $('<button class="tune-back-btn">&#8592; Back</button>');
            $panel.prepend($backBtn);

            // Render primary notation into #tune-notation
            var $primaryBlock = $panel.find('.setting-block:first');
            if ($primaryBlock.length) {
                var $primaryAbc = $primaryBlock.find('.setting-abc-data');
                if ($primaryAbc.length) {
                    try {
                        ABCJS.renderAbc('tune-notation', JSON.parse($primaryAbc[0].textContent));
                    } catch(e) {}
                }
            }
            // Render each alternate setting's inline notation div
            $panel.find('.setting-block:not(:first-child)').each(function() {
                var $block = $(this);
                var $abcEl = $block.find('.setting-abc-data');
                var $notDiv = $block.find('.setting-notation');
                if ($abcEl.length && $notDiv.length) {
                    try {
                        ABCJS.renderAbc($notDiv.attr('id'), JSON.parse($abcEl[0].textContent));
                    } catch(e) {}
                }
            });

            $backBtn.one('click', function() {
                $panel.html(savedHtml);
                $panel.removeData('tunePageState');
                if (tableId) {
                    if (isCollectionTable) {
                        filterAndPaginateCollectionTunes(tableId, savedPage);
                    } else {
                        paginateTable(tableId, savedPage);
                    }
                }
            });
        });
    });

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
    var currentPages = {};
    var collectionRowsPerPage = 5;
    var currentCollectionPage = 1;
    var collectionTuneRowsPerPage = {};
    var collectionTunePages = {};
    var collectionActiveTabs = {};
    
    function paginateTable(tableId, page) {
        var showNoSetting = $('#show-no-setting').is(':checked');
        var $allRows = $('#' + tableId + ' tbody tr');
        var $rows = showNoSetting ? $allRows : $allRows.not('.no-setting');
        var total = $rows.length;
        var totalPages = Math.ceil(total / rowsPerPage);
        var start = (page - 1) * rowsPerPage;
        var end = start + rowsPerPage;

        // Reset all rows first, then show only the eligible slice
        $allRows.hide();
        $rows.slice(start, end).show();
    
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
        $('#tabs table').each(function() {
            paginateTable($(this).attr('id'), page);
        });
    }

    function filterAndPaginate(tableId, filter, page) {
        var showNoSetting = $('#show-no-setting').is(':checked');
        var $allRows = $('#' + tableId + ' tbody tr');

        $allRows.each(function() {
            var title = $(this).find('.tune_title').text().toLowerCase();
            var matchesFilter = filter === '' || title.indexOf(filter) !== -1;
            var matchesNoSetting = showNoSetting || !$(this).hasClass('no-setting');
            $(this).toggle(matchesFilter && matchesNoSetting);
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
    
        currentPages[tableId] = page; // ADD THIS
    
        if (filter === '') {
            paginateTable(tableId, page);
        } else {
            filterAndPaginate(tableId, filter, page);
        }
    });

    $('#tune-filter').on('input', function() {
        var filter = $(this).val().toLowerCase();
        var tableId = $("#tabs .ui-tabs-panel:visible table").attr('id');
        currentPages = {}; // ADD THIS - reset all saved pages on new search

        if (filter === '') {
            paginateTable(tableId, 1);
        } else {
            filterAndPaginate(tableId, filter, 1);
        }
    });

    $(document).on('change', '#show-no-setting', function() {
        currentPages = {};
        if ($('#tabs').length) {
            var filter = $('#tune-filter').val().toLowerCase();
            var tableId = $("#tabs .ui-tabs-panel:visible table").attr('id');
            if (filter === '') {
                paginateAll(1);
            } else {
                filterAndPaginate(tableId, filter, 1);
            }
        }
        $('.collection-tunes-table').each(function() {
            filterAndPaginateCollectionTunes($(this).attr('id'), 1);
        });
    });

    $("#tabs").on("tabsactivate", function(event, ui) {
        // If the tab we're leaving was showing a tune detail, restore its table
        var oldState = ui.oldPanel.data('tunePageState');
        if (oldState) {
            ui.oldPanel.html(oldState.savedHtml);
            ui.oldPanel.removeData('tunePageState');
            if (oldState.tableId) {
                if (oldState.isCollectionTable) {
                    filterAndPaginateCollectionTunes(oldState.tableId, oldState.savedPage);
                } else {
                    paginateTable(oldState.tableId, oldState.savedPage);
                }
            }
        }

        var tableId = ui.newPanel.find('table').attr('id');
        var filter = $('#tune-filter').val().toLowerCase();
        var page = currentPages[tableId] || 1;

        if (filter === '') {
            paginateTable(tableId, page);
        } else {
            filterAndPaginate(tableId, filter, page);
        }
    });

    function renderCollectionPagination(page, totalPages) {
        var $controls = $('#collections-pagination-controls');
        $controls.empty();

        if (totalPages <= 1) {
            return;
        }

        for (var i = 1; i <= totalPages; i++) {
            $controls.append(
                $('<a>')
                    .text(i)
                    .addClass('collection-page-link' + (i === page ? ' active' : ''))
                    .attr('data-page', i)
            );
        }
    }

    function filterAndPaginateCollections(page) {
        var $accordion = $('#collections-accordion');

        if (!$accordion.length) {
            return;
        }

        var filter = ($('#collection-filter').val() || '').toLowerCase();
        collectionRowsPerPage = parseInt($('#collections-per-page-select').val(), 10) || 5;

        var matchedHeaders = [];

        $accordion.find('.collection-header').each(function() {
            var $header = $(this);
            var searchText = $header.find('.collection-title').text().toLowerCase();

            if (filter === '' || searchText.indexOf(filter) !== -1) {
                matchedHeaders.push($header);
            }
        });

        var totalPages = Math.ceil(matchedHeaders.length / collectionRowsPerPage);

        if (totalPages === 0) {
            page = 1;
        } else if (page > totalPages) {
            page = totalPages;
        }

        currentCollectionPage = page;

        $accordion.find('.collection-header, .collection-body').hide();

        if (matchedHeaders.length === 0) {
            $('#collections-empty-state').show();
        } else {
            $('#collections-empty-state').hide();

            var start = (page - 1) * collectionRowsPerPage;
            var end = start + collectionRowsPerPage;

            $(matchedHeaders.slice(start, end)).each(function() {
                $(this).show();
                $(this).next('.collection-body').show();
            });
        }

        renderCollectionPagination(page, totalPages);

        if ($accordion.hasClass('ui-accordion')) {
            $accordion.accordion('option', 'active', false);
            $accordion.accordion('refresh');
        }
    }

    function getCollectionTuneFilter(collectionId) {
        return ($('#collection-tune-filter-' + collectionId).val() || '').toLowerCase();
    }

    function getCollectionTuneRowsPerPage(collectionId) {
        return collectionTuneRowsPerPage[collectionId] || parseInt($('#collection-tunes-per-page-' + collectionId).val(), 10) || 10;
    }

    function getActiveCollectionTableId(collectionId) {
        var $tabs = $('#collection-tabs-' + collectionId);
        var $panel = $tabs.find('.ui-tabs-panel:visible');

        if (!$panel.length) {
            $panel = $tabs.children('div.collection-tab-panel').first();
        }

        return $panel.find('table').attr('id');
    }

    function renderCollectionTunePagination(tableId, page, totalPages) {
        var $controls = $('#pagination-' + tableId);
        $controls.empty();

        if (totalPages <= 1) {
            return;
        }

        for (var i = 1; i <= totalPages; i++) {
            $controls.append(
                $('<a>')
                    .text(i)
                    .addClass('collection-tune-page-link' + (i === page ? ' active' : ''))
                    .attr('data-page', i)
                    .attr('data-table', tableId)
            );
        }
    }

    function filterAndPaginateCollectionTunes(tableId, page) {
        var $table = $('#' + tableId);

        if (!$table.length) {
            return;
        }

        var collectionId = $table.data('collection-id');
        var filter = getCollectionTuneFilter(collectionId);
        var rowsPerPageForTable = getCollectionTuneRowsPerPage(collectionId);
        var showNoSetting = $('#show-no-setting').is(':checked');
        var $rows = $table.find('tbody tr');
        var $matchedRows = $rows.filter(function() {
            var title = $(this).find('.tune_title').text().toLowerCase();
            var matchesFilter = filter === '' || title.indexOf(filter) !== -1;
            var matchesNoSetting = showNoSetting || !$(this).hasClass('no-setting');
            return matchesFilter && matchesNoSetting;
        });

        var totalPages = Math.ceil($matchedRows.length / rowsPerPageForTable);

        if (totalPages === 0) {
            page = 1;
        } else if (page > totalPages) {
            page = totalPages;
        }

        collectionTunePages[tableId] = page;

        $rows.hide();

        if ($matchedRows.length) {
            var start = (page - 1) * rowsPerPageForTable;
            $matchedRows.slice(start, start + rowsPerPageForTable).show();
        }

        renderCollectionTunePagination(tableId, page, totalPages);
    }

    function paginateCollectionTunesForAllTabs(collectionId, resetPages) {
        var $tables = $('#collection-tabs-' + collectionId + ' table.collection-tunes-table');

        if (resetPages) {
            $tables.each(function() {
                collectionTunePages[$(this).attr('id')] = 1;
            });
        }

        $tables.each(function() {
            var tableId = $(this).attr('id');
            var page = collectionTunePages[tableId] || 1;
            filterAndPaginateCollectionTunes(tableId, page);
        });
    }

    function initializeCollectionTabs(collectionId) {
        var $tabs = $('#collection-tabs-' + collectionId);

        if (!$tabs.length) {
            return;
        }

        if ($tabs.hasClass('ui-tabs')) {
            $tabs.tabs('destroy');
        }

        var activeIndex = typeof collectionActiveTabs[collectionId] === 'number' ? collectionActiveTabs[collectionId] : 0;

        $tabs.tabs({
            active: activeIndex,
            activate: function(event, ui) {
                var newIndex = ui.newTab.index();
                var tableId = ui.newPanel.find('table').attr('id');

                collectionActiveTabs[collectionId] = newIndex;

                // If the tab we're leaving was showing a tune detail, restore its table
                var oldState = ui.oldPanel.data('tunePageState');
                if (oldState) {
                    ui.oldPanel.html(oldState.savedHtml);
                    ui.oldPanel.removeData('tunePageState');
                    if (oldState.tableId) {
                        if (oldState.isCollectionTable) {
                            filterAndPaginateCollectionTunes(oldState.tableId, oldState.savedPage);
                        } else {
                            paginateTable(oldState.tableId, oldState.savedPage);
                        }
                    }
                }

                if (tableId) {
                    filterAndPaginateCollectionTunes(tableId, collectionTunePages[tableId] || 1);
                }
            }
        });

        var activeTableId = getActiveCollectionTableId(collectionId);
        if (activeTableId) {
            filterAndPaginateCollectionTunes(activeTableId, collectionTunePages[activeTableId] || 1);
        }
    }

    window.initializeCollectionsPage = function() {
        var $accordion = $('#collections-accordion');

        if (!$accordion.length) {
            return;
        }

        if ($accordion.hasClass('ui-accordion')) {
            $accordion.accordion('destroy');
        }

        $accordion.accordion({
            collapsible: true,
            active: false,
            heightStyle: "content"
        });

        $('.collection-tunes-per-page').each(function() {
            var collectionId = $(this).data('collection-id');
            collectionTuneRowsPerPage[collectionId] = parseInt($(this).val(), 10) || 10;
        });

        $('.collection-tabs').each(function() {
            initializeCollectionTabs($(this).data('collection-id'));
        });

        filterAndPaginateCollections(1);
    };

    $(document).on('change', '#collections-per-page-select', function() {
        filterAndPaginateCollections(1);
    });

    $(document).on('input', '#collection-filter', function() {
        filterAndPaginateCollections(1);
    });

    $(document).on('click', '.collection-page-link', function() {
        var page = parseInt($(this).data('page'), 10) || 1;
        filterAndPaginateCollections(page);
    });

    $(document).on('change', '.collection-tunes-per-page', function() {
        var collectionId = $(this).data('collection-id');
        collectionTuneRowsPerPage[collectionId] = parseInt($(this).val(), 10) || 10;
        paginateCollectionTunesForAllTabs(collectionId, true);
    });

    $(document).on('input', '.collection-tune-filter', function() {
        var collectionId = $(this).data('collection-id');
        paginateCollectionTunesForAllTabs(collectionId, true);
    });

    $(document).on('click', '.collection-tune-page-link', function() {
        var tableId = $(this).data('table');
        var page = parseInt($(this).data('page'), 10) || 1;
        filterAndPaginateCollectionTunes(tableId, page);
    });

    if ($('#collections-accordion').length) {
        window.initializeCollectionsPage();
    }
    
});
