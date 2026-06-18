/**
 * Tune List Page JS
 * =================
 * Handles tab switching, pagination, search filtering, and tune highlighting.
 *
 * Search filtering:
 *   As the user types in the search input, tune rows are filtered by title.
 *   Tabs with zero matching tunes are hidden. The first visible tab is activated.
 *   Pagination recalculates after each filter.
 *
 * Tab and pagination state is preserved automatically by Turbo's page cache.
 * When the user clicks a tune and hits back (via history.back()), Turbo restores
 * the cached snapshot with tabs, pagination, and highlight intact.
 */
function initTunePage() {
    var tabs = document.querySelectorAll('#tune-tabs .tab');
    var panels = document.querySelectorAll('.tune-panel');
    var perPageSelect = document.getElementById('per-page');
    var filterInput = document.getElementById('tune-filter');

    if (!tabs.length) return;

    // ── Tab switching ──────────────────────────────────────────────────
    // Extracted into a named function so both click handlers and the
    // search filter can switch tabs (e.g. when the active tab gets hidden)
    function activateTab(tab) {
        tabs.forEach(function(t) { t.classList.remove('tab-active'); });
        panels.forEach(function(p) { p.classList.add('hidden'); });
        tab.classList.add('tab-active');
        var panel = document.getElementById(tab.dataset.tab);
        if (panel) panel.classList.remove('hidden');
    }

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            activateTab(tab);
            // Re-paginate the newly visible panel so filtered rows stay hidden
            var panel = document.getElementById(tab.dataset.tab);
            if (panel) paginatePanel(panel, 1);
        });
    });

    // ── Pagination ─────────────────────────────────────────────────────
    // Paginates only visible (not filtered out) rows in a panel
    function getPerPage() {
        return parseInt(perPageSelect ? perPageSelect.value : 10);
    }

    function paginatePanel(panel, page) {
        var rows = Array.from(panel.querySelectorAll('tbody .tune-row'));
        // Only paginate rows that aren't hidden by the search filter
        var visibleRows = rows.filter(function(row) {
            return !row.classList.contains('filter-hidden');
        });
        var perPage = getPerPage();
        var totalPages = Math.ceil(visibleRows.length / perPage);
        var start = (page - 1) * perPage;
        var end = start + perPage;

        // Hide all rows, then show only the current page of visible rows
        rows.forEach(function(row) { row.style.display = 'none'; });
        visibleRows.forEach(function(row, i) {
            row.style.display = (i >= start && i < end) ? '' : 'none';
        });

        var controls = panel.querySelector('.pagination-controls');
        if (!controls) return;
        controls.innerHTML = '';

        if (totalPages <= 1) return;

        for (var i = 1; i <= totalPages; i++) {
            var link = document.createElement('button');
            link.textContent = i;
            link.className = 'btn btn-xs ' + (i === page ? 'btn-primary' : 'btn-ghost');
            link.dataset.page = i;
            link.addEventListener('click', function() {
                paginatePanel(panel, parseInt(this.dataset.page));
            });
            controls.appendChild(link);
        }
    }

    function paginateAll() {
        panels.forEach(function(panel) {
            paginatePanel(panel, 1);
        });
    }

    // ── Search filtering ───────────────────────────────────────────────
    // Filters tune rows by title as the user types.
    // Hides tabs that have zero matching tunes.
    // Activates the first visible tab if the current one becomes hidden.
    function filterTunes() {
        var query = filterInput ? filterInput.value.toLowerCase().trim() : '';

        // Mark each row as filter-hidden or not based on title match
        panels.forEach(function(panel) {
            var rows = panel.querySelectorAll('tbody .tune-row');
            rows.forEach(function(row) {
                var link = row.querySelector('.tune_title a');
                var text = link ? link.textContent.toLowerCase().trim() : '';
                if (query === '' || text.indexOf(query) > -1) {
                    row.classList.remove('filter-hidden');
                } else {
                    row.classList.add('filter-hidden');
                }
            });
        });

        // Show/hide tabs based on whether they have any matching tunes
        var firstVisibleTab = null;
        tabs.forEach(function(tab) {
            var panel = document.getElementById(tab.dataset.tab);
            if (!panel) return;
            var visibleRows = panel.querySelectorAll('tbody .tune-row:not(.filter-hidden)');
            if (visibleRows.length === 0) {
                tab.style.display = 'none';
            } else {
                tab.style.display = '';
                if (!firstVisibleTab) firstVisibleTab = tab;
            }
        });

        // If the current active tab got hidden, switch to the first visible one
        var activeTab = document.querySelector('#tune-tabs .tab-active');
        if (activeTab && activeTab.style.display === 'none' && firstVisibleTab) {
            activateTab(firstVisibleTab);
        }

        // Re-paginate after filtering
        paginateAll();
    }

    if (filterInput) {
        filterInput.addEventListener('input', filterTunes);
    }

    // Only paginate on fresh loads — Turbo cache restores preserve pagination state
    if (!document.querySelector('.tune-row[style]')) {
        paginateAll();
    }

    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            paginateAll();
        });
    }

    // ── Tune highlight ─────────────────────────────────────────────────
    // Highlight the tune title in orange when clicked.
    // history.back() restores the page from browser cache with the highlight intact.
    document.querySelectorAll('.tune-row a').forEach(function(link) {
        link.addEventListener('click', function() {
            document.querySelectorAll('.tune-row-highlight').forEach(function(row) {
                row.classList.remove('tune-row-highlight');
            });
            var row = link.closest('.tune-row');
            if (row) row.classList.add('tune-row-highlight');
        });
    });

    // Clear highlight when clicking anything else on the page
    if (document.querySelector('.tune-row-highlight')) {
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.tune-row-highlight')) {
                document.querySelectorAll('.tune-row-highlight').forEach(function(row) {
                    row.classList.remove('tune-row-highlight');
                });
            }
        }, { once: true });
    }
}

initTunePage();
document.addEventListener('turbo:load', initTunePage);
