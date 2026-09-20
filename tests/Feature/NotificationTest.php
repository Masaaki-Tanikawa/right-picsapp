<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\CommentReceived;
use Illuminate\Support\Facades\DB;

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

// ── cleanup on delete ─────────────────────────────────────────────

test('deleting a comment deletes the notification about it', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $commenter = User::factory()->create();

    $this->actingAs($commenter)
        ->postJson(route('comments.store', [$owner, $post]), ['content' => 'soon deleted'])
        ->assertCreated();

    $comment = Comment::query()->latest('id')->first();

    expect($owner->notifications()->count())->toBe(1);

    $this->actingAs($commenter)
        ->deleteJson(route('comments.destroy', [$owner, $post, $comment]))
        ->assertOk();

    expect($owner->notifications()->count())->toBe(0);
});

test('deleting a post deletes the notifications about its comments', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $otherPost = Post::factory()->for($owner)->create();

    $owner->notify(new CommentReceived(Comment::factory()->for($post)->create()));
    $owner->notify(new CommentReceived(Comment::factory()->for($otherPost)->create()));

    expect($owner->notifications()->count())->toBe(2);

    $this->actingAs($owner)
        ->delete(route('posts.destroy', [$owner, $post]))
        ->assertRedirect(route('posts.index'));

    expect($owner->notifications()->count())->toBe(1)
        ->and($owner->notifications()->first()->data['post_id'])->toBe($otherPost->id);
});

test('deleting a user deletes their notifications and the ones about their comments', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $commenter = User::factory()->create();

    $owner->notify(new CommentReceived(Comment::factory()->for($post)->for($commenter)->create()));

    $ownPost = Post::factory()->for($commenter)->create();
    $commenter->notify(new CommentReceived(Comment::factory()->for($ownPost)->create()));

    expect($owner->notifications()->count())->toBe(1)
        ->and($commenter->notifications()->count())->toBe(1);

    $commenter->delete();

    expect($owner->notifications()->count())->toBe(0)
        ->and(DB::table('notifications')->count())->toBe(0);
});
