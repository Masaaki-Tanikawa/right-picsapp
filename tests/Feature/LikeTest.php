<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// ── rate limiting ─────────────────────────────────────────────────

test('like and unlike routes are rate limited', function () {
    $store = Route::getRoutes()->getByName('likes.store');
    $destroy = Route::getRoutes()->getByName('likes.destroy');

    expect($store->gatherMiddleware())->toContain('throttle:60,1')
        ->and($destroy->gatherMiddleware())->toContain('throttle:60,1');
});

// ── store (like) ──────────────────────────────────────────────────

test('an authenticated user can like a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('likes.store', [$post->user, $post]));

    $response->assertOk()->assertJson(['liked' => true, 'likers_count' => 1]);
    expect($post->likers()->whereKey($user->id)->exists())->toBeTrue();
});

test('liking the same post twice does not create duplicate likes', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)->postJson(route('likes.store', [$post->user, $post]));
    $response = $this->actingAs($user)->postJson(route('likes.store', [$post->user, $post]));

    $response->assertOk()->assertJson(['liked' => true, 'likers_count' => 1]);
    expect($post->likers()->count())->toBe(1);
});

test('a guest cannot like a post', function () {
    $post = Post::factory()->create();

    $this->postJson(route('likes.store', [$post->user, $post]))
        ->assertUnauthorized();

    expect($post->likers()->count())->toBe(0);
});

// ── destroy (unlike) ──────────────────────────────────────────────

test('an authenticated user can unlike a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $post->likers()->attach($user);

    $response = $this->actingAs($user)
        ->deleteJson(route('likes.destroy', [$post->user, $post]));

    $response->assertOk()->assertJson(['liked' => false, 'likers_count' => 0]);
    expect($post->likers()->whereKey($user->id)->exists())->toBeFalse();
});

test('unliking a post that was not liked is a no-op', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('likes.destroy', [$post->user, $post]))
        ->assertOk()
        ->assertJson(['liked' => false, 'likers_count' => 0]);
});

test('a guest cannot unlike a post', function () {
    $post = Post::factory()->create();

    $this->deleteJson(route('likes.destroy', [$post->user, $post]))
        ->assertUnauthorized();
});

// ── view integration ──────────────────────────────────────────────

test('detail page shows the like count and an interactive button to authenticated users', function () {
    $post = Post::factory()->create();
    $post->likers()->attach(User::factory()->count(2)->create());
    $viewer = User::factory()->create();

    $response = $this->actingAs($viewer)
        ->get(route('posts.show', [$post->user, $post]))
        ->assertOk();

    $response->assertSee('@click="toggle"', false)
        ->assertSee(str_replace('/', '\/', route('likes.store', [$post->user, $post])), false)
        ->assertSee('count: 2', false);
});

test('detail page shows the like count but no toggle button to guests', function () {
    $post = Post::factory()->create();
    $post->likers()->attach(User::factory()->count(3)->create());

    $this->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertDontSee('@click="toggle"', false)
        ->assertSee('>3</span>', false);
});

test('the like button reflects whether the current user already liked the post', function () {
    $post = Post::factory()->create();
    $viewer = User::factory()->create();
    $post->likers()->attach($viewer);

    $this->actingAs($viewer)
        ->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertSee('liked: true', false);

    $other = User::factory()->create();
    $this->actingAs($other)
        ->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertSee('liked: false', false);
});
