<?php

declare(strict_types=1);

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DiscussionReplyController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\DiscussionThreadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrackController;
use App\Http\Controllers\TuneController;
use Illuminate\Support\Facades\Route;

/*** Public Routes ***/
Route::get('/', function () {
    return view('home');
})->name('home');
Route::get('/tunes', [TuneController::class, 'index'])->name('tunes.index');
Route::get('/tunes/create', [TuneController::class, 'create'])->name('tunes.create')->middleware(['auth', 'verified']);
Route::get('/tunes/{tune}', [TuneController::class, 'show'])->name('tunes.show');
Route::get('/settings/{setting}', [SettingController::class, 'show'])->name('settings.show');
Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
Route::get('/collections/create', [CollectionController::class, 'create'])->name('collections.create')->middleware(['auth', 'verified']);
Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store')->middleware(['auth', 'verified', 'throttle:1,1']);
Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
Route::get('/discussion-threads', [DiscussionThreadController::class, 'index'])->name('discussion-threads.index');
Route::get('/discussion-threads/{discussionThread}', [DiscussionThreadController::class, 'show']); // Show one

/*** Authenticated & Verified Users' Routes ***/
Route::middleware(['auth', 'verified'])->group(function () {

    /*** Tune Routes ***/
    Route::post('/tunes', [TuneController::class, 'store'])->name('tunes.store')->middleware('throttle:1,1');
    Route::delete('/tunes/{tune}', [TuneController::class, 'destroy'])->name('tunes.destroy');

    /*** Favorite Routes ***/
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/tunes/{tune}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    /*** Setting Routes ***/
    Route::get('/tunes/{tune}/settings/create', [SettingController::class, 'create'])->name('settings.create');
    Route::post('/tunes/{tune}/settings', [SettingController::class, 'store'])->name('settings.store')->middleware('throttle:1,1');
    Route::get('/settings/{setting}/edit', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/{setting}', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('/settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');

    /*** Album Routes ***/
    Route::resource('albums', AlbumController::class);

    /*** Track Routes (nested under albums, scoped so track must belong to album) ***/
    Route::resource('albums.tracks', TrackController::class)->scoped();

    /*** Discussion Threads Routes ***/
    Route::post('/discussion-threads', [DiscussionThreadController::class, 'store'])->middleware('throttle:1,1');
    Route::delete('/discussion-threads/{discussionThread}', [DiscussionThreadController::class, 'destroy']);
    Route::put('/discussion-threads/{discussionThread}', [DiscussionThreadController::class, 'update']);
    Route::get('/discussion-threads/{discussionThread}/edit', [DiscussionThreadController::class, 'edit']);

    /*** Discussion Replies Routes ***/
    Route::post('/discussion-threads/{discussionThread}/replies', [DiscussionReplyController::class, 'store'])->middleware('throttle:1,1');
    Route::get('/discussion-replies/{discussionReply}/edit', [DiscussionReplyController::class, 'edit']);
    Route::put('/discussion-replies/{discussionReply}', [DiscussionReplyController::class, 'update']);
    Route::delete('/discussion-replies/{discussionReply}', [DiscussionReplyController::class, 'destroy']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
