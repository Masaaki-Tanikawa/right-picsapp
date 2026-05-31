<?php

use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ── index / show / edit (view data) ───────────────────────────────

test('index passes paginated posts to the view', function () {
    Post::factory()->count(3)->create();

    $response = $this->get(route('posts.index'));

    $response->assertOk()->assertViewIs('posts.index')->assertViewHas('posts');
    expect($response->viewData('posts')->total())->toBe(3);
});

test('post list links to the detail page', function () {
    $post = Post::factory()->create(['content' => 'A caption to read more of.']);
    PostImage::factory()->for($post)->create();

    $this->get(route('posts.index'))
        ->assertOk()
        ->assertSee(route('posts.show', [$post->user, $post]), false)
        ->assertSee(__('Read more'));
});

test('index returns json with html and next page url when requested as json', function () {
    Post::factory()->count(25)->create();

    $response = $this->getJson(route('posts.index'));

    $response->assertOk()->assertJsonStructure(['html', 'nextUrl']);
    expect($response->json('nextUrl'))->toContain('page=2');
});

test('index returns null nextUrl on last page', function () {
    Post::factory()->count(5)->create();

    $response = $this->getJson(route('posts.index'));

    expect($response->json('nextUrl'))->toBeNull();
});

test('empty feed shows a create CTA to authenticated users but not guests', function () {
    $this->get(route('posts.index'))
        ->assertOk()
        ->assertDontSee('<span>'.__('New post').'</span>', false);

    $user = User::factory()->create();
    $this->actingAs($user)->get(route('posts.index'))
        ->assertOk()
        ->assertSee('<span>'.__('New post').'</span>', false);
});

test('non-empty feed shows the mobile FAB to authenticated users only', function () {
    Post::factory()->count(2)->create();

    $this->get(route('posts.index'))
        ->assertOk()
        ->assertDontSee('sm:hidden fixed bottom-6 right-6', false);

    $user = User::factory()->create();
    $this->actingAs($user)->get(route('posts.index'))
        ->assertOk()
        ->assertSee('sm:hidden fixed bottom-6 right-6', false);
});

test('show passes the post to the view', function () {
    $post = Post::factory()->create();

    $this->get(route('posts.show', [$post->user, $post]))
        ->assertOk()
        ->assertViewIs('posts.show')
        ->assertViewHas('post', fn ($viewPost) => $viewPost->is($post));
});

test('show renders share button wired up with the post url', function () {
    $post = Post::factory()->create();
    $url = route('posts.show', [$post->user, $post]);

    $response = $this->get($url)->assertOk();

    $response->assertSee('@click="share"', false)
        ->assertSee(str_replace('/', '\/', $url), false);
});

test('create renders the new post form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('posts.create'));

    $response->assertOk()
        ->assertViewIs('posts.create')
        ->assertSee('action="'.route('posts.store').'"', false)
        ->assertSee('enctype="multipart/form-data"', false)
        ->assertSee('name="images[]"', false)
        ->assertSee('name="title"', false)
        ->assertSee('name="content"', false);
});

test('edit passes the post to the view', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    $this->actingAs($owner)->get(route('posts.edit', [$owner, $post]))
        ->assertOk()
        ->assertViewIs('posts.edit')
        ->assertViewHas('post', fn ($viewPost) => $viewPost->is($post));
});

test('edit form is prefilled and wired up for the owner', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create([
        'title' => 'Editable title',
        'content' => 'Editable body',
    ]);
    $image = PostImage::factory()->for($post)->create();

    $this->actingAs($owner)->get(route('posts.edit', [$owner, $post]))
        ->assertOk()
        ->assertSee('action="'.route('posts.update', [$owner, $post]).'"', false)
        ->assertSee('name="_method"', false)
        ->assertSee('value="PUT"', false)
        ->assertSee('name="images[]"', false)
        ->assertSee('name="deleted_image_ids[]"', false)
        ->assertSee('Editable title', false)
        ->assertSee('Editable body', false)
        ->assertSee(basename($image->image_path), false);
});

// ── store ─────────────────────────────────────────────────────────

test('store persists post and images, then redirects to show', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('posts.store'), [
        'title' => 'My title',
        'content' => 'My content',
        'images' => [
            UploadedFile::fake()->image('a.jpg'),
            UploadedFile::fake()->image('b.jpg'),
            UploadedFile::fake()->image('c.jpg'),
        ],
    ]);

    $post = Post::firstWhere('user_id', $user->id);

    expect($post)->not->toBeNull()
        ->and($post->title)->toBe('My title')
        ->and($post->content)->toBe('My content')
        ->and($post->images)->toHaveCount(3)
        ->and($post->images->pluck('sort_order')->all())->toBe([0, 1, 2]);

    foreach ($post->images as $image) {
        Storage::disk('public')->assertExists($image->image_path);
        expect($image->image_path)->toStartWith('post-images/');
    }

    $response->assertRedirect(route('posts.show', [$user, $post]))
        ->assertSessionHas('status', 'post-created');
});

