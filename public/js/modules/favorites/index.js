$(document).ready(function() {

    // ── Remove favorite ──────────────────────────────────────────────────────

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

    // ── Collection selection state ───────────────────────────────────────────

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

    // ── Add/remove from collection selection ─────────────────────────────────

    $(document).on('click', '.collection-select-icon', function (event) {
        event.stopPropagation();
        var $row = $(this).closest('tr');
        var tuneId = $row.attr('id');

        if (selectedTunes[tuneId]) {
            delete selectedTunes[tuneId];
            $row.removeClass('collection-selected');
            $(this).removeClass('collection-active fa-square-check fa-solid').addClass('fa-regular fa-square');
        } else {
            selectedTunes[tuneId] = {
                name: $row.data('tune-name'),
                keySignature: $row.data('key-signature'),
                timeSignature: $row.data('time-signature'),
                abc: $row.data('abc')
            };
            $row.addClass('collection-selected');
            $(this).addClass('collection-active').removeClass('fa-regular fa-square').addClass('fa-solid fa-square-check');
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

    // ── Collection mode toggle ───────────────────────────────────────────────

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

    // ── Submit collection form ───────────────────────────────────────────────

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

});
