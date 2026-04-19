$(function () {

    var base = (typeof APP_BASE !== 'undefined') ? APP_BASE : '';
    var apiBase = base + '/';

    function loadDiscussionContent(src, urlPath) {
        $('#content').load(apiBase + src);
        history.pushState({ src: src }, '', base + '/' + urlPath);
    }

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

        $.post(apiBase + 'api/create-thread', { title: title, body: body }, function (response) {
            var result = (typeof response === 'string') ? JSON.parse(response) : response;
            if (result.success) {
                loadDiscussionContent('page/discussion', 'discussion');
            } else {
                alert(result.error || 'Could not create thread.');
            }
        }).fail(function (xhr) {
            var errorResponse = xhr.responseJSON || {};
            alert(errorResponse.error || 'Could not create thread.');
        });
    });

    // ── Navigate to thread detail ────────────────────────────────────────────

    $(document).on('click', '.discussion-thread-row', function () {
        var threadId = $(this).data('thread-id');
        loadDiscussionContent(
            'page/discussion-thread?thread_id=' + threadId,
            'discussion/' + threadId
        );
    });

    // ── Back to thread list ──────────────────────────────────────────────────

    $(document).on('click', '#back-to-threads-btn', function () {
        history.back();
    });

    // ── Submit reply ─────────────────────────────────────────────────────────

    $(document).on('click', '#submit-reply-btn', function () {
        var threadId = $('#thread-detail').data('thread-id');
        var body     = $('#reply-body-input').val().trim();

        if (!body) {
            alert('Please write a reply.');
            return;
        }

        $.post(apiBase + 'api/create-post', { thread_id: threadId, body: body }, function (response) {
            var result = (typeof response === 'string') ? JSON.parse(response) : response;
            if (result.success) {
                $('#content').load(apiBase + 'page/discussion-thread?thread_id=' + threadId);
            } else {
                alert(result.error || 'Could not post reply.');
            }
        }).fail(function (xhr) {
            var errorResponse = xhr.responseJSON || {};
            alert(errorResponse.error || 'Could not post reply.');
        });
    });

    // ── Delete thread ────────────────────────────────────────────────────────

    $(document).on('click', '.delete-thread-btn', function (event) {
        event.stopPropagation();
        if (!confirm('Are you sure you want to delete this thread and all its replies?')) {
            return;
        }

        var threadId = $('#thread-detail').data('thread-id');

        $.post(apiBase + 'api/delete-thread', { thread_id: threadId }, function (response) {
            var result = (typeof response === 'string') ? JSON.parse(response) : response;
            if (result.success) {
                loadDiscussionContent('page/discussion', 'discussion');
            } else {
                alert(result.error || 'Could not delete thread.');
            }
        }).fail(function (xhr) {
            var errorResponse = xhr.responseJSON || {};
            alert(errorResponse.error || 'Could not delete thread.');
        });
    });

    // ── Delete post ──────────────────────────────────────────────────────────

    $(document).on('click', '.delete-post-btn', function (event) {
        event.stopPropagation();
        if (!confirm('Are you sure you want to delete this post?')) {
            return;
        }

        var postId   = $(this).closest('.post-card').data('post-id');
        var threadId = $('#thread-detail').data('thread-id');

        $.post(apiBase + 'api/delete-post', { post_id: postId }, function (response) {
            var result = (typeof response === 'string') ? JSON.parse(response) : response;
            if (result.success) {
                $('#content').load(apiBase + 'page/discussion-thread?thread_id=' + threadId);
            } else {
                alert(result.error || 'Could not delete post.');
            }
        }).fail(function (xhr) {
            var errorResponse = xhr.responseJSON || {};
            alert(errorResponse.error || 'Could not delete post.');
        });
    });

});
