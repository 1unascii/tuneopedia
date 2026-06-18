<?php
require_once __DIR__ . '/test_helper.php';

$pdo = connect();
$user1 = create_test_user($pdo, '_disc1');
$user2 = create_test_user($pdo, '_disc2');
$userId1 = $user1['user_id'];
$userId2 = $user2['user_id'];

// ── Create Thread ───────────────────────────────────────────────────────────
log_section('Create Thread');
$threadTitle = 'Test Thread ' . $timestamp;
$threadBody = 'This is the opening post body.';
log_data('Thread data', ['user_id' => $userId1, 'title' => $threadTitle, 'body' => $threadBody]);
$threadId = Discussion::createThread($pdo, $userId1, $threadTitle, $threadBody);
log_data('Created thread_id', $threadId);
assert_greater_than('Thread created with valid ID', 0, $threadId);

// ── Show (findThreadById) ───────────────────────────────────────────────────
log_section('Show (findThreadById)');
$thread = Discussion::findThreadById($pdo, $threadId);
log_data('findThreadById result', $thread);
assert_not_null('findThreadById returns thread', $thread);
assert_equals('Thread title matches', $threadTitle, $thread['title']);
assert_equals('Thread user_id matches', $userId1, (int)$thread['user_id']);
assert_equals('Thread user_name matches', $user1['user_name'], $thread['user_name']);

$noThread = Discussion::findThreadById($pdo, 999999);
assert_null('findThreadById returns null for nonexistent', $noThread);

// ── Show (getPostsByThreadId) — opening post ────────────────────────────────
log_section('Show (getPostsByThreadId) — opening post');
$posts = Discussion::getPostsByThreadId($pdo, $threadId);
log_data('Posts', $posts);
assert_equals('Thread has 1 post (opening post via createPost)', 1, count($posts));
assert_equals('Opening post body matches', $threadBody, $posts[0]['body']);
assert_equals('Opening post user matches thread creator', $userId1, (int)$posts[0]['user_id']);

// ── Create Post (reply) ─────────────────────────────────────────────────────
log_section('Create Post (reply)');
$replyBody = 'This is a reply from user 2.';
log_data('Reply data', ['thread_id' => $threadId, 'user_id' => $userId2, 'body' => $replyBody]);
$postId = Discussion::createPost($pdo, $threadId, $userId2, $replyBody);
log_data('Created post_id', $postId);
assert_greater_than('Post created with valid ID', 0, $postId);

$postsAfter = Discussion::getPostsByThreadId($pdo, $threadId);
log_data('Posts after reply', ['count' => count($postsAfter), 'last_author' => $postsAfter[1]['user_name'], 'last_body' => $postsAfter[1]['body']]);
assert_equals('Thread now has 2 posts', 2, count($postsAfter));
assert_equals('Second post is from user 2', $userId2, (int)$postsAfter[1]['user_id']);
assert_equals('Second post body matches', $replyBody, $postsAfter[1]['body']);

// ── Create second thread ────────────────────────────────────────────────────
log_section('Create second thread');
$thread2Title = 'Second Thread ' . $timestamp;
log_data('Second thread', ['user_id' => $userId2, 'title' => $thread2Title]);
$threadId2 = Discussion::createThread($pdo, $userId2, $thread2Title, 'Another discussion.');
log_data('Created thread_id', $threadId2);
assert_greater_than('Second thread created', 0, $threadId2);

// ── Index (getAllThreads) ───────────────────────────────────────────────────
log_section('Index (getAllThreads)');
$allThreads = Discussion::getAllThreads($pdo);
log_data('Total threads returned', count($allThreads));

$foundThread1 = false;
$foundThread2 = false;
foreach ($allThreads as $t) {
    if ((int)$t['discussion_thread_id'] === $threadId) {
        $foundThread1 = true;
        log_data('Thread 1 in index', ['id' => $t['discussion_thread_id'], 'title' => $t['title'], 'post_count' => $t['post_count']]);
        assert_equals('Thread 1 post count is 2', 2, (int)$t['post_count']);
    }
    if ((int)$t['discussion_thread_id'] === $threadId2) {
        $foundThread2 = true;
        log_data('Thread 2 in index', ['id' => $t['discussion_thread_id'], 'title' => $t['title'], 'post_count' => $t['post_count']]);
        assert_equals('Thread 2 post count is 1', 1, (int)$t['post_count']);
    }
}
assert_true('Thread 1 found in getAllThreads', $foundThread1);
assert_true('Thread 2 found in getAllThreads', $foundThread2);

// ── Delete Post ─────────────────────────────────────────────────────────────
log_section('Delete Post');
$openingPostId = (int)$posts[0]['post_id'];
log_data('User 2 tries to delete user 1\'s post', ['post_id' => $openingPostId, 'acting_user_id' => $userId2]);
$wrongDelete = Discussion::deletePost($pdo, $openingPostId, $userId2);
assert_true('Cannot delete another user\'s post', !$wrongDelete);

log_data('User 2 deletes own post', ['post_id' => $postId, 'acting_user_id' => $userId2]);
$rightDelete = Discussion::deletePost($pdo, $postId, $userId2);
assert_true('Can delete own post', $rightDelete);

$postsAfterDelete = Discussion::getPostsByThreadId($pdo, $threadId);
log_data('Posts after delete', ['count' => count($postsAfterDelete)]);
assert_equals('Thread has 1 post after deleting reply', 1, count($postsAfterDelete));

// ── Delete Thread ───────────────────────────────────────────────────────────
log_section('Delete Thread');
log_data('User 2 tries to delete user 1\'s thread', ['thread_id' => $threadId, 'acting_user_id' => $userId2]);
$wrongThreadDelete = Discussion::deleteThread($pdo, $threadId, $userId2);
assert_true('Cannot delete another user\'s thread', !$wrongThreadDelete);

log_data('User 1 deletes own thread', ['thread_id' => $threadId, 'acting_user_id' => $userId1]);
$rightThreadDelete = Discussion::deleteThread($pdo, $threadId, $userId1);
assert_true('Can delete own thread', $rightThreadDelete);

$deletedThread = Discussion::findThreadById($pdo, $threadId);
assert_null('Thread no longer exists after delete', $deletedThread);

$remainingPosts = Discussion::getPostsByThreadId($pdo, $threadId);
assert_equals('No posts remain after thread delete', 0, count($remainingPosts));

// ── Cleanup ─────────────────────────────────────────────────────────────────
Discussion::deleteThread($pdo, $threadId2, $userId2);
cleanup_test_user($pdo, $userId1);
cleanup_test_user($pdo, $userId2);
print_results('Discussion Model Tests');
