/**
 * Favorite Toggle
 * ===============
 * Handles clicking the heart icon on tune lists.
 * Sends a fetch POST to toggle the favorite in the background
 * and swaps the icon between filled (favorited) and outline (not favorited)
 * without reloading the page.
 *
 * Requires:
 *   - A <meta name="csrf-token"> tag in the <head>
 *   - Buttons with class .favorite-toggle, data-url, and an <i> icon inside
 */
function initFavoriteToggle() {
    document.querySelectorAll('.favorite-toggle').forEach(function(btn) {
        // Skip if already initialized (prevents double-binding on Turbo navigation)
        if (btn.dataset.initialized) return;
        btn.dataset.initialized = 'true';

        btn.addEventListener('click', function() {
            var url = btn.dataset.url;
            var icon = btn.querySelector('i');
            var token = document.querySelector('meta[name="csrf-token"]').content;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            }).then(function() {
                // Toggle the icon between filled and outline heart
                if (icon.classList.contains('fa-solid')) {
                    icon.classList.remove('fa-solid', 'text-error');
                    icon.classList.add('fa-regular');
                } else {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid', 'text-error');
                }
            });
        });
    });
}

initFavoriteToggle();
document.addEventListener('turbo:load', initFavoriteToggle);