test('store requires at least one image', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), ['title' => 'No images here'])
        ->assertSessionHasErrors('images');

    expect(Post::count())->toBe(0);
});

test('store rejects more than ten images', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'images' => collect(range(1, 11))
                ->map(fn ($n) => UploadedFile::fake()->image("img{$n}.jpg"))
                ->all(),
        ])
        ->assertSessionHasErrors('images');

    expect(Post::count())->toBe(0);
});

test('store rejects a non-image file', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'images' => [UploadedFile::fake()->create('document.pdf', 10)],
        ])
        ->assertSessionHasErrors('images.0');

    expect(Post::count())->toBe(0);
});

test('store rejects an image larger than 2MB', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'images' => [UploadedFile::fake()->image('huge.jpg')->size(3000)],
        ])
        ->assertSessionHasErrors('images.0');

    expect(Post::count())->toBe(0);
});

test('store rejects title and content that exceed their limits', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => str_repeat('a', 256),
            'content' => str_repeat('a', 10001),
            'images' => [UploadedFile::fake()->image('a.jpg')],
        ])
        ->assertSessionHasErrors(['title', 'content']);

    expect(Post::count())->toBe(0);
});

// ── update ────────────────────────────────────────────────────────

test('update modifies title and content', function () {
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create([
        'title' => 'original',
        'content' => 'original body',
    ]);

    $this->actingAs($owner)
        ->put(route('posts.update', [$owner, $post]), [
            'title' => 'updated',
            'content' => 'updated body',
        ])
        ->assertRedirect(route('posts.show', [$owner, $post]))
        ->assertSessionHas('status', 'post-updated');

    expect($post->fresh())
        ->title->toBe('updated')
        ->content->toBe('updated body');
});

test('update deletes specified images from db and storage', function () {
    Storage::fake('public');
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    $keptPath = UploadedFile::fake()->image('kept.jpg')->store('post-images', 'public');
    $deletedPath = UploadedFile::fake()->image('deleted.jpg')->store('post-images', 'public');
    $kept = PostImage::factory()->for($post)->create(['image_path' => $keptPath, 'sort_order' => 0]);
    $deleted = PostImage::factory()->for($post)->create(['image_path' => $deletedPath, 'sort_order' => 1]);

    $this->actingAs($owner)->put(route('posts.update', [$owner, $post]), [
        'deleted_image_ids' => [$deleted->id],
    ]);

    expect(PostImage::find($deleted->id))->toBeNull()
        ->and(PostImage::find($kept->id))->not->toBeNull();
    Storage::disk('public')->assertMissing($deletedPath);
    Storage::disk('public')->assertExists($keptPath);
});

test('update appends new images continuing sort_order', function () {
    Storage::fake('public');
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();
    PostImage::factory()->for($post)->create(['sort_order' => 0]);
    PostImage::factory()->for($post)->create(['sort_order' => 1]);

    $this->actingAs($owner)->put(route('posts.update', [$owner, $post]), [
        'images' => [
            UploadedFile::fake()->image('new1.jpg'),
            UploadedFile::fake()->image('new2.jpg'),
        ],
    ]);

    expect($post->fresh()->images->pluck('sort_order')->all())->toBe([0, 1, 2, 3]);
});

// ── destroy ───────────────────────────────────────────────────────

test('destroy deletes image files from storage and the post itself', function () {
    Storage::fake('public');
    $owner = User::factory()->create();
    $post = Post::factory()->for($owner)->create();

    $path1 = UploadedFile::fake()->image('p1.jpg')->store('post-images', 'public');
    $path2 = UploadedFile::fake()->image('p2.jpg')->store('post-images', 'public');
    PostImage::factory()->for($post)->create(['image_path' => $path1]);
    PostImage::factory()->for($post)->create(['image_path' => $path2]);

    $response = $this->actingAs($owner)->delete(route('posts.destroy', [$owner, $post]));

    expect(Post::find($post->id))->toBeNull()
        ->and(PostImage::where('post_id', $post->id)->count())->toBe(0);
    Storage::disk('public')->assertMissing($path1);
    Storage::disk('public')->assertMissing($path2);

    $response->assertRedirect(route('posts.index'))
        ->assertSessionHas('status', 'post-deleted');
});
