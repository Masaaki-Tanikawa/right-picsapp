<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// ── rate limiting ─────────────────────────────────────────────────

test('comment store and destroy routes are rate limited', function () {
    $store = Route::getRoutes()->getByName('comments.store');
    $destroy = Route::getRoutes()->getByName('comments.destroy');

    expect($store->gatherMiddleware())->toContain('throttle:60,1')
        ->and($destroy->gatherMiddleware())->toContain('throttle:60,1');
});

// ── store ─────────────────────────────────────────────────────────

test('an authenticated user can post a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('comments.store', [$post->user, $post]), [
            'content' => 'Nice shot!',
        ]);

    $response->assertCreated()
        ->assertJsonStructure(['html', 'comments_count'])
        ->assertJson(['comments_count' => 1]);
    $response->assertSee('Nice shot!', false);

    expect($post->comments()->count())->toBe(1)
        ->and($post->comments()->first())
        ->content->toBe('Nice shot!')
        ->user_id->toBe($user->id);
});

test('a comment requires non-empty content within the length limit', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->postJson(route('comments.store', [$post->user, $post]), ['content' => ''])
        ->assertJsonValidationErrors('content');

    $this->actingAs($user)
        ->postJson(route('comments.store', [$post->user, $post]), [
            'content' => str_repeat('a', 1001),
        ])
        ->assertJsonValidationErrors('content');

    expect(Comment::count())->toBe(0);
});

test('a whitespace-only comment is rejected', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->postJson(route('comments.store', [$post->user, $post]), ['content' => "   \n\t  "])
        ->assertJsonValidationErrors('content');

    expect(Comment::count())->toBe(0);
});

test('comment content is trimmed before saving', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($user)
        ->postJson(route('comments.store', [$post->user, $post]), ['content' => '  hello  '])
        ->assertCreated();

    expect($post->comments()->first()->content)->toBe('hello');
});

test('a guest cannot post a comment', function () {
    $post = Post::factory()->create();

    $this->postJson(route('comments.store', [$post->user, $post]), ['content' => 'hi'])
        ->assertUnauthorized();

    expect(Comment::count())->toBe(0);
});

// ── destroy ───────────────────────────────────────────────────────

test('the comment author can delete their own comment', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->for($author)->create();

    $response = $this->actingAs($author)
        ->deleteJson(route('comments.destroy', [$post->user, $post, $comment]));

    $response->assertOk()->assertJson(['comments_count' => 0]);
    expect(Comment::find($comment->id))->toBeNull();
});

test('the post owner can delete comments on their post', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $commenter = User::factory()->create();
    $comment = Comment::factory()->for($post)->for($commenter)->create();

    $this->actingAs($owner)
        ->deleteJson(route('comments.destroy', [$post->user, $post, $comment]))
        ->assertOk()
        ->assertJson(['comments_count' => 0]);

    expect(Comment::find($comment->id))->toBeNull();
});

test('an unrelated user cannot delete a comment', function () {
    $post = Post::factory()->create();
    $commenter = User::factory()->create();
    $comment = Comment::factory()->for($post)->for($commenter)->create();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)
        ->deleteJson(route('comments.destroy', [$post->user, $post, $comment]))
        ->assertForbidden();

    expect(Comment::find($comment->id))->not->toBeNull();
});

test('a guest cannot delete a comment', function () {
    $post = Post::factory()->create();
    $comment = Comment::factory()->for($post)->create();

    $this->deleteJson(route('comments.destroy', [$post->user, $post, $comment]))
        ->assertUnauthorized();

    expect(Comment::find($comment->id))->not->toBeNull();
});

test('the destroy route scopes the comment to the post', function () {
    $post = Post::factory()->create();
    $otherPost = Post::factory()->create();
    $comment = Comment::factory()->for($otherPost)->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->deleteJson(route('comments.destroy', [$post->user, $post, $comment]))
        ->assertNotFound();
});

// ── pagination ────────────────────────────────────────────────────

test('the comments index returns a paginated page of comments as json', function () {
    $post = Post::factory()->create();
    Comment::factory()->for($post)->count(25)->create();

    $response = $this->getJson(route('comments.index', [$post->user, $post]));

    $response->assertOk()->assertJsonStructure(['html', 'nextUrl']);
    expect($response->json('nextUrl'))->toContain('page=2');
});

test('the comments index returns a null nextUrl on the last page', function () {
    $post = Post::factory()->create();
    Comment::factory()->for($post)->count(8)->create();

    $response = $this->getJson(route('comments.index', [$post->user, $post]));

    expect($response->json('nextUrl'))->toBeNull();
});

test('the detail page only renders the first page of comments', function () {
    $post = Post::factory()->create();
    Comment::factory()->for($post)->count(15)->create();

    $response = $this->get(route('posts.show', [$post->user, $post]))->assertOk();

    // The load-more control is present because more pages exist.
    $response->assertSee(__('Load more'), false);
});

test('the detail page load-more points at the comments index endpoint, not the show page', function () {
    $post = Post::factory()->create();
    Comment::factory()->for($post)->count(15)->create();

    $nextUrl = route('comments.index', [$post->user, $post]).'?page=2';

    $this->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertSee(str_replace('/', '\/', $nextUrl), false);
});

// ── view integration ──────────────────────────────────────────────

test('the detail page lists existing comments with the count', function () {
    $post = Post::factory()->create();
    Comment::factory()->for($post)->create(['content' => 'First comment here']);
    Comment::factory()->for($post)->create(['content' => 'Second comment here']);

    $this->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertSee('First comment here', false)
        ->assertSee('Second comment here', false)
        ->assertSee(__('Comments'), false)
        ->assertSee('>2</span>', false);
});

test('the comment form is shown to authenticated users and hidden from guests', function () {
    $post = Post::factory()->create();
    $storeUrl = str_replace('/', '\/', route('comments.store', [$post->user, $post]));

    $this->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertDontSee('@submit.prevent="submit"', false)
        ->assertSee(route('login'), false);

    $user = User::factory()->create();
    $this->actingAs($user)
        ->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertSee('@submit.prevent="submit"', false)
        ->assertSee($storeUrl, false);
});

test('the delete control shows only to users who may delete the comment', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    $commenter = User::factory()->create();
    $comment = Comment::factory()->for($post)->for($commenter)->create();
    $destroyUrl = str_replace('/', '\/', route('comments.destroy', [$post->user, $post, $comment]));

    // commenter sees delete control
    $this->actingAs($commenter)
        ->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertSee($destroyUrl, false);

    // post owner sees delete control
    $this->actingAs($owner)
        ->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertSee($destroyUrl, false);

    // unrelated user does not
    $stranger = User::factory()->create();
    $this->actingAs($stranger)
        ->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertDontSee($destroyUrl, false);
});

test('the feed shows the comment count on each post card', function () {
    $post = Post::factory()->create();
    Comment::factory()->for($post)->count(3)->create();

    $this->get(route('posts.index'))
        ->assertOk()
        ->assertSee('>3</span>', false);
});
