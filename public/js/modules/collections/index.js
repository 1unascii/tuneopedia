$(document).ready(function() {

    var collectionRowsPerPage = 5;
    var currentCollectionPage = 1;
    var collectionTuneRowsPerPage = {};
    var collectionTunePages = {};

    // Expose for use by tunes/show.js
    window.collectionTunePages = collectionTunePages;

    // ── Collection pagination ────────────────────────────────────────────────

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
        var visibility = $('#collection-visibility').val() || 'all';
        var currentUserId = parseInt($('#collections-content').data('current-user-id') || 0, 10);
        collectionRowsPerPage = parseInt($('#collections-per-page-select').val(), 10) || 5;

        var matchedHeaders = [];

        $accordion.find('.collection-header').each(function() {
            var $header = $(this);
            var searchText = $header.find('.collection-title').text().toLowerCase();
            var isShared = $header.data('shared');
            var ownerId = parseInt($header.data('owner-id') || 0, 10);

            if (isShared != 1 && ownerId !== currentUserId && currentUserId > 0) {
                return;
            }
            if (isShared != 1 && currentUserId === 0) {
                return;
            }

            var matchesFilter = (filter === '' || searchText.indexOf(filter) !== -1);
            var matchesVisibility = (visibility === 'all') ||
                (visibility === 'public' && isShared == 1) ||
                (visibility === 'private' && isShared != 1);

            if (matchesFilter && matchesVisibility) {
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

    // ── Collection tune pagination ───────────────────────────────────────────

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

    // Expose globally for other modules
    window.filterAndPaginateCollectionTunes = filterAndPaginateCollectionTunes;

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

        $tabs.tabs({
            active: 0,
            activate: function(event, ui) {
                var tableId = ui.newPanel.find('table').attr('id');

                var oldState = ui.oldPanel.data('tunePageState');
                if (oldState) {
                    ui.oldPanel.html(oldState.savedHtml);
                    ui.oldPanel.removeData('tunePageState');
                    $('#select-tune-prompt').show();
                    if (oldState.tableId) {
                        if (oldState.isCollectionTable) {
                            filterAndPaginateCollectionTunes(oldState.tableId, oldState.savedPage);
                        } else if (typeof window.paginateTable === 'function') {
                            window.paginateTable(oldState.tableId, oldState.savedPage);
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

    // ── Initialize collections page ──────────────────────────────────────────

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

    // ── Event handlers ───────────────────────────────────────────────────────

    $(document).on('change', '#collections-per-page-select', function() {
        filterAndPaginateCollections(1);
    });

    $(document).on('change', '#collection-visibility', function() {
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
