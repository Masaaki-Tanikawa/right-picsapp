<?php

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
Route::get('/posts/{post}', [PostController::class, 'show'])
    ->whereNumber('post')
    ->name('posts.show');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('can:create,'.Post::class)->group(function () {
        Route::get('/posts/create', [PostController::class, 'create'])
            ->name('posts.create');
        Route::post('/posts', [PostController::class, 'store'])
            ->name('posts.store');
    });
    Route::middleware('can:update,post')->group(function () {
        Route::get('/posts/{post}/edit', [PostController::class, 'edit'])
            ->whereNumber('post')
            ->name('posts.edit');
        Route::put('/posts/{post}', [PostController::class, 'update'])
            ->whereNumber('post')
            ->name('posts.update');
    });
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->whereNumber('post')
        ->middleware('can:delete,post')
        ->name('posts.destroy');
});

require __DIR__.'/auth.php';

Route::get('/{user}', [UserController::class, 'show'])
    ->where('user', '[a-z0-9_]{3,20}')
    ->name('users.show');
