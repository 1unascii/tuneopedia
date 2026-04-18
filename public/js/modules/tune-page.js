$(document).ready(function () {

    // ── Notes expand/collapse ─────────────────────────────────────────────────
    $(document).on('click', '.tune-page-notes-toggle', function () {
        var $notes = $(this).closest('.tune-page-notes');
        var $body  = $notes.find('.tune-page-notes-body');
        if ($notes.hasClass('collapsed')) {
            $notes.removeClass('collapsed');
            $body.css('max-height', $body[0].scrollHeight + 'px');
            $(this).text('Show less');
        } else {
            $body.css('max-height', $body[0].scrollHeight + 'px');// set explicit height before collapsing so transition works
            $body[0].offsetHeight;// force reflow
            $notes.addClass('collapsed');
            $body.css('max-height', '');
            $(this).text('Show more');
        }
    });

    // ── Edit button: load form inline ─────────────────────────────────────────
    $(document).on('click', '.edit-setting-btn', function () {
        var $btn      = $(this);
        var settingId = $btn.data('setting-id');
        var $block    = $btn.closest('.setting-block');
        var $editArea = $block.find('.setting-edit-area');

        // Toggle: if form already open, close it and restore original notation
        if ($editArea.is(':visible')) {
            restoreNotation($block);
            $editArea.hide().empty();
            return;
        }

        $editArea.html('<p class="edit-loading">Loading...</p>').show();

        $.get('api/edit-setting', { setting_id: settingId }, function (html) {
            $editArea.html(html);

            // Render live preview immediately from the loaded form values
            renderFromForm($block);

            // Keep re-rendering on any field change
            $editArea.on('input change', 'input, select, textarea', function () {
                renderFromForm($block);
            });
        });
    });

    // ── Cancel edit: restore the original notation ────────────────────────────
    $(document).on('click', '.edit-cancel-btn', function () {
        var $block    = $(this).closest('.setting-block');
        var $editArea = $(this).closest('.setting-edit-area');
        restoreNotation($block);
        $editArea.hide().empty();
    });

    // ── Build ABC string from form fields and render into the notation div ────
    function renderFromForm($block) {
        var $form = $block.find('.edit-setting-form');
        if (!$form.length) return;

        var settingId = $block.data('setting-id');
        var abcString =
            'X:' + settingId + '\n' +
            'T:' + ($form.find('[name="tune_name"]').val()           || '') + '\n' +
            'M:' + ($form.find('[name="time_signature"]').val()      || '4/4') + '\n' +
            'L:' + ($form.find('[name="default_note_length"]').val() || '1/8') + '\n' +
            'K:' + ($form.find('[name="key_signature"]').val()       || '') + '\n' +
            ($form.find('[name="abc_transcription"]').val() || '');

        renderNotation($block, abcString);
    }

    // ── Re-render the original (stored) ABC for a block ───────────────────────
    function restoreNotation($block) {
        var $abcEl = $block.find('.setting-abc-data');
        if (!$abcEl.length) return;
        try {
            renderNotation($block, JSON.parse($abcEl[0].textContent));
        } catch (e) {}
    }

    // ── Render an ABC string into the correct notation div for a block ─────────
    function renderNotation($block, abcString) {
        var settingId = $block.data('setting-id');
        var targetId  = $block.hasClass('primary-setting')
            ? 'tune-notation'
            : 'setting-notation-' + settingId;
        ABCJS.renderAbc(targetId, abcString);
    }

    // ── Save edit ─────────────────────────────────────────────────────────────
    $(document).on('submit', '.edit-setting-form', function (e) {
        e.preventDefault();
        var $form     = $(this);
        var settingId = $form.data('setting-id');
        var $block    = $form.closest('.setting-block');

        $.post('api/edit-setting', $form.serialize(), function (data) {
            if (data.error) {
                $form.find('.edit-error').text(data.error);
                return;
            }

            var s = data.setting;

            // Update meta display
            $block.find('.setting-key').text(s.key_signature);
            $block.find('.setting-time').text(s.time_signature);

            // Rebuild and re-render the ABC for this setting
            var newAbc =
                'X:' + s.setting_id + '\n' +
                'T:' + s.tune_name  + '\n' +
                'M:' + s.time_signature + '\n' +
                'L:' + s.default_note_length + '\n' +
                'K:' + s.key_signature + '\n' +
                s.abc_transcription;

            // Update the stored ABC data used by reordering
            $block.find('.setting-abc-data').text(JSON.stringify(newAbc));

            var isPrimary = $block.hasClass('primary-setting');
            if (isPrimary) {
                ABCJS.renderAbc('tune-notation', newAbc);
            } else {
                var notationId = 'setting-notation-' + s.setting_id;
                ABCJS.renderAbc(notationId, newAbc);
            }

            // Close the form
            $block.find('.setting-edit-area').hide().empty();
        }, 'json');
    });

    // ── Vote button handler ───────────────────────────────────────────────────
    $(document).on('click', '.vote-btn', function () {
        var $btn       = $(this);
        var settingId  = parseInt($btn.data('setting-id'));
        var voteValue  = $btn.hasClass('vote-up') ? 1 : -1;
        var userId     = parseInt($('#tune-page').data('user-id') || $('#user-info').data('user-id') || 0);

        if (!userId) {
            alert('Please log in to vote.');
            return;
        }

        $.post('api/vote-setting', {
            setting_id: settingId,
            vote_value: voteValue
        }, function (data) {
            if (data.error) return;

            // Update the block's score and button states
            var $block = $('.setting-block[data-setting-id="' + settingId + '"]');
            $block.data('vote-score', data.vote_score);
            $block.find('.vote-score').text(data.vote_score);
            $block.find('.vote-up').toggleClass('vote-active', data.user_vote === 1);
            $block.find('.vote-down').toggleClass('vote-active', data.user_vote === -1);

            reorderSettings($('#tune-settings'));
        }, 'json');
    });

    // ── Reorder settings by vote score ───────────────────────────────────────
    function reorderSettings($container) {
        if (!$container.length) return;

        var currentPrimaryId = parseInt($container.data('primary-setting-id'));

        // Detach, sort, re-append — DOM must be updated BEFORE calling ABCJS.renderAbc
        // because ABCJS looks up the target via document.getElementById and won't find
        // detached elements.
        var $blocks = $container.find('.setting-block').detach();
        $blocks.sort(function (a, b) {
            return parseInt($(b).data('vote-score')) - parseInt($(a).data('vote-score'));
        });

        var newPrimaryId = parseInt($($blocks[0]).data('setting-id'));
        var primaryChanged = (newPrimaryId !== currentPrimaryId);

        // Re-append in new order and renumber labels first
        $blocks.each(function (i) {
            $(this).find('.setting-label').text('Setting ' + (i + 1));
            $container.append(this);
        });

        // Now that elements are in the DOM, handle notation updates
        if (primaryChanged) {
            var $newPrimary = $container.find('.setting-block[data-setting-id="' + newPrimaryId + '"]');
            var $oldPrimary = $container.find('.setting-block[data-setting-id="' + currentPrimaryId + '"]');

            // Render new primary's notation into #tune-notation at the top
            var $newAbc = $newPrimary.find('.setting-abc-data');
            if ($newAbc.length) {
                try {
                    ABCJS.renderAbc('tune-notation', JSON.parse($newAbc[0].textContent));
                } catch (e) {}
            }

            // Render old primary's notation into its per-setting div, then show it
            var $oldNotDiv = $oldPrimary.find('.setting-notation');
            if ($oldNotDiv.length) {
                var $oldAbc = $oldPrimary.find('.setting-abc-data');
                if ($oldAbc.length) {
                    try {
                        ABCJS.renderAbc($oldNotDiv.attr('id'), JSON.parse($oldAbc[0].textContent));
                    } catch (e) {}
                }
                $oldNotDiv.show();
            }

            // Hide new primary's per-setting notation div (shown in #tune-notation above)
            $newPrimary.find('.setting-notation').hide();

            // Update classes and tracking
            $oldPrimary.removeClass('primary-setting');
            $newPrimary.addClass('primary-setting');
            $container.data('primary-setting-id', newPrimaryId);
        }
    }

});
