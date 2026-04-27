$(document).ready(function() {

    var collectionTunePages = window.collectionTunePages || {};
    var base = (typeof APP_BASE !== 'undefined') ? APP_BASE : '';

    // ── Open a tune detail view inside a tab panel ───────────────────────────
    // This is the core function that loads the tune page into the panel,
    // saves the panel state for the back button, and renders notation.
    // Called by the .tune_title click handler and by auto-open on deep links.
    window.openTuneInPanel = function(tuneId, $panel) {
        if (!$panel || !$panel.length) return;
        stopAllMidiPlayers();

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

            var params = getTablatureParams();
            params.add_classes = true;

            var $primaryBlock = $panel.find('.setting-block:first');
            if ($primaryBlock.length) {
                var $primaryAbc = $primaryBlock.find('.setting-abc-data');
                if ($primaryAbc.length) {
                    try {
                        var vis = ABCJS.renderAbc('tune-notation', JSON.parse($primaryAbc[0].textContent), params);
                        if (vis && vis[0]) $primaryBlock.data('visualObj', vis[0]);
                    } catch(e) {}
                }
            }

            $panel.find('.setting-block:not(:first-child)').each(function() {
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

            $backBtn.one('click', function() {
                stopAllMidiPlayers();
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

    // ── Click handler for tune titles ────────────────────────────────────────
    $(document).on('click', '.tune_title', function() {
        var tuneId = $(this).attr('id');
        var $panel = $(this).closest('.ui-tabs-panel');
        window.openTuneInPanel(tuneId, $panel);
    });

    // ── Custom tuning ───────────────────────────────────────────────────────

    function parseCustomTuning(input) {
        // Parses tuning like "DAdea", "D,AdF#a", "gDGBd"
        // Returns an array of ABC note strings
        var tuning = [];
        var i = 0;
        while (i < input.length) {
            var ch = input[i];
            if (!/[A-Ga-g]/.test(ch)) { i++; continue; }
            var note = ch;
            i++;
            // Check for # or b after the note letter
            if (i < input.length && input[i] === '#') {
                note = '^' + ch;
                i++;
            } else if (i < input.length && input[i] === 'b' && /[A-G]/.test(ch)) {
                note = '_' + ch;
                i++;
            }
            // Check for octave modifiers , or '
            while (i < input.length && (input[i] === ',' || input[i] === "'")) {
                note += input[i];
                i++;
            }
            tuning.push(note);
        }
        return tuning;
    }



    function applyCustomTuning() {
        stopAllMidiPlayers();
        var instrument = $('#custom-instrument').val();
        var numStrings = parseInt($('#custom-strings').val());
        var input = $('#custom-tuning-input').val().trim();
        if (!input) return;

        var tuning = parseCustomTuning(input);
        if (tuning.length !== numStrings) {
            alert('Expected ' + numStrings + ' strings but got ' + tuning.length + ' from "' + input + '"');
            return;
        }

        // For banjo, move the first string (drone) to the end
        if (instrument === 'banjo') {
            tuning.push(tuning.shift());
        }

        window.tablaturePresets['custom'] = {
            instrument: instrument,
            tuning: tuning,
            label: $('#custom-instrument option:selected').text() + ' (' + input + ')'
        };

        // Render all settings with the custom tuning
        var $page = $('#tune-page');
        if (!$page.length) return;
        var params = getTablatureParams();
        params.add_classes = true;

        $page.find('.setting-block').each(function() {
            var $block = $(this);
            var $editArea = $block.find('.setting-edit-area');
            if ($editArea.children().length && $editArea.css('display') !== 'none') {
                renderFromForm($block);
                var editVis = $block.data('visualObj');
                if (editVis) setMidiTune($block, editVis, true);
            } else {
                var $abcEl = $block.find('.setting-abc-data');
                if ($abcEl.length) {
                    var targetId = $block.hasClass('primary-setting')
                        ? 'tune-notation'
                        : 'setting-notation-' + $block.data('setting-id');
                    var $notDiv = $block.find('.setting-notation');
                    if ($block.hasClass('primary-setting') || $notDiv.length) {
                        try {
                            var vis = ABCJS.renderAbc(targetId, JSON.parse($abcEl[0].textContent), params);
                            if (vis && vis[0]) {
                                $block.data('visualObj', vis[0]);
                                setMidiTune($block, vis[0], true);
                            }
                        } catch(e) {}
                    }
                }
            }
        });
    }

    $(document).on('click', '#custom-tuning-apply', applyCustomTuning);

    // ── Tablature instrument change ─────────────────────────────────────────

    $(document).on('change', '#tablature-instrument', function () {
        var val = $(this).val();
        if (val === 'custom') {
            $('#custom-tuning-controls').show();
            return;
        }
        $('#custom-tuning-controls').hide();

        stopAllMidiPlayers();

        var $page = $(this).closest('#tune-page');
        if (!$page.length) return;
        var params = getTablatureParams();
        params.add_classes = true;

        $page.find('.setting-block').each(function() {
            var $block = $(this);
            var $editArea = $block.find('.setting-edit-area');

            if ($editArea.children().length && $editArea.css('display') !== 'none') {
                renderFromForm($block);
                var editVis = $block.data('visualObj');
                if (editVis) setMidiTune($block, editVis, true);
            } else {
                var $abcEl = $block.find('.setting-abc-data');
                if ($abcEl.length) {
                    var targetId = $block.hasClass('primary-setting')
                        ? 'tune-notation'
                        : 'setting-notation-' + $block.data('setting-id');
                    var $notDiv = $block.find('.setting-notation');
                    if ($block.hasClass('primary-setting') || $notDiv.length) {
                        try {
                            var vis = ABCJS.renderAbc(targetId, JSON.parse($abcEl[0].textContent), params);
                            if (vis && vis[0]) {
                                $block.data('visualObj', vis[0]);
                                setMidiTune($block, vis[0], true);
                            }
                        } catch(e) {}
                    }
                }
            }
        });
    });

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

    // ── Edit setting: load form inline ───────────────────────────────────────

    $(document).on('click', '.edit-setting-btn', function () {
        var $btn      = $(this);
        var settingId = $btn.data('setting-id');
        var $block    = $btn.closest('.setting-block');
        var $editArea = $block.find('.setting-edit-area');

        if ($editArea.is(':visible')) {
            restoreNotation($block);
            $editArea.hide().empty();
            return;
        }

        // Close any other open edit forms
        $('.setting-edit-area:visible').each(function () {
            var $otherArea = $(this);
            var $otherBlock = $otherArea.closest('.setting-block');
            restoreNotation($otherBlock);
            $otherArea.hide().empty();
        });

        // Close add-setting form if open
        $('.add-setting-area').each(function () {
            if ($(this).children().length) {
                $(this).hide().empty();
            }
        });

        $editArea.html('<p class="edit-loading">Loading...</p>').show();

        $.get(base + '/api/edit-setting', { setting_id: settingId }, function (html) {
            $editArea.html(html);
            renderFromForm($block);
            $editArea.on('input change keyup', 'input, select, textarea', function () {
                renderFromForm($block);
            });
        });
    });

    // ── Cancel edit ──────────────────────────────────────────────────────────

    $(document).on('click', '.edit-cancel-btn', function () {
        var $block    = $(this).closest('.setting-block');
        var $editArea = $(this).closest('.setting-edit-area');
        restoreNotation($block);
        $editArea.hide().empty();
        loadMidiPlayer($block);
    });

    // ── Build ABC from form fields and render ────────────────────────────────

    function renderFromForm($block) {
        var $form = $block.find('.edit-setting-form');
        if (!$form.length) return;

        var settingId = $block.data('setting-id');
        var keyVal = $('#key').val() || $form.find('[name="key_signature"]').val() || '';
        var tempo = parseInt($form.closest('.setting-block').find('#playback-tempo').val()) || parseInt($block.data('tempo')) || 120;
        var abcString =
            'X:' + settingId + '\n' +
            'T:' + ($form.find('[name="tune_name"]').val()           || '') + '\n' +
            'M:' + ($form.find('[name="time_signature"]').val()      || '4/4') + '\n' +
            'L:' + ($form.find('[name="default_note_length"]').val() || '1/8') + '\n' +
            'Q:1/4=' + tempo + '\n' +
            'K:' + keyVal + '\n' +
            ($form.find('[name="abc_transcription"]').val() || '');

        renderNotation($block, abcString);
    }

    // ── Restore original notation ────────────────────────────────────────────

    function restoreNotation($block) {
        var $abcEl = $block.find('.setting-abc-data');
        if (!$abcEl.length) return;
        try {
            renderNotation($block, JSON.parse($abcEl[0].textContent));
        } catch (e) {}
    }

    // ── Tablature params ────────────────────────────────────────────────────

    function getTablatureParams() {
        var val = $('#tablature-instrument').val();
        if (!val) return {};
        var tab = window.tablaturePresets[val];
        if (!tab) return {};
        var entry = { instrument: tab.instrument };
        if (tab.tuning) entry.tuning = tab.tuning;
        if (tab.label) entry.label = tab.label;
        return { tablature: [entry], visualTranspose: 0 };
    }



    // ── Render ABC into the correct notation div ─────────────────────────────

    function getNotationTargetId($block) {
        return $block.hasClass('primary-setting')
            ? 'tune-notation'
            : 'setting-notation-' + $block.data('setting-id');
    }

    function renderNotation($block, abcString) {
        var targetId = getNotationTargetId($block);
        var params = getTablatureParams();
        params.add_classes = true;
        var visualObj = ABCJS.renderAbc(targetId, abcString, params);
        if (visualObj && visualObj[0]) {
            $block.data('visualObj', visualObj[0]);
            stopAllMidiPlayers();
            initMidiPlayer($block);
            var settingId = $block.data('setting-id');
            var sc = synthControllers[settingId];
            if (sc) {
                sc.setTune(visualObj[0], false).catch(function(e) {});
            }
        }
    }

    // ── MIDI player ─────────────────────────────────────────────────────────

    var synthControllers = {};

    function stopAllMidiPlayers(exceptId) {
        for (var id in synthControllers) {
            if (exceptId && id == exceptId) continue;
            var sc = synthControllers[id];
            try { sc.pause(); } catch(e) {}
            try { if (sc.midiBuffer) sc.midiBuffer.stop(); } catch(e) {}
            try { sc.isStarted = false; } catch(e) {}
            try { if (sc.control) sc.control.pushPlay(false); } catch(e) {}
        }
    }

    $(document).on('click', '.abcjs-midi-start', function () {
        var $block = $(this).closest('.setting-block');
        var settingId = $block.length ? $block.data('setting-id') : null;
        stopAllMidiPlayers(settingId);
    });

    function CursorControl(notationId) {
        var self = this;
        self.notationId = notationId;

        self.onStart = function () {
            var svg = document.querySelector('#' + self.notationId + ' svg');
            if (!svg) return;
            var cursor = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            cursor.setAttribute('class', 'abcjs-cursor');
            cursor.setAttributeNS(null, 'x1', 0);
            cursor.setAttributeNS(null, 'y1', 0);
            cursor.setAttributeNS(null, 'x2', 0);
            cursor.setAttributeNS(null, 'y2', 0);
            svg.appendChild(cursor);
        };

        self.onEvent = function (ev) {
            if (ev.measureStart && ev.left === null) return;

            var lastSelection = document.querySelectorAll('svg .highlight');
            for (var k = 0; k < lastSelection.length; k++)
                lastSelection[k].classList.remove('highlight');

            var showHighlight = document.getElementById('playback-highlight');
            if (!showHighlight || showHighlight.checked) {
                for (var i = 0; i < ev.elements.length; i++) {
                    var note = ev.elements[i];
                    for (var j = 0; j < note.length; j++) {
                        note[j].classList.add('highlight');
                    }
                }
            }

            var showCursor = document.getElementById('playback-cursor');
            var cursor = document.querySelector('#' + self.notationId + ' svg .abcjs-cursor');
            if (cursor) {
                if (!showCursor || showCursor.checked) {
                    cursor.setAttribute('x1', ev.left - 2);
                    cursor.setAttribute('x2', ev.left - 2);
                    cursor.setAttribute('y1', ev.top);
                    cursor.setAttribute('y2', ev.top + ev.height);
                } else {
                    cursor.setAttribute('x1', 0);
                    cursor.setAttribute('x2', 0);
                    cursor.setAttribute('y1', 0);
                    cursor.setAttribute('y2', 0);
                }
            }
        };

        self.onFinished = function () {
            var els = document.querySelectorAll('svg .highlight');
            for (var i = 0; i < els.length; i++)
                els[i].classList.remove('highlight');
            var cursor = document.querySelector('#' + self.notationId + ' svg .abcjs-cursor');
            if (cursor) {
                cursor.setAttribute('x1', 0);
                cursor.setAttribute('x2', 0);
                cursor.setAttribute('y1', 0);
                cursor.setAttribute('y2', 0);
            }
        };
    }

    function initMidiPlayer($block) {
        var settingId = $block.data('setting-id');
        var playerId = 'midi-player-' + settingId;
        var $player = $('#' + playerId);
        if (!$player.length) return;

        if (synthControllers[settingId]) {
            var old = synthControllers[settingId];
            try { old.pause(); } catch(e) {}
            try { if (old.midiBuffer) old.midiBuffer.stop(); } catch(e) {}
            try { old.isStarted = false; } catch(e) {}
            try { if (old.control) old.control.pushPlay(false); } catch(e) {}
            delete synthControllers[settingId];
            $player.empty();
        }

        var notationId = getNotationTargetId($block);
        var cursor = new CursorControl(notationId);
        var synthControl = new ABCJS.synth.SynthController();
        synthControl.load('#' + playerId, cursor, {
            displayLoop: true,
            displayRestart: true,
            displayPlay: true,
            displayProgress: true,
            displayWarp: true
        });
        synthControllers[settingId] = synthControl;
    }

    function setMidiTune($block, visualObj, reinit) {
        var settingId = $block.data('setting-id');
        if (!visualObj) return;

        if (reinit) {
            initMidiPlayer($block);
        }

        var synthControl = synthControllers[settingId];
        if (!synthControl) return;

        synthControl.setTune(visualObj, false).then(function () {
        }).catch(function (err) {
            console.warn('MIDI load error:', err);
        });
    }

    function loadMidiPlayer($block) {
        var visualObj = $block.data('visualObj');
        if (visualObj) {
            setMidiTune($block, visualObj);
        }
    }

    window.initAllMidiPlayers = function() {
        $('.setting-block').each(function () {
            initMidiPlayer($(this));
        });
    };

    window.setMidiTune = function($block, visualObj) {
        setMidiTune($block, visualObj);
    };


    // ── Save edit ────────────────────────────────────────────────────────────

    $(document).on('submit', '.edit-setting-form', function (e) {
        e.preventDefault();
        var $form     = $(this);
        var settingId = $form.data('setting-id');
        var $block    = $form.closest('.setting-block');

        $.post(base + '/api/edit-setting', $form.serialize(), function (data) {
            if (data.error) {
                $form.find('.edit-error').text(data.error);
                return;
            }

            var s = data.setting;

            $block.find('.setting-key').text(s.key_signature);
            $block.find('.setting-time').text(s.time_signature);

            var savedTempo = parseInt(s.tempo) || parseInt($block.data('tempo')) || 120;
            $block.data('tempo', savedTempo);
            var newAbc =
                'X:' + s.setting_id + '\n' +
                'T:' + s.tune_name  + '\n' +
                'M:' + s.time_signature + '\n' +
                'L:' + s.default_note_length + '\n' +
                'Q:1/4=' + savedTempo + '\n' +
                'K:' + s.key_signature + '\n' +
                s.abc_transcription;

            $block.find('.setting-abc-data').text(JSON.stringify(newAbc));

            var isPrimary = $block.hasClass('primary-setting');
            var saveParams = getTablatureParams();
            if (isPrimary) {
                ABCJS.renderAbc('tune-notation', newAbc, saveParams);
            } else {
                var notationId = 'setting-notation-' + s.setting_id;
                ABCJS.renderAbc(notationId, newAbc, saveParams);
            }

            $block.find('.setting-edit-area').hide().empty();
            loadMidiPlayer($block);
        }, 'json');
    });

    // ── Add setting: load form ──────────────────────────────────────────────

    $(document).on('click', '.add-setting-btn', function () {
        var $btn     = $(this);
        var tuneId   = $btn.data('tune-id');
        var $area    = $btn.siblings('.add-setting-area');

        if ($area.is(':visible')) {
            $area.hide().empty();
            return;
        }

        // Close any open edit forms
        $('.setting-edit-area:visible').each(function () {
            var $otherArea = $(this);
            var $otherBlock = $otherArea.closest('.setting-block');
            restoreNotation($otherBlock);
            $otherArea.hide().empty();
        });

        $area.html('<p class="edit-loading">Loading...</p>').show();

        $.get(base + '/api/add-setting-form', { tune_id: tuneId }, function (html) {
            $area.html(html);
            // Init knob dials
            $area.find('.playback-dial').knob({
                'release': function(v) {}
            });
            // Live notation preview
            $area.on('input change keyup', 'input, select, textarea', function () {
                var $form = $area.find('.add-setting-form');
                if (!$form.length) return;
                var keyVal = $area.find('#key').val() || '';
                var abcString =
                    'X:1\n' +
                    'M:' + ($form.find('[name="metre"]').val() || '4/4') + '\n' +
                    'L:1/8\n' +
                    'K:' + keyVal + '\n' +
                    ($form.find('[name="tune_body"]').val() || '');
                var $preview = $area.find('.add-setting-preview');
                if (!$preview.length) {
                    $preview = $('<div class="add-setting-preview"></div>');
                    $form.before($preview);
                }
                try {
                    ABCJS.renderAbc($preview[0], abcString, getTablatureParams());
                } catch(e) {}
            });
        });
    });

    // ── Add setting: cancel ─────────────────────────────────────────────────

    $(document).on('click', '.add-setting-cancel-btn', function () {
        var $area = $(this).closest('.add-setting-area');
        $area.hide().empty();
    });

    // ── Add setting: submit ─────────────────────────────────────────────────

    $(document).on('submit', '.add-setting-form', function (e) {
        e.preventDefault();
        var $form  = $(this);
        var tuneId = $form.data('tune-id');

        var postData = {
            tune_id:  tuneId,
            metre:    $form.find('[name="metre"]').val(),
            tune_key: $form.find('[name="tune_key"]').val(),
            tune_body: $form.find('[name="tune_body"]').val(),
            tempo:    $form.find('#playback-tempo').val()
        };

        $.post(base + '/api/add-setting', postData, function (data) {
            if (data.error) {
                $form.find('.edit-error').text(data.error);
                return;
            }
            // Reload the tune page to show the new setting
            var $panel = $form.closest('.ui-tabs-panel');
            if ($panel.length) {
                window.openTuneInPanel(tuneId, $panel);
            } else {
                location.reload();
            }
        }, 'json');
    });

    // ── Vote ─────────────────────────────────────────────────────────────────

    $(document).on('click', '.vote-btn', function () {
        var $btn       = $(this);
        var settingId  = parseInt($btn.data('setting-id'));
        var voteValue  = $btn.hasClass('vote-up') ? 1 : -1;
        var userId     = parseInt($('#tune-page').data('user-id') || $('#user-info').data('user-id') || 0);

        if (!userId) {
            alert('Please log in to vote.');
            return;
        }

        $.post(base + '/api/vote-setting', {
            setting_id: settingId,
            vote_value: voteValue
        }, function (data) {
            if (data.error) return;

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

        var $blocks = $container.find('.setting-block').detach();
        $blocks.sort(function (a, b) {
            return parseInt($(b).data('vote-score')) - parseInt($(a).data('vote-score'));
        });

        var newPrimaryId = parseInt($($blocks[0]).data('setting-id'));
        var primaryChanged = (newPrimaryId !== currentPrimaryId);

        $blocks.each(function (i) {
            $(this).find('.setting-label').text('Setting ' + (i + 1));
            $container.append(this);
        });

        if (primaryChanged) {
            var $newPrimary = $container.find('.setting-block[data-setting-id="' + newPrimaryId + '"]');
            var $oldPrimary = $container.find('.setting-block[data-setting-id="' + currentPrimaryId + '"]');

            var reorderParams = getTablatureParams();
            var $newAbc = $newPrimary.find('.setting-abc-data');
            if ($newAbc.length) {
                try {
                    ABCJS.renderAbc('tune-notation', JSON.parse($newAbc[0].textContent), reorderParams);
                } catch (e) {}
            }

            var $oldNotDiv = $oldPrimary.find('.setting-notation');
            if ($oldNotDiv.length) {
                var $oldAbc = $oldPrimary.find('.setting-abc-data');
                if ($oldAbc.length) {
                    try {
                        ABCJS.renderAbc($oldNotDiv.attr('id'), JSON.parse($oldAbc[0].textContent), reorderParams);
                    } catch (e) {}
                }
                $oldNotDiv.show();
            }

            $newPrimary.find('.setting-notation').hide();

            $oldPrimary.removeClass('primary-setting');
            $newPrimary.addClass('primary-setting');
            $container.data('primary-setting-id', newPrimaryId);
        }
    }

});
