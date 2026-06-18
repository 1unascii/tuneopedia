<?php

use App\Models\DiscussionThread;
use App\Models\User;

test('discussion threads index page displays threads', function () {
    $thread = DiscussionThread::factory()->create(['title' => 'Favorite jam tunes']);

    $response = $this->get('/discussion-threads');

    $response->assertStatus(200);
    $response->assertSeeText('Favorite jam tunes');
});

test('discussion threads index page shows latest threads first', function () {
    DiscussionThread::factory()->create([
        'title' => 'Older Thread',
        'created_at' => now()->subDays(5),
    ]);
    DiscussionThread::factory()->create([
        'title' => 'Newer Thread',
        'created_at' => now(),
    ]);

    $response = $this->get('/discussion-threads');

    $response->assertStatus(200);
    $response->assertSeeTextInOrder(['Newer Thread', 'Older Thread']);
});

test('guests cannot create discussion threads', function () {
    $response = $this->post('/discussion-threads', [
        'title' => 'Test Thread',
        'body' => 'Test body',
    ]);

    $response->assertRedirect('/login');
});

test('authenticated user can create a discussion thread', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/discussion-threads', [
        'title' => 'New Jam Tune',
        'body' => 'What are your favorites?',
    ]);

    $response->assertRedirect('/discussion-threads');
    $this->assertDatabaseHas('discussion_threads', [
        'title' => 'New Jam Tune',
        'body' => 'What are your favorites?',
        'user_id' => $user->id,
    ]);
});

test('creating a discussion thread requires a title', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/discussion-threads', [
        'body' => 'Some body text',
    ]);

    $response->assertSessionHasErrors(['title']);
});

test('creating a discussion thread requires a body', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/discussion-threads', [
        'title' => 'Some title',
    ]);

    $response->assertSessionHasErrors(['body']);
});

test('creating a discussion thread title must be under 255 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/discussion-threads', [
        'title' => str_repeat('a', 256),
        'body' => 'Some body',
    ]);

    $response->assertSessionHasErrors(['title']);
});

test('thread owner can view the edit form', function () {
    $user = User::factory()->create();
    $thread = DiscussionThread::factory()->for($user)->create();

    $response = $this->actingAs($user)->get("/discussion-threads/{$thread->id}/edit");

    $response->assertStatus(200);
});

test('non-owner cannot view the edit form', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $thread = DiscussionThread::factory()->for($owner)->create();

    $response = $this->actingAs($otherUser)->get("/discussion-threads/{$thread->id}/edit");

    $response->assertStatus(403);
});

test('thread owner can update their thread', function () {
    $user = User::factory()->create();
    $thread = DiscussionThread::factory()->for($user)->create();

    $response = $this->actingAs($user)->put("/discussion-threads/{$thread->id}", [
        'body' => 'Updated body text',
    ]);

    $response->assertRedirect('/discussion-threads');
    $this->assertDatabaseHas('discussion_threads', [
        'id' => $thread->id,
        'body' => 'Updated body text',
    ]);
});

test('non-owner cannot update a thread', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $thread = DiscussionThread::factory()->for($owner)->create();

    $response = $this->actingAs($otherUser)->put("/discussion-threads/{$thread->id}", [
        'body' => 'Hacked body',
    ]);

    $response->assertStatus(403);
});

test('updating a thread requires body under 255 characters', function () {
    $user = User::factory()->create();
    $thread = DiscussionThread::factory()->for($user)->create();

    $response = $this->actingAs($user)->put("/discussion-threads/{$thread->id}", [
        'body' => str_repeat('a', 256),
    ]);

    $response->assertSessionHasErrors(['body']);
});

test('thread owner can delete their thread', function () {
    $user = User::factory()->create();
    $thread = DiscussionThread::factory()->for($user)->create();

    $response = $this->actingAs($user)->delete("/discussion-threads/{$thread->id}");

    $response->assertRedirect('/discussion-threads');
    $this->assertDatabaseMissing('discussion_threads', ['id' => $thread->id]);
});

test('non-owner cannot delete a thread', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $thread = DiscussionThread::factory()->for($owner)->create();

    $response = $this->actingAs($otherUser)->delete("/discussion-threads/{$thread->id}");

    $response->assertStatus(403);
});

test('guests cannot delete a thread', function () {
    $thread = DiscussionThread::factory()->create();

    $response = $this->delete("/discussion-threads/{$thread->id}");

    $response->assertRedirect('/login');
});
