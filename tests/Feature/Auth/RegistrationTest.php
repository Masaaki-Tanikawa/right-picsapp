<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/signup');

    $response->assertStatus(200);
});

test('new users can signup', function () {
    $response = $this->post('/signup', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/');
});

test('username is auto-generated on signup', function () {
    $this->post('/signup', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::firstWhere('email', 'test@example.com');

    expect($user->username)
        ->toStartWith('user_')
        ->toMatch('/^[a-z0-9_]{3,20}$/');
});
