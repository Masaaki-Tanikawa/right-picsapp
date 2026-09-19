<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\CommentReceived;

// ── notify on comment ─────────────────────────────────────────────

test('commenting on a post notifies the post owner', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $commenter = User::factory()->create();

    $this->actingAs($commenter)
        ->postJson(route('comments.store', [$post->user, $post]), ['content' => 'Nice shot!'])
        ->assertCreated();

    expect($owner->unreadNotifications()->count())->toBe(1);

    $data = $owner->notifications()->first()->data;
    expect($data['commenter_username'])->toBe($commenter->username)
        ->and($data['post_id'])->toBe($post->id)
        ->and($data['excerpt'])->toBe('Nice shot!')
        ->and($data['url'])->toBe(route('posts.show', [$owner, $post]));
});

test('commenting on your own post does not create a notification', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    $this->actingAs($owner)
        ->postJson(route('comments.store', [$post->user, $post]), ['content' => 'my own post'])
        ->assertCreated();

    expect($owner->notifications()->count())->toBe(0);
});

// ── mark as read ──────────────────────────────────────────────────

test('a user can mark all their notifications as read', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $comment = Comment::factory()->for($post)->create();
    $owner->notify(new CommentReceived($comment));
    $owner->notify(new CommentReceived($comment));

    expect($owner->unreadNotifications()->count())->toBe(2);

    $this->actingAs($owner)
        ->postJson(route('notifications.read'))
        ->assertOk()
        ->assertJson(['ok' => true]);

    expect($owner->unreadNotifications()->count())->toBe(0)
        ->and($owner->notifications()->count())->toBe(2);
});

test('a guest cannot mark notifications as read', function () {
    $this->postJson(route('notifications.read'))->assertUnauthorized();
});

// ── nav integration ───────────────────────────────────────────────

test('the nav shows the unread notification count and recent notifications', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $commenter = User::factory()->create(['username' => 'nikonshooter']);
    $comment = Comment::factory()->for($post)->for($commenter)->create(['content' => 'great composition']);
    $owner->notify(new CommentReceived($comment));

    $this->actingAs($owner)
        ->get(route('posts.index'))
        ->assertOk()
        ->assertSee('unread: 1', false)
        ->assertSee('nikonshooter', false)
        ->assertSee('great composition', false);
});

test('the nav shows zero unread when there are no notifications', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('posts.index'))
        ->assertOk()
        ->assertSee('unread: 0', false);
});
