<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('guest can view any posts', function () {
    expect(Gate::forUser(null)->allows('viewAny', Post::class))->toBeTrue();
});

test('authenticated user can view any posts', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Post::class))->toBeTrue();
});

test('guest can view a single post', function () {
    $post = Post::factory()->create();

    expect(Gate::forUser(null)->allows('view', $post))->toBeTrue();
});

test('authenticated user can view a single post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    expect($user->can('view', $post))->toBeTrue();
});

test('authenticated user can create a post', function () {
    $user = User::factory()->create();

    expect($user->can('create', Post::class))->toBeTrue();
});

test('guest cannot create a post', function () {
    expect(Gate::forUser(null)->allows('create', Post::class))->toBeFalse();
});

test('owner can update their own post', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    expect($owner->can('update', $post))->toBeTrue();
});

test('other user cannot update someone else post', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    expect($other->can('update', $post))->toBeFalse();
});

test('owner can delete their own post', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    expect($owner->can('delete', $post))->toBeTrue();
});

test('other user cannot delete someone else post', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    expect($other->can('delete', $post))->toBeFalse();
});
