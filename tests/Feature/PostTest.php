<?php

use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;

test('post belongs to a user', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();

    expect($post->user)->toBeInstanceOf(User::class)
        ->and($post->user->id)->toBe($user->id);
});

test('user has many posts', function () {
    $user = User::factory()->create();
    Post::factory()->count(3)->for($user)->create();

    expect($user->posts)->toHaveCount(3);
});

test('post has many images ordered by sort_order', function () {
    $post = Post::factory()->create();
    PostImage::factory()->for($post)->create(['sort_order' => 2]);
    PostImage::factory()->for($post)->create(['sort_order' => 0]);
    PostImage::factory()->for($post)->create(['sort_order' => 1]);

    expect($post->images->pluck('sort_order')->all())->toBe([0, 1, 2]);
});

test('deleting a user cascades to posts and images', function () {
    $user = User::factory()->create();
    $post = Post::factory()->for($user)->create();
    PostImage::factory()->for($post)->create();

    $user->delete();

    expect(Post::count())->toBe(0)
        ->and(PostImage::count())->toBe(0);
});

test('deleting a post cascades to images', function () {
    $post = Post::factory()->create();
    PostImage::factory()->count(2)->for($post)->create();

    $post->delete();

    expect(PostImage::count())->toBe(0);
});

test('latestFirst scope orders by updated_at desc', function () {
    $older = Post::factory()->create(['updated_at' => now()->subDay()]);
    $newer = Post::factory()->create(['updated_at' => now()]);

    expect(Post::latestFirst()->first()->id)->toBe($newer->id);
});
