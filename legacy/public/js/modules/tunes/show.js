$(document).ready(function() {

    var collectionTunePages = window.collectionTunePages || {};
    var base = (typeof APP_BASE !== 'undefined') ? APP_BASE : '';

    // ── Render all settings on a tune page ──────────────────────────────────

    function renderAllSettings($container) {
        var $page = $container || $('#tune-page');
        if (!$page.length) return;

        var params = typeof getTablatureParams === 'function' ? getTablatureParams() : {};
        params.add_classes = true;

        var $primaryBlock = $page.find('.setting-block:first');
        if ($primaryBlock.length) {
            var $primaryAbc = $primaryBlock.find('.setting-abc-data');
            if ($primaryAbc.length) {
                try {
                    var vis = ABCJS.renderAbc('tune-notation', JSON.parse($primaryAbc[0].textContent), params);
                    if (vis && vis[0]) $primaryBlock.data('visualObj', vis[0]);
                } catch(e) {}
            }
        }

        $page.find('.setting-block:not(:first-child)').each(function() {
            var $block = $(this);
            var $abcEl = $block.find('.setting-abc-data');
            var $notDiv = $block.find('.setting-notation');
            if ($abcEl.length && $notDiv.length) {
                try {
                    var vis = ABCJS.renderAbc($notDiv.attr('id'), JSON.parse($abcEl[0].textContent), params);
                    if (vis && vis[0]) $block.data('visualObj', vis[0]);
                } catch(e) {}
            }
        });
    }

    window.renderAllSettings = renderAllSettings;

    // ── Open a tune detail view inside a tab panel ───────────────────────────
    window.openTuneInPanel = function(tuneId, $panel) {
        if (!$panel || !$panel.length) return;
        if (typeof stopAllMidiPlayers === 'function') stopAllMidiPlayers();

        sessionStorage.setItem('lastViewedTuneId', tuneId);

        var $table = $panel.find('table');
        var tableId = $table.attr('id');
        var isCollectionTable = $table.hasClass('collection-tunes-table');
        var currentPages = window.currentPages || {};
        var savedPage = isCollectionTable
            ? (collectionTunePages[tableId] || 1)
            : (currentPages[tableId] || 1);

        $('#paper').empty();

        var savedHtml = $panel.html();

        $panel.data('tunePageState', {
            savedHtml: savedHtml,
            savedPage: savedPage,
            tableId: tableId,
            isCollectionTable: isCollectionTable
        });

        $('#select-tune-prompt').hide();

        $panel.load(base + '/page/tune-page?tune_id=' + tuneId, function() {
            var $backBtn = $('<button class="tune-back-btn">&#8592; Back</button>');
            $panel.prepend($backBtn);

            renderAllSettings($panel);

            $backBtn.one('click', function() {
                if (typeof stopAllMidiPlayers === 'function') stopAllMidiPlayers();
                $panel.html(savedHtml);
                $panel.removeData('tunePageState');
                $('#select-tune-prompt').show();

                if (tableId) {
                    if (isCollectionTable && typeof window.filterAndPaginateCollectionTunes === 'function') {
                        window.filterAndPaginateCollectionTunes(tableId, savedPage);
                    } else if (typeof window.paginateTable === 'function') {
                        window.paginateTable(tableId, savedPage);
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
    };

    // ── Notes expand/collapse ────────────────────────────────────────────────

    $(document).on('click', '.tune-page-notes-toggle', function () {
        var $notes = $(this).closest('.tune-page-notes');
        var $body  = $notes.find('.tune-page-notes-body');
        if ($notes.hasClass('collapsed')) {
            $notes.removeClass('collapsed');
            $body.css('max-height', $body[0].scrollHeight + 'px');
            $(this).text('Show less');
        } else {
            $body.css('max-height', $body[0].scrollHeight + 'px');
            $body[0].offsetHeight;
            $notes.addClass('collapsed');
            $body.css('max-height', '');
            $(this).text('Show more');
        }
    });

});
