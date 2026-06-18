<?php

use App\Models\Album;
use App\Models\Track;
use App\Models\User;

test('album owner can access create track page', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get("/albums/{$album->id}/tracks/create");

    $response->assertStatus(200);
});

test('non-owner cannot access create track page', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($otherUser)->get("/albums/{$album->id}/tracks/create");

    $response->assertStatus(403);
});

test('album owner can store a track', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post("/albums/{$album->id}/tracks", [
        'name' => 'Cripple Creek',
        'track_number' => 1,
    ]);

    $response->assertRedirect(route('albums.show', $album));
    $this->assertDatabaseHas('tracks', [
        'album_id' => $album->id,
        'name' => 'Cripple Creek',
        'track_number' => 1,
    ]);
});

test('non-owner cannot store a track', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($otherUser)->post("/albums/{$album->id}/tracks", [
        'name' => 'Cripple Creek',
        'track_number' => 1,
    ]);

    $response->assertStatus(403);
});

test('storing a track requires a name', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post("/albums/{$album->id}/tracks", [
        'track_number' => 1,
    ]);

    $response->assertSessionHasErrors(['name']);
});

test('storing a track requires a track number', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->post("/albums/{$album->id}/tracks", [
        'name' => 'Cripple Creek',
    ]);

    $response->assertSessionHasErrors(['track_number']);
});

test('album owner can access edit track page', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);
    $track = Track::create(['album_id' => $album->id, 'name' => 'Sally Goodin', 'track_number' => 1]);

    $response = $this->actingAs($user)->get("/albums/{$album->id}/tracks/{$track->id}/edit");

    $response->assertStatus(200);
});

test('non-owner cannot access edit track page', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);
    $track = Track::create(['album_id' => $album->id, 'name' => 'Sally Goodin', 'track_number' => 1]);

    $response = $this->actingAs($otherUser)->get("/albums/{$album->id}/tracks/{$track->id}/edit");

    $response->assertStatus(403);
});

test('album owner can update a track', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);
    $track = Track::create(['album_id' => $album->id, 'name' => 'Old Name', 'track_number' => 1]);

    $response = $this->actingAs($user)->put("/albums/{$album->id}/tracks/{$track->id}", [
        'name' => 'New Name',
        'track_number' => 2,
    ]);

    $response->assertRedirect(route('albums.show', $album));
    $this->assertDatabaseHas('tracks', ['id' => $track->id, 'name' => 'New Name', 'track_number' => 2]);
});

test('non-owner cannot update a track', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);
    $track = Track::create(['album_id' => $album->id, 'name' => 'Old Name', 'track_number' => 1]);

    $response = $this->actingAs($otherUser)->put("/albums/{$album->id}/tracks/{$track->id}", [
        'name' => 'Hacked',
        'track_number' => 1,
    ]);

    $response->assertStatus(403);
});

test('album owner can delete a track', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);
    $track = Track::create(['album_id' => $album->id, 'name' => 'Delete Me', 'track_number' => 1]);

    $response = $this->actingAs($user)->delete("/albums/{$album->id}/tracks/{$track->id}");

    $response->assertRedirect(route('albums.show', $album));
    $this->assertDatabaseMissing('tracks', ['id' => $track->id]);
});

test('non-owner cannot delete a track', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);
    $track = Track::create(['album_id' => $album->id, 'name' => 'Protected', 'track_number' => 1]);

    $response = $this->actingAs($otherUser)->delete("/albums/{$album->id}/tracks/{$track->id}");

    $response->assertStatus(403);
});

test('guests cannot access tracks', function () {
    $album = Album::factory()->create();

    $response = $this->get("/albums/{$album->id}/tracks/create");

    $response->assertRedirect('/login');
});

test('authenticated user can view tracks index', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create();
    Track::create(['album_id' => $album->id, 'name' => 'Cripple Creek', 'track_number' => 1]);

    $response = $this->actingAs($user)->get("/albums/{$album->id}/tracks");

    $response->assertStatus(200);
    $response->assertSeeText('Cripple Creek');
});

test('authenticated user can view a track', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create();
    $track = Track::create(['album_id' => $album->id, 'name' => 'Sally Goodin', 'track_number' => 2]);

    $response = $this->actingAs($user)->get("/albums/{$album->id}/tracks/{$track->id}");

    $response->assertStatus(200);
});

test('track belongs to album', function () {
    $album = Album::factory()->create(['name' => 'Test Album']);
    $track = Track::create(['album_id' => $album->id, 'name' => 'Test Track', 'track_number' => 1]);

    expect($track->album->name)->toBe('Test Album');
});
