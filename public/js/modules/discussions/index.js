$(function () {

    var base = (typeof APP_BASE !== 'undefined') ? APP_BASE : '';
    var apiBase = base + '/';

    // ── Toggle new thread form ───────────────────────────────────────────────

    $(document).on('click', '#new-thread-btn', function () {
        $('#new-thread-form').slideToggle();
    });

    $(document).on('click', '#cancel-thread-btn', function () {
        $('#new-thread-form').slideUp();
        $('#thread-title-input').val('');
        $('#thread-body-input').val('');
    });

    // ── Submit new thread ────────────────────────────────────────────────────

    $(document).on('click', '#submit-thread-btn', function () {
        var title = $('#thread-title-input').val().trim();
        var body  = $('#thread-body-input').val().trim();

        if (!title || !body) {
            alert('Please fill in both the title and body.');
            return;
        }

        $.post(apiBase + 'api/threads', { title: title, body: body }, function (response) {
            var result = (typeof response === 'string') ? JSON.parse(response) : response;
            if (result.success) {
                $('#discussion-container').load(apiBase + 'page/discussions #discussion-container > *');
            } else {
                alert(result.error || 'Could not create thread.');
            }
        }).fail(function (xhr) {
            var errorResponse = xhr.responseJSON || {};
            alert(errorResponse.error || 'Could not create thread.');
        });
    });

    // ── Navigate to thread detail (inline, like tunes) ───────────────────────

    $(document).on('click', '.discussion-thread-row', function () {
        var threadId = $(this).data('thread-id');
        var $container = $('#discussion-container');

        // Save the current list HTML so the back button can restore it
        $container.data('threadListState', $container.html());

        history.pushState({ src: 'page/discussion-thread?thread_id=' + threadId, urlPath: 'discussions' }, '', base + '/discussions/' + threadId);

        $container.load(apiBase + 'page/discussion-thread?thread_id=' + threadId, function () {
            // The loaded content has its own #discussion-container wrapper,
            // so unwrap it to avoid nesting
            var $inner = $container.find('#discussion-container');
            if ($inner.length) {
                $container.html($inner.html());
            }
        });
    });

    // ── Back to thread list ──────────────────────────────────────────────────

    $(document).on('click', '#back-to-threads-btn', function () {
        var $container = $('#discussion-container');
        var savedHtml = $container.data('threadListState');
        if (savedHtml) {
            $container.html(savedHtml);
            $container.removeData('threadListState');
        } else {
            $container.load(apiBase + 'page/discussions #discussion-container > *');
        }
        history.pushState({ src: 'page/discussions', urlPath: 'discussions' }, '', base + '/discussions');
    });

});
