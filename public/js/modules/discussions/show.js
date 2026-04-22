$(function () {

    var base = (typeof APP_BASE !== 'undefined') ? APP_BASE : '';
    var apiBase = base + '/';

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
                $('#content').load(apiBase + 'page/discussions');
                history.pushState({ src: 'page/discussions' }, '', base + '/discussions');
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
