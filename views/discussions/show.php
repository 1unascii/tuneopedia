<div id="discussion-container">
    <button id="back-to-threads-btn" class="ui-button ui-widget ui-corner-all">&#8592; Back to Discussions</button>

    <div id="thread-detail" data-thread-id="<?= $thread['discussion_thread_id'] ?>">
        <h2><?= htmlspecialchars($thread['title']) ?></h2>
        <div class="thread-meta">
            <span class="thread-author"><?= htmlspecialchars($thread['user_name']) ?></span>
            <span class="thread-date"><?= date('M j, Y g:ia', strtotime($thread['created_at'])) ?></span>
<?php if (!empty($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $thread['user_id']): ?>
            <span class="ui-icon ui-icon-trash delete-thread-btn" title="Delete thread"></span>
<?php endif; ?>
        </div>
    </div>

    <div id="thread-posts">
<?php foreach ($posts as $post): ?>
        <div class="post-card" data-post-id="<?= $post['post_id'] ?>">
            <div class="post-meta">
                <span class="post-author"><?= htmlspecialchars($post['user_name']) ?></span>
                <span class="post-date"><?= date('M j, Y g:ia', strtotime($post['created_at'])) ?></span>
<?php if (!empty($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $post['user_id']): ?>
                <span class="ui-icon ui-icon-trash delete-post-btn" title="Delete post"></span>
<?php endif; ?>
            </div>
            <div class="post-body"><?= nl2br(htmlspecialchars($post['body'])) ?></div>
        </div>
<?php endforeach; ?>
    </div>

<?php if (!empty($_SESSION['Authenticated'])): ?>
    <div id="reply-form">
        <h3>Post a Reply</h3>
        <div class="form-group">
            <textarea id="reply-body-input" rows="4" placeholder="Write your reply..."></textarea>
        </div>
        <div class="form-actions">
            <button id="submit-reply-btn" class="ui-button ui-widget ui-corner-all">Reply</button>
        </div>
    </div>
<?php else: ?>
    <p class="discussion-login-prompt">Log in to join the discussion.</p>
<?php endif; ?>
</div>
