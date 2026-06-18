<?php

use App\Models\Setting;
use App\Models\Tune;
use App\Models\User;

test('any user can view tunes', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Tune::class))->toBeTrue();
});

test('any user can view a specific tune', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();

    expect($user->can('view', $tune))->toBeTrue();
});

test('any authenticated user can create a tune', function () {
    $user = User::factory()->create();

    expect($user->can('create', Tune::class))->toBeTrue();
});

test('a tune with no settings can be deleted', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();

    expect($user->can('delete', $tune))->toBeTrue();
});

test('a tune with settings cannot be deleted', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();
    Setting::factory()->for($tune)->create();

    expect($user->can('delete', $tune))->toBeFalse();
});

test('a tune cannot be updated', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();

    expect($user->can('update', $tune))->toBeFalse();
});

test('a tune cannot be restored', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();

    expect($user->can('restore', $tune))->toBeFalse();
});

test('a tune cannot be force deleted', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();

    expect($user->can('forceDelete', $tune))->toBeFalse();
});
