<?php

use App\Models\Setting;
use App\Models\User;

test('any user can view settings', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Setting::class))->toBeTrue();
});

test('any user can view a specific setting', function () {
    $user = User::factory()->create();
    $setting = Setting::factory()->create();

    expect($user->can('view', $setting))->toBeTrue();
});

test('any authenticated user can create a setting', function () {
    $user = User::factory()->create();

    expect($user->can('create', Setting::class))->toBeTrue();
});

test('setting owner can update their setting', function () {
    $user = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $user->id]);

    expect($user->can('update', $setting))->toBeTrue();
});

test('non-owner cannot update a setting', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('update', $setting))->toBeFalse();
});

test('setting owner can delete their setting', function () {
    $user = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $user->id]);

    expect($user->can('delete', $setting))->toBeTrue();
});

test('non-owner cannot delete a setting', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('delete', $setting))->toBeFalse();
});

test('setting owner can restore their setting', function () {
    $user = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $user->id]);

    expect($user->can('restore', $setting))->toBeTrue();
});

test('non-owner cannot restore a setting', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $owner->id]);

    expect($otherUser->can('restore', $setting))->toBeFalse();
});

test('no one can force delete a setting', function () {
    $user = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $user->id]);

    expect($user->can('forceDelete', $setting))->toBeFalse();
});
