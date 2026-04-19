$(document).ready(function(){

    $(document).on('click keydown', function() {
        $('.tune-highlighted').removeClass('tune-highlighted');
    });

   //$('.show_abc').click(function() {
    $(document).on('click', '.tune_title', function() {
        var tune_id = $(this).attr('id');
        sessionStorage.setItem('lastViewedTuneId', tune_id);
        var $panel = $(this).closest('.ui-tabs-panel');
        if (!$panel.length) return;

        var $table = $panel.find('table');
        var tableId = $table.attr('id');
        var isCollectionTable = $table.hasClass('collection-tunes-table');
        var savedPage = isCollectionTable
            ? (collectionTunePages[tableId] || 1)
            : (currentPages[tableId] || 1);
        // Clear any open sheet music and its close icon before saving state
        $('#paper').empty();
        $('.show-abc-close').remove();

        var savedHtml = $panel.html();

        // Store state on the panel so tab-switch can also restore it
        $panel.data('tunePageState', {
            savedHtml: savedHtml,
            savedPage: savedPage,
            tableId: tableId,
            isCollectionTable: isCollectionTable
        });

        $('#select-tune-prompt').hide();

        $panel.load('page/tune-page?tune_id=' + tune_id, function() {
            // Remove close icon in case the $.post callback fired late and re-inserted it
            $('.show-abc-close').remove();

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
                $('#select-tune-prompt').show();
                if (tableId) {
                    if (isCollectionTable) {
                        filterAndPaginateCollectionTunes(tableId, savedPage);
                    } else {
                        paginateTable(tableId, savedPage);
                    }
                }
                var lastTuneId = sessionStorage.getItem('lastViewedTuneId');
                if (lastTuneId) {
                    setTimeout(function() {
                        var $row = $panel.find('tr#' + lastTuneId);
                        if ($row.length) {
                            $row.addClass('tune-highlighted');
                            $row[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }, 0);
                }
            });
        });
    });

    $(document).on('click', '.show_abc', function() {
        var setting_id = $(this).attr('id');
        var $thisSpan = $(this);
        var $thisIcon = $thisSpan.find('.music_note_icon');

        // If clicking the already-active icon, close notation
        if ($thisIcon.hasClass('active-notation')) {
            $("#paper").empty();
            $thisIcon.removeClass('active-notation');
            return;
        }

        // Reset any previously active notation icon
        $('.music_note_icon.active-notation').removeClass('active-notation');

        $.post("api/get-tune-body", { "setting_id": setting_id }, function(data) {
            if (data) {
                var tune = jQuery.parseJSON(data);

                ABCJS.renderAbc("paper",
                    "X:" + tune.setting_id + "\n" +
                    "T:" + tune.name + "\n" +
                    "M:" + tune.time_signature + "\n" +
                    "L: 1/8 \n" +
                    "K:" + tune.key_signature + "\n" +
                    tune.abc_transcription
                );
                // Mark this icon as active
                $thisIcon.addClass('active-notation');
            }
        });
    });
    
    $(document).on('click', '.favorite-toggle', function() {
        var $toggle = $(this);
        var $icon   = $toggle.find('.favorite-icon');
        var userId  = $toggle.data('user-id');
        var tuneId  = $toggle.closest('tr').attr('id');
        var isFavorited = $icon.hasClass('favorited');
        var apiUrl = isFavorited ? 'api/remove-favorite' : 'api/favorite-tune';
        var postData = isFavorited
            ? { tune_id: tuneId }
            : { tune_id: tuneId, user_id: userId };

        $.post(apiUrl, postData)
        .done(function() {
            if (isFavorited) {
                $icon.removeClass('favorited fa-xmark').addClass('fa-plus');
            } else {
                $icon.removeClass('fa-plus').addClass('favorited fa-xmark');
            }
            var message = isFavorited ? 'Removed from favorites.' : 'Added to favorites.';
            $('<div class="alert-box">' + message + '</div>')
                .appendTo('#pop_up')
                .delay(600)
                .fadeOut(150, function () {
                    $(this).remove();
                });
        }).fail(function(xhr) {
            $('<div class="alert-box">' + (xhr.responseText || 'Could not update favorite.') + '</div>')
                .appendTo('#pop_up')
                .delay(1500)
                .fadeOut(300, function () {
                    $(this).remove();
                });
        });
    });

    // ── Remove favorite ────────────────────────────────────────────────────────

    $(document).on('click', '.remove-favorite-icon', function() {
        var $row   = $(this).closest('tr');
        var tuneId = $row.attr('id');

        $.post('api/remove-favorite', { tune_id: tuneId })
        .done(function() {
            delete selectedTunes[tuneId];
            updateSelectionBar();
            if ($('#save-collection-form').is(':visible')) {
                $('#fav-abc-text').val(buildAbcFromSelection());
            }
            $row.fadeOut(300, function () {
                $(this).remove();
            });
            $('<div class="alert-box">Removed from favorites.</div>')
                .appendTo('#pop_up')
                .delay(600)
                .fadeOut(150, function () {
                    $(this).remove();
                });
        })
        .fail(function(xhr) {
            $('<div class="alert-box">' + (xhr.responseText || 'Could not remove favorite.') + '</div>')
                .appendTo('#pop_up')
                .delay(1500)
                .fadeOut(300, function () {
                    $(this).remove();
                });
        });
    });

    // ── Add to collection selection ─────────────────────────────────────────────

    var selectedTunes = {};

    function updateSelectionBar() {
        var count = Object.keys(selectedTunes).length;
        $('#collection-selection-count').text(count);
        if (count > 0) {
            $('#collection-selection-bar').slideDown();
        } else {
            $('#collection-selection-bar').slideUp();
        }
    }

    function buildAbcFromSelection() {
        var abcParts = [];
        var xNumber = 1;
        $.each(selectedTunes, function (tuneId, tuneData) {
            var abc = 'X:' + xNumber + '\n' +
                      'T:' + tuneData.name + '\n' +
                      'M:' + tuneData.timeSignature + '\n' +
                      'L:1/8\n' +
                      'K:' + tuneData.keySignature + '\n' +
                      tuneData.abc;
            abcParts.push(abc);
            xNumber++;
        });
        return abcParts.join('\n\n');
    }

    $(document).on('click', '.collection-select-icon', function (event) {
        event.stopPropagation();
        var $row = $(this).closest('tr');
        var tuneId = $row.attr('id');

        if (selectedTunes[tuneId]) {
            delete selectedTunes[tuneId];
            $row.removeClass('collection-selected');
            $(this).removeClass('collection-active fa-xmark').addClass('fa-plus');
        } else {
            selectedTunes[tuneId] = {
                name: $row.data('tune-name'),
                keySignature: $row.data('key-signature'),
                timeSignature: $row.data('time-signature'),
                abc: $row.data('abc')
            };
            $row.addClass('collection-selected');
            $(this).addClass('collection-active').removeClass('fa-plus').addClass('fa-xmark');
        }
        updateSelectionBar();
        if ($('#save-collection-form').is(':visible')) {
            $('#fav-abc-text').val(buildAbcFromSelection());
        }
    });

    $(document).on('click', '#clear-collection-btn', function () {
        selectedTunes = {};
        $('.collection-selected').removeClass('collection-selected');
        $('.collection-select-icon').removeClass('collection-active fa-xmark').addClass('fa-plus');
        updateSelectionBar();
        $('#save-collection-form').slideUp();
    });

    $(document).on('click', '#save-collection-btn', function () {
        var abcText = buildAbcFromSelection();
        $('#fav-abc-text').val(abcText);
        $('#save-collection-form').slideDown();
    });

    $(document).on('change', '#collection-mode', function () {
        var mode = $(this).val();
        if (mode === 'new') {
            $('#new-collection-fields').slideDown();
            $('#collection-submit-btn').text('Create Collection');
            $('#save-collection-form h2').text('Create Collection');
        } else {
            $('#new-collection-fields').slideUp();
            $('#collection-submit-btn').text('Add to Collection');
            $('#save-collection-form h2').text('Add to Collection');
        }
    });

    $(document).on('click', '#cancel-collection-form-btn', function () {
        $('#save-collection-form').slideUp();
    });

    $(document).on('submit', '#favorites-collection-form', function (event) {
        event.preventDefault();
        var $form = $(this);
        var tuneIds = Object.keys(selectedTunes);
        var mode = $('#collection-mode').val();
        var apiUrl, formData;

        var removeFromFavorites = $('#remove-from-favorites').is(':checked') ? '1' : '0';

        if (mode === 'new') {
            formData = $form.serialize() + '&tune_ids=' + encodeURIComponent(JSON.stringify(tuneIds)) + '&remove_from_favorites=' + removeFromFavorites;
            apiUrl = 'api/create-collection-from-favorites';
        } else {
            formData = 'collection_id=' + mode + '&tune_ids=' + encodeURIComponent(JSON.stringify(tuneIds)) + '&remove_from_favorites=' + removeFromFavorites;
            apiUrl = 'api/add-to-existing-collection';
        }

        $.post(apiUrl, formData, function (response) {
            var result = (typeof response === 'string') ? JSON.parse(response) : response;
            if (result.success) {
                if (removeFromFavorites === '1') {
                    $('.collection-selected').fadeOut(300, function () {
                        $(this).remove();
                    });
                } else {
                    $('.collection-selected').removeClass('collection-selected');
                    $('.collection-select-icon').removeClass('collection-active fa-xmark').addClass('fa-plus');
                }
                selectedTunes = {};
                updateSelectionBar();
                $('#save-collection-form').slideUp();
                $form[0].reset();
                $('#new-collection-fields').slideDown();
                $('#collection-submit-btn').text('Create Collection');
                $('#fav-abc-text').val('');
                var message = mode === 'new'
                    ? 'Collection created with ' + result.tune_count + ' tune(s).'
                    : 'Added ' + result.tune_count + ' tune(s) to collection.';
                $('<div class="alert-box">' + message + '</div>')
                    .appendTo('#pop_up')
                    .delay(1500)
                    .fadeOut(300, function () {
                        $(this).remove();
                    });
            } else {
                $('<div class="alert-box">' + (result.error || 'Could not create collection.') + '</div>')
                    .appendTo('#pop_up')
                    .delay(1500)
                    .fadeOut(300, function () {
                        $(this).remove();
                    });
            }
        }).fail(function (xhr) {
            var errorResponse = (xhr.responseJSON) ? xhr.responseJSON : {};
            $('<div class="alert-box">' + (errorResponse.error || 'Could not create collection.') + '</div>')
                .appendTo('#pop_up')
                .delay(1500)
                .fadeOut(300, function () {
                    $(this).remove();
                });
        });
    });

    var rowsPerPage = 10;
    var currentPages = {};
    var collectionRowsPerPage = 5;
    var currentCollectionPage = 1;
    var collectionTuneRowsPerPage = {};
    var collectionTunePages = {};
    
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

    function handleTabsActivate(event, ui) {
        // If the tab we're leaving was showing a tune detail, restore its table
        var oldState = ui.oldPanel.data('tunePageState');
        if (oldState) {
            ui.oldPanel.html(oldState.savedHtml);
            ui.oldPanel.removeData('tunePageState');
            $('#select-tune-prompt').show();
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
    }

    // Exposed globally so tunes.php's inline script can call it after the content
    // is injected via jQuery .load(). Re-binds tabsactivate to the fresh #tabs
    // element each time the page loads — the direct binding in $(document).ready()
    // would only attach to the first #tabs element and break after AJAX navigation.
    window.initializeTunesPage = function() {
        sessionStorage.removeItem('lastViewedTuneId');
        paginateAll(1);
        $("#tabs").off("tabsactivate").on("tabsactivate", handleTabsActivate);
    };

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

    $(document).on('input', '#tune-filter', function() {
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

    // tabsactivate is bound inside window.initializeTunesPage so it re-attaches
    // to the fresh #tabs element on every AJAX navigation to the tunes page.

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

            // Hide other users' private collections
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

        $tabs.tabs({
            active: 0,
            activate: function(event, ui) {
                var newIndex = ui.newTab.index();
                var tableId = ui.newPanel.find('table').attr('id');

                // If the tab we're leaving was showing a tune detail, restore its table
                var oldState = ui.oldPanel.data('tunePageState');
                if (oldState) {
                    ui.oldPanel.html(oldState.savedHtml);
                    ui.oldPanel.removeData('tunePageState');
                    $('#select-tune-prompt').show();
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
