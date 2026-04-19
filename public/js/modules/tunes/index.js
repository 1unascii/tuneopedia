$(document).ready(function() {

    // ── Shared pagination state ──────────────────────────────────────────────

    var rowsPerPage = 10;
    var currentPages = {};

    // ── Clear highlight on click/keydown ──────────────────────────────────────

    $(document).on('click keydown', function() {
        $('.tune-highlighted').removeClass('tune-highlighted');
    });

    // ── Show ABC notation toggle ─────────────────────────────────────────────

    $(document).on('click', '.show_abc', function() {
        var settingId = $(this).attr('id');
        var $thisSpan = $(this);
        var $thisIcon = $thisSpan.find('.music_note_icon');

        if ($thisIcon.hasClass('active-notation')) {
            $("#paper").empty();
            $thisIcon.removeClass('active-notation');
            return;
        }

        $('.music_note_icon.active-notation').removeClass('active-notation');

        $.post("api/get-tune-body", { "setting_id": settingId }, function(data) {
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
                $thisIcon.addClass('active-notation');
            }
        });
    });

    // ── Favorite toggle ──────────────────────────────────────────────────────

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

    // ── Pagination ───────────────────────────────────────────────────────────

    function paginateTable(tableId, page) {
        var showNoSetting = $('#show-no-setting').is(':checked');
        var $allRows = $('#' + tableId + ' tbody tr');
        var $rows = showNoSetting ? $allRows : $allRows.not('.no-setting');
        var total = $rows.length;
        var totalPages = Math.ceil(total / rowsPerPage);
        var start = (page - 1) * rowsPerPage;
        var end = start + rowsPerPage;

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

    // ── Tabs activate handler ────────────────────────────────────────────────

    function handleTabsActivate(event, ui) {
        var oldState = ui.oldPanel.data('tunePageState');
        if (oldState) {
            ui.oldPanel.html(oldState.savedHtml);
            ui.oldPanel.removeData('tunePageState');
            $('#select-tune-prompt').show();
            if (oldState.tableId) {
                if (oldState.isCollectionTable) {
                    window.filterAndPaginateCollectionTunes(oldState.tableId, oldState.savedPage);
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

    window.initializeTunesPage = function() {
        sessionStorage.removeItem('lastViewedTuneId');
        paginateAll(1);
        $("#tabs").off("tabsactivate").on("tabsactivate", handleTabsActivate);
    };

    // ── Initialize ───────────────────────────────────────────────────────────

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

        currentPages[tableId] = page;

        if (filter === '') {
            paginateTable(tableId, page);
        } else {
            filterAndPaginate(tableId, filter, page);
        }
    });

    $(document).on('input', '#tune-filter', function() {
        var filter = $(this).val().toLowerCase();
        var tableId = $("#tabs .ui-tabs-panel:visible table").attr('id');
        currentPages = {};

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
            if (typeof window.filterAndPaginateCollectionTunes === 'function') {
                window.filterAndPaginateCollectionTunes($(this).attr('id'), 1);
            }
        });
    });

    // Expose for use by other modules
    window.paginateTable = paginateTable;

});
