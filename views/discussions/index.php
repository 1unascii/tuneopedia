<div id="discussion-container">
    <div id="discussion-header">
        <h2>Discussion</h2>
<?php if (!empty($_SESSION['Authenticated'])): ?>
        <button id="new-thread-btn" class="ui-button ui-widget ui-corner-all">New Thread</button>
<?php endif; ?>
    </div>

<?php if (!empty($_SESSION['Authenticated'])): ?>
    <div id="new-thread-form" style="display: none;">
        <h3>Start a New Discussion</h3>
        <div class="form-group">
            <label for="thread-title-input">Title</label>
            <input type="text" id="thread-title-input" maxlength="255" placeholder="What do you want to discuss?" />
        </div>
        <div class="form-group">
            <label for="thread-body-input">Body</label>
            <textarea id="thread-body-input" rows="5" placeholder="Share your thoughts..."></textarea>
        </div>
        <div class="form-actions">
            <button id="submit-thread-btn" class="ui-button ui-widget ui-corner-all">Post</button>
            <button id="cancel-thread-btn" class="ui-button ui-widget ui-corner-all">Cancel</button>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($threads)): ?>
    <?php if (empty($_SESSION['Authenticated'])): ?>
    <p class="discussion-empty"><a href="#" onclick="$('#login_link').click(); return false;">Log in</a> to start a discussion.</p>
    <?php else: ?>
    <p class="discussion-empty">No discussions yet. Be the first to start one!</p>
    <?php endif; ?>
<?php else: ?>
    <table id="discussion-thread-list">
        <thead class="ui-state-default">
            <tr>
                <th>Topic</th>
                <th>Started By</th>
                <th>Posts</th>
                <th>Last Activity</th>
            </tr>
        </thead>
        <tbody class="ui-state-default">
    <?php foreach ($threads as $thread): ?>
            <tr class="discussion-thread-row" data-thread-id="<?= $thread['discussion_thread_id'] ?>">
                <td>
                    <span class="thread-title-link"><?= htmlspecialchars($thread['title']) ?></span>
                </td>
                <td><?= htmlspecialchars($thread['user_name']) ?></td>
                <td class="discussion-post-count"><?= (int) $thread['post_count'] ?></td>
                <td class="discussion-last-activity">
                    <?= date('M j, Y g:ia', strtotime($thread['last_post_at'] ?? $thread['created_at'])) ?>
                </td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</div>
