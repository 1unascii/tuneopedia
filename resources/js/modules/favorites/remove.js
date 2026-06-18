/**
 * Favorite Remove
 * ===============
 * Handles clicking the "Remove" button on the favorites index page.
 * Sends a fetch POST to toggle the favorite off in the background,
 * then fades out and removes the card from the DOM without reloading.
 *
 * Requires:
 *   - A <meta name="csrf-token"> tag in the <head>
 *   - Buttons with class .favorite-remove and data-url attribute
 *   - The button should be inside a .card element (the row to remove)
 */
function initFavoriteRemove() {
    document.querySelectorAll('.favorite-remove').forEach(function(btn) {
        if (btn.dataset.initialized) return;
        btn.dataset.initialized = 'true';

        btn.addEventListener('click', function() {
            var url = btn.dataset.url;
            var card = btn.closest('.card');
            var token = document.querySelector('meta[name="csrf-token"]').content;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            }).then(function() {
                // Fade out the card then remove it from the DOM
                card.style.transition = 'opacity 0.3s ease';
                card.style.opacity = '0';
                setTimeout(function() {
                    card.remove();

                    // If no favorites left, show the empty message
                    if (!document.querySelector('.favorite-remove')) {
                        var container = document.querySelector('.max-w-4xl .mt-6');
                        if (container) {
                            container.innerHTML = '<p class="text-base-content/60 text-center py-8">No favorites yet. Browse the <a href="/tunes" class="text-primary hover:underline">tunebook</a> to add some!</p>';
                        }
                    }
                }, 300);
            });
        });
    });
}

initFavoriteRemove();
document.addEventListener('turbo:load', initFavoriteRemove);
