<?php

use App\Models\Setting;
use App\Models\Tune;
use App\Models\TuneType;
use Illuminate\Support\Facades\Artisan;

test('tunes index page displays tune types with tunes that have settings', function () {
    $tuneType = TuneType::factory()->create(['name' => 'Reel']);
    $tune = Tune::factory()->for($tuneType)->create(['name' => 'Drowsy Maggie']);
    Setting::factory()->for($tune)->create();

    $response = $this->get('/tunes');

    $response->assertStatus(200);
    $response->assertSeeText('Reel');
    $response->assertSeeText('Drowsy Maggie');
});

test('tunes index page hides empty tune types', function () {
    TuneType::factory()->create(['name' => 'Reel']);

    $response = $this->get('/tunes');

    $response->assertStatus(200);
    $response->assertDontSeeText('Reel');
});

test('tunes index page hides tunes with no settings', function () {
    $tuneType = TuneType::factory()->create(['name' => 'Jig']);
    $tuneWithSetting = Tune::factory()->for($tuneType)->create(['name' => 'Kesh Jig']);
    Setting::factory()->for($tuneWithSetting)->create();
    Tune::factory()->for($tuneType)->create(['name' => 'Orphan Tune']);

    $response = $this->get('/tunes');

    $response->assertStatus(200);
    $response->assertSeeText('Kesh Jig');
    $response->assertDontSeeText('Orphan Tune');
});

test('every tune in the database has at least one setting', function () {
    $tunesWithoutSettings = Tune::doesntHave('settings')->count();

    expect($tunesWithoutSettings)->toBe(0);
});

test('seeded tunes all have at least one setting', function () {
    Artisan::call('db:seed', ['--class' => 'TuneSeeder', '--no-interaction' => true]);

    $tunesWithoutSettings = Tune::doesntHave('settings')->count();

    expect($tunesWithoutSettings)->toBe(0);
});

test('tune show page displays tune details', function () {
    $tune = Tune::factory()->create(['name' => 'Morrison Jig']);

    $response = $this->get("/tunes/{$tune->id}");

    $response->assertStatus(200);
    $response->assertSeeText('Morrison Jig');
});

test('tune show page displays settings ordered by votes', function () {
    $tune = Tune::factory()->create();
    $topSetting = Setting::factory()->for($tune)->create(['name' => 'Top Setting']);
    $lowSetting = Setting::factory()->for($tune)->create(['name' => 'Low Setting']);

    // Give topSetting more votes
    \App\Models\SettingVote::factory()->for($topSetting)->create(['vote_value' => 1]);
    \App\Models\SettingVote::factory()->for($topSetting)->create(['vote_value' => 1]);
    \App\Models\SettingVote::factory()->for($lowSetting)->create(['vote_value' => -1]);

    $response = $this->get("/tunes/{$tune->id}");

    $response->assertStatus(200);
    $response->assertSeeTextInOrder(['Top Setting', 'Low Setting']);
});

test('guests cannot access create tune page', function () {
    $response = $this->get('/tunes/create');

    $response->assertRedirect('/login');
});

test('authenticated user can access create tune page', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->get('/tunes/create');

    $response->assertStatus(200);
});

test('authenticated user can store a new tune', function () {
    $user = \App\Models\User::factory()->create();
    $tuneType = TuneType::factory()->create(['name' => 'Reel']);

    $response = $this->actingAs($user)->post('/tunes', [
        'name' => 'Drowsy Maggie',
        'tune_type' => 'Reel',
        'time_signature' => '4/4',
        'default_note_length' => '1/8',
        'key_signature' => 'D',
        'abc_transcription' => 'ABCD|EFGA|',
    ]);

    $this->assertDatabaseHas('tunes', ['name' => 'Drowsy Maggie']);
    $this->assertDatabaseHas('settings', ['name' => 'Drowsy Maggie', 'user_id' => $user->id]);
});

test('storing a tune requires a name', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->post('/tunes', [
        'tune_type' => 'Reel',
        'time_signature' => '4/4',
        'default_note_length' => '1/8',
        'key_signature' => 'D',
        'abc_transcription' => 'ABCD|',
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('storing a tune requires abc_transcription', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->post('/tunes', [
        'name' => 'Test Tune',
        'tune_type' => 'Reel',
        'time_signature' => '4/4',
        'default_note_length' => '1/8',
        'key_signature' => 'D',
    ]);

    $response->assertSessionHasErrors(['abc_transcription']);
});

test('guests cannot store a tune', function () {
    $response = $this->post('/tunes', [
        'name' => 'Test',
        'tune_type' => 'Reel',
        'time_signature' => '4/4',
        'default_note_length' => '1/8',
        'key_signature' => 'D',
        'abc_transcription' => 'ABCD|',
    ]);

    $response->assertRedirect('/login');
});

test('guests cannot delete a tune', function () {
    $tune = Tune::factory()->create();

    $response = $this->delete("/tunes/{$tune->id}");

    $response->assertRedirect('/login');
});

test('regular user cannot delete a tune that has settings', function () {
    $user = \App\Models\User::factory()->create();
    $tune = Tune::factory()->create();
    Setting::factory()->for($tune)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("/tunes/{$tune->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('tunes', ['id' => $tune->id]);
});

test('regular user can delete a tune with no settings', function () {
    $user = \App\Models\User::factory()->create();
    $tune = Tune::factory()->create();

    $response = $this->actingAs($user)->delete("/tunes/{$tune->id}");

    $response->assertRedirect(route('tunes.index'));
    $this->assertDatabaseMissing('tunes', ['id' => $tune->id]);
});

test('admin can delete a tune with settings and it deletes both', function () {
    $admin = \App\Models\User::factory()->create(['is_admin' => true]);
    $tune = Tune::factory()->create();
    $setting1 = Setting::factory()->for($tune)->create();
    $setting2 = Setting::factory()->for($tune)->create();

    $response = $this->actingAs($admin)->delete("/tunes/{$tune->id}");

    $response->assertRedirect(route('tunes.index'));
    $this->assertDatabaseMissing('tunes', ['id' => $tune->id]);
    $this->assertDatabaseMissing('settings', ['id' => $setting1->id]);
    $this->assertDatabaseMissing('settings', ['id' => $setting2->id]);
});
