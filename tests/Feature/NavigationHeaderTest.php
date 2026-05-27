<?php

use App\Models\User;

test('header shows post-create link and avatar dropdown for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('posts.index'))->assertOk();

    $response->assertSee(route('posts.create'), false)
        ->assertSee(route('users.show', $user), false)
        ->assertSee(route('profile.edit'), false)
        ->assertSee(route('logout'), false);
});

test('header shows login and register links for guests, hides post-create', function () {
    $response = $this->get(route('posts.index'))->assertOk();

    $response->assertSee(route('login'), false)
        ->assertSee(route('register'), false)
        ->assertDontSee(route('posts.create'), false)
        ->assertDontSee(route('logout'), false);
});

test('logo links to posts index on every authenticated page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('href="'.route('posts.index').'"', false);
});
