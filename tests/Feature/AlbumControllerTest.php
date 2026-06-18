<?php

use App\Models\Album;
use App\Models\User;

test('authenticated user can view albums index', function () {
    $user = User::factory()->create();
    Album::factory()->create(['name' => 'Foggy Mountain Banjo']);

    $response = $this->actingAs($user)->get('/albums');

    $response->assertStatus(200);
    $response->assertSeeText('Foggy Mountain Banjo');
});

test('authenticated user can access create album page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/albums/create');

    $response->assertStatus(200);
});

test('authenticated user can store an album', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/albums', [
        'name' => 'New Album',
    ]);

    $response->assertRedirect(route('albums.index'));
    $this->assertDatabaseHas('albums', ['name' => 'New Album', 'user_id' => $user->id]);
});

test('authenticated user can store an album with tracks', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/albums', [
        'name' => 'Album With Tracks',
        'tracks' => [
            ['name' => 'First Track', 'track_number' => 1],
            ['name' => 'Second Track', 'track_number' => 2],
        ],
    ]);

    $response->assertRedirect(route('albums.index'));
    $this->assertDatabaseHas('albums', ['name' => 'Album With Tracks']);
    $this->assertDatabaseHas('tracks', ['name' => 'First Track', 'track_number' => 1]);
    $this->assertDatabaseHas('tracks', ['name' => 'Second Track', 'track_number' => 2]);
});

test('storing an album requires a name', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/albums', []);

    $response->assertSessionHasErrors(['name']);
});

test('authenticated user can view album show page', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['name' => 'The Bothy Band']);

    $response = $this->actingAs($user)->get("/albums/{$album->id}");

    $response->assertStatus(200);
    $response->assertSeeText('The Bothy Band');
});

test('album owner can access edit page', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get("/albums/{$album->id}/edit");

    $response->assertStatus(200);
});

test('non-owner cannot access edit page', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($otherUser)->get("/albums/{$album->id}/edit");

    $response->assertStatus(403);
});

test('album owner can update their album', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id, 'name' => 'Old Name']);

    $response = $this->actingAs($user)->put("/albums/{$album->id}", [
        'name' => 'Updated Name',
    ]);

    $response->assertRedirect(route('albums.show', $album));
    $this->assertDatabaseHas('albums', ['id' => $album->id, 'name' => 'Updated Name']);
});

test('non-owner cannot update an album', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($otherUser)->put("/albums/{$album->id}", [
        'name' => 'Hacked Name',
    ]);

    $response->assertStatus(403);
});

test('album owner can delete their album', function () {
    $user = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("/albums/{$album->id}");

    $response->assertRedirect(route('albums.index'));
    $this->assertDatabaseMissing('albums', ['id' => $album->id]);
});

test('non-owner cannot delete an album', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $album = Album::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($otherUser)->delete("/albums/{$album->id}");

    $response->assertStatus(403);
});

test('guests cannot access albums', function () {
    $response = $this->get('/albums');

    $response->assertRedirect('/login');
});

test('authenticated user can store an album with cover art', function () {
    $user = User::factory()->create();
    $file = \Illuminate\Http\UploadedFile::fake()->image('cover.jpg', 300, 300);

    $response = $this->actingAs($user)->post('/albums', [
        'name' => 'Album With Cover',
        'cover_art' => $file,
    ]);

    $response->assertRedirect(route('albums.index'));
    $album = Album::where('name', 'Album With Cover')->first();
    expect($album->cover_art)->not->toBeNull();
    expect(file_exists(public_path($album->cover_art)))->toBeTrue();

    // Cleanup
    unlink(public_path($album->cover_art));
});

test('deleting an album removes its cover art file', function () {
    $user = User::factory()->create();
    $file = \Illuminate\Http\UploadedFile::fake()->image('cover.png', 300, 300);

    $this->actingAs($user)->post('/albums', [
        'name' => 'Delete Me Album',
        'cover_art' => $file,
    ]);

    $album = Album::where('name', 'Delete Me Album')->first();
    $coverPath = public_path($album->cover_art);
    expect(file_exists($coverPath))->toBeTrue();

    $this->actingAs($user)->delete("/albums/{$album->id}");

    expect(file_exists($coverPath))->toBeFalse();
});

test('storing an album sets user_id and artist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/albums', [
        'name' => 'My Album',
        'artist' => 'Bill Monroe',
    ]);

    $this->assertDatabaseHas('albums', [
        'name' => 'My Album',
        'artist' => 'Bill Monroe',
        'user_id' => $user->id,
    ]);
});
