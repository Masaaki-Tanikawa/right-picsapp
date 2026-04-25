<?php

use App\Models\User;

test('signin screen can be rendered', function () {
    $response = $this->get('/signin');

    $response->assertStatus(200);
});

test('users can authenticate using the signin screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/signin', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/signin', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
