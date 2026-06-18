<?php

use App\Models\DiscussionThread;
use App\Models\User;

test('any user can view discussion threads', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', DiscussionThread::class))->toBeTrue();
});

test('any user can view a specific thread', function () {
    $user = User::factory()->create();
    $thread = DiscussionThread::factory()->create();

    expect($user->can('view', $thread))->toBeTrue();
});

test('any authenticated user can create a thread', function () {
    $user = User::factory()->create();

    expect($user->can('create', DiscussionThread::class))->toBeTrue();
});

test('thread owner can update their own thread', function () {
    $user = User::factory()->create();
    $thread = DiscussionThread::factory()->for($user)->create();

    expect($user->can('update', $thread))->toBeTrue();
});

test('non-owner cannot update a thread', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $thread = DiscussionThread::factory()->for($owner)->create();

    expect($otherUser->can('update', $thread))->toBeFalse();
});

test('thread owner can delete their own thread', function () {
    $user = User::factory()->create();
    $thread = DiscussionThread::factory()->for($user)->create();

    expect($user->can('delete', $thread))->toBeTrue();
});

test('non-owner cannot delete a thread', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $thread = DiscussionThread::factory()->for($owner)->create();

    expect($otherUser->can('delete', $thread))->toBeFalse();
});

test('thread owner can restore their own thread', function () {
    $user = User::factory()->create();
    $thread = DiscussionThread::factory()->for($user)->create();

    expect($user->can('restore', $thread))->toBeTrue();
});

test('non-owner cannot restore a thread', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $thread = DiscussionThread::factory()->for($owner)->create();

    expect($otherUser->can('restore', $thread))->toBeFalse();
});
