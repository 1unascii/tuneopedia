<?php

use App\Models\Setting;
use App\Models\Tune;
use App\Models\User;

test('setting show page displays setting details', function () {
    $setting = Setting::factory()->create([
        'name' => 'Drowsy Maggie Setting 1',
        'key_signature' => 'D',
        'time_signature' => '4/4',
    ]);

    $response = $this->get("/settings/{$setting->id}");

    $response->assertStatus(200);
    $response->assertSeeText('Drowsy Maggie Setting 1');
});

test('setting show page loads tune and tune type relationships', function () {
    $setting = Setting::factory()->create();
    $setting->load('tune.tuneType');

    $response = $this->get("/settings/{$setting->id}");

    $response->assertStatus(200);
    $response->assertSeeText($setting->tune->tuneType->name);
});

test('guests cannot access create setting page', function () {
    $tune = Tune::factory()->create();

    $response = $this->get("/tunes/{$tune->id}/settings/create");

    $response->assertRedirect('/login');
});

test('authenticated user can access create setting page', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();

    $response = $this->actingAs($user)->get("/tunes/{$tune->id}/settings/create");

    $response->assertStatus(200);
});

test('authenticated user can store a setting', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();

    $response = $this->actingAs($user)->post("/tunes/{$tune->id}/settings", [
        'name' => 'New Setting',
        'time_signature' => '6/8',
        'default_note_length' => '1/8',
        'key_signature' => 'G',
        'abc_transcription' => 'GABc|defg|',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('settings', [
        'tune_id' => $tune->id,
        'name' => 'New Setting',
        'user_id' => $user->id,
    ]);
});

test('setting owner can access edit page', function () {
    $user = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get("/settings/{$setting->id}/edit");

    $response->assertStatus(200);
});

test('non-owner cannot access edit page', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($otherUser)->get("/settings/{$setting->id}/edit");

    $response->assertStatus(403);
});

test('setting owner can update their setting', function () {
    $user = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put("/settings/{$setting->id}", [
        'name' => 'Updated Setting',
        'time_signature' => '4/4',
        'default_note_length' => '1/8',
        'key_signature' => 'D',
        'abc_transcription' => 'dcBA|GFED|',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('settings', [
        'id' => $setting->id,
        'name' => 'Updated Setting',
    ]);
});

test('non-owner cannot update a setting', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($otherUser)->put("/settings/{$setting->id}", [
        'name' => 'Hacked',
        'time_signature' => '4/4',
        'default_note_length' => '1/8',
        'key_signature' => 'D',
        'abc_transcription' => 'ABCD|',
    ]);

    $response->assertStatus(403);
});

test('setting owner can delete their setting', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();
    Setting::factory()->for($tune)->create(['user_id' => $user->id]);
    $settingToDelete = Setting::factory()->for($tune)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("/settings/{$settingToDelete->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('settings', ['id' => $settingToDelete->id]);
});

test('deleting the last setting also deletes the tune', function () {
    $user = User::factory()->create();
    $tune = Tune::factory()->create();
    $setting = Setting::factory()->for($tune)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("/settings/{$setting->id}");

    $response->assertRedirect(route('tunes.index'));
    $this->assertDatabaseMissing('settings', ['id' => $setting->id]);
    $this->assertDatabaseMissing('tunes', ['id' => $tune->id]);
});

test('non-owner cannot delete a setting', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $setting = Setting::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($otherUser)->delete("/settings/{$setting->id}");

    $response->assertStatus(403);
});

test('admin can delete any setting', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $owner = User::factory()->create();
    $tune = Tune::factory()->create();
    Setting::factory()->for($tune)->create(['user_id' => $owner->id]);
    $settingToDelete = Setting::factory()->for($tune)->create(['user_id' => $owner->id]);

    $response = $this->actingAs($admin)->delete("/settings/{$settingToDelete->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('settings', ['id' => $settingToDelete->id]);
});

test('guests cannot store a setting', function () {
    $tune = Tune::factory()->create();

    $response = $this->post("/tunes/{$tune->id}/settings", [
        'name' => 'Test',
        'time_signature' => '4/4',
        'default_note_length' => '1/8',
        'key_signature' => 'D',
        'abc_transcription' => 'ABCD|',
    ]);

    $response->assertRedirect('/login');
});

test('guests cannot edit a setting', function () {
    $setting = Setting::factory()->create();

    $response = $this->get("/settings/{$setting->id}/edit");

    $response->assertRedirect('/login');
});

test('guests cannot update a setting', function () {
    $setting = Setting::factory()->create();

    $response = $this->put("/settings/{$setting->id}", [
        'name' => 'Test',
        'time_signature' => '4/4',
        'default_note_length' => '1/8',
        'key_signature' => 'D',
        'abc_transcription' => 'ABCD|',
    ]);

    $response->assertRedirect('/login');
});

test('guests cannot delete a setting', function () {
    $setting = Setting::factory()->create();

    $response = $this->delete("/settings/{$setting->id}");

    $response->assertRedirect('/login');
});
