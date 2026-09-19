<?php

use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [PostController::class, 'index'])->name('posts.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('can:create,'.Post::class)->group(function () {
        Route::get('/create', [PostController::class, 'create'])
            ->name('posts.create');
        Route::post('/posts', [PostController::class, 'store'])
            ->name('posts.store');
    });
});

require __DIR__.'/auth.php';

Route::get('/{user}/{post}', [PostController::class, 'show'])
    ->scopeBindings()
    ->whereNumber('post')
    ->name('posts.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('can:update,post')->group(function () {
        Route::get('/{user}/{post}/edit', [PostController::class, 'edit'])
            ->scopeBindings()
            ->whereNumber('post')
            ->name('posts.edit');
        Route::put('/{user}/{post}', [PostController::class, 'update'])
            ->scopeBindings()
            ->whereNumber('post')
            ->name('posts.update');
    });
    Route::delete('/{user}/{post}', [PostController::class, 'destroy'])
        ->scopeBindings()
        ->whereNumber('post')
        ->middleware('can:delete,post')
        ->name('posts.destroy');

    Route::post('/{user}/{post}/likes', [LikeController::class, 'store'])
        ->scopeBindings()
        ->whereNumber('post')
        ->middleware('throttle:60,1')
        ->name('likes.store');
    Route::delete('/{user}/{post}/likes', [LikeController::class, 'destroy'])
        ->scopeBindings()
        ->whereNumber('post')
        ->middleware('throttle:60,1')
        ->name('likes.destroy');
});

Route::get('/{user}', [UserController::class, 'show'])
    ->where('user', '[a-z0-9_]{3,20}')
    ->name('users.show');
