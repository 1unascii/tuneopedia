<?php

use App\Models\Album;
use App\Models\User;

test('any authenticated user can view albums', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Album::class))->toBeTrue();
});

test('any authenticated user can view a specific album', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create();

    expect($user->can('view', $album))->toBeTrue();
});

test('any authenticated user can create an album', function () {
    $user = User::factory()->create();

    expect($user->can('create', Album::class))->toBeTrue();
});

test('album owner can update their album', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    expect($user->can('update', $album))->toBeTrue();
});

test('non-owner cannot update an album', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('update', $album))->toBeFalse();
});

test('album owner can delete their album', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    expect($user->can('delete', $album))->toBeTrue();
});

test('admin can delete any album', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $album = Album::factory()->create(['user_id' => User::factory()->create()->id]);

    expect($admin->can('delete', $album))->toBeTrue();
});

test('non-owner cannot delete an album', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('delete', $album))->toBeFalse();
});

test('album owner can add tracks', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    expect($user->can('addTrack', $album))->toBeTrue();
});

test('non-owner cannot add tracks', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('addTrack', $album))->toBeFalse();
});

test('album owner can force delete their album', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    expect($user->can('forceDelete', $album))->toBeTrue();
});

test('admin can force delete any album', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $album = Album::factory()->create(['user_id' => User::factory()->create()->id]);

    expect($admin->can('forceDelete', $album))->toBeTrue();
});

test('non-owner cannot force delete an album', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('forceDelete', $album))->toBeFalse();
});
