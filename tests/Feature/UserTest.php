<?php

use App\Models\User;

test('public profile is displayed for an existing user', function () {
    $user = User::factory()->create([
        'name' => 'Tanikawa Masaaki',
        'username' => 'tanikawa',
    ]);

    $response = $this->get('/tanikawa');

    $response->assertOk();
    $response->assertSeeText('Tanikawa Masaaki');
    $response->assertSeeText('@tanikawa');
});

test('public profile is accessible to guests', function () {
    User::factory()->create(['username' => 'guest_target']);

    $response = $this->get('/guest_target');

    $response->assertOk();
    $this->assertGuest();
});

test('unknown username returns 404', function () {
    $response = $this->get('/does_not_exist');

    $response->assertNotFound();
});

test('reserved-style top-level routes are not captured by the public profile route', function () {
    $response = $this->get('/profile');

    expect($response->status())->not->toBe(404);
});

test('profile owner sees the edit profile button', function () {
    $user = User::factory()->create(['username' => 'owner_handle']);

    $response = $this->actingAs($user)->get('/owner_handle');

    $response->assertOk();
    $response->assertSee(__('Edit Profile'));
    $response->assertDontSee(__('Follow'));
});

test('other authenticated users see the follow button', function () {
    $owner = User::factory()->create(['username' => 'target_handle']);
    $viewer = User::factory()->create();

    $response = $this->actingAs($viewer)->get('/target_handle');

    $response->assertOk();
    $response->assertSee(__('Follow'));
    $response->assertDontSee(__('Edit Profile'));
});

test('empty state message is shown when there are no posts', function () {
    User::factory()->create(['username' => 'empty_user']);

    $response = $this->get('/empty_user');

    $response->assertOk();
    $response->assertSeeText(__('No posts yet'));
});

test('guests see the follow button linking to login', function () {
    User::factory()->create(['username' => 'public_handle']);

    $response = $this->get('/public_handle');

    $response->assertOk();
    $response->assertSee(__('Follow'));
    $response->assertDontSee(__('Edit Profile'));
    $response->assertSee('href="'.route('login').'"', escape: false);
});
