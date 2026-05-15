<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'username' => $user->username,
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'username' => $user->username,
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('username can be changed from profile edit', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'username' => 'new_handle',
            'email' => $user->email,
        ])
        ->assertSessionHasNoErrors();

    expect($user->refresh()->username)->toBe('new_handle');
});

test('username update rejects reserved words', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'username' => 'admin',
            'email' => $user->email,
        ])
        ->assertSessionHasErrors('username');
});

test('username update rejects duplicates', function () {
    User::factory()->create(['username' => 'taken_handle']);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'username' => 'taken_handle',
            'email' => $user->email,
        ])
        ->assertSessionHasErrors('username');
});

test('username update rejects invalid format', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'username' => 'Invalid-Handle!',
            'email' => $user->email,
        ])
        ->assertSessionHasErrors('username');
});

test('bio can be set and updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'bio' => '📸 旅と食 / 東京',
        ])
        ->assertSessionHasNoErrors();

    expect($user->refresh()->bio)->toBe('📸 旅と食 / 東京');
});

test('bio update rejects strings longer than 160 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'bio' => str_repeat('あ', 161),
        ])
        ->assertSessionHasErrors('bio');
});

test('bio is displayed on the public profile page', function () {
    $user = User::factory()->create([
        'username' => 'bio_target',
        'bio' => 'Hello world from my bio',
    ]);

    $this->get('/bio_target')
        ->assertOk()
        ->assertSeeText('Hello world from my bio');
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('profile photo can be uploaded', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post('/profile/photo', [
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

    $response->assertOk()->assertJsonStructure(['url']);

    $user->refresh();

    expect($user->profile_photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($user->profile_photo_path);
});

test('uploading a new profile photo replaces the previous one', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/profile/photo', ['profile_photo' => UploadedFile::fake()->image('first.jpg')]);

    $firstPath = $user->refresh()->profile_photo_path;

    $this->actingAs($user)
        ->post('/profile/photo', ['profile_photo' => UploadedFile::fake()->image('second.jpg')]);

    $user->refresh();

    expect($user->profile_photo_path)->not->toBe($firstPath);
    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertExists($user->profile_photo_path);
});

test('profile photo upload rejects non-image files', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->postJson('/profile/photo', [
            'profile_photo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ]);

    $response->assertStatus(422)->assertJsonValidationErrors(['profile_photo']);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
