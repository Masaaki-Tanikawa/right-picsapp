<?php

use App\Models\Post;
use App\Models\User;

test('guest can access posts index', function () {
    $this->get(route('posts.index'))->assertOk();
});

test('guest can access posts show', function () {
    $post = Post::factory()->create();

    $this->get(route('posts.show', [$post->user, $post]))->assertOk();
});

test('guest is redirected to login from auth-required post routes', function () {
    $post = Post::factory()->create();

    $this->get(route('posts.create'))->assertRedirect(route('login'));
    $this->post(route('posts.store'))->assertRedirect(route('login'));
    $this->get(route('posts.edit', [$post->user, $post]))->assertRedirect(route('login'));
    $this->put(route('posts.update', [$post->user, $post]))->assertRedirect(route('login'));
    $this->delete(route('posts.destroy', [$post->user, $post]))->assertRedirect(route('login'));
});

test('authenticated user can access posts create form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('posts.create'))->assertOk();
});

test('the literal /create path resolves to the create form, not a user profile', function () {
    expect(route('posts.create', [], false))->toBe('/create');

    $user = User::factory()->create();

    $this->actingAs($user)->get('/create')
        ->assertOk()
        ->assertViewIs('posts.create');
});

test('non-owner is forbidden from edit, update, and destroy', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    $this->actingAs($other)->get(route('posts.edit', [$owner, $post]))->assertForbidden();
    $this->actingAs($other)->put(route('posts.update', [$owner, $post]))->assertForbidden();
    $this->actingAs($other)->delete(route('posts.destroy', [$owner, $post]))->assertForbidden();
});

test('owner can access posts edit', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    $this->actingAs($owner)->get(route('posts.edit', [$owner, $post]))->assertOk();
});
