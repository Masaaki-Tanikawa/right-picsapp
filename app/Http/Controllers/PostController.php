<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\PostImageCropper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $posts = Post::with(['user', 'images'])
            ->latestFirst()
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('posts._post-list-items', ['posts' => $posts])->render(),
                'nextUrl' => $posts->nextPageUrl(),
            ]);
        }

        return view('posts.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request, PostImageCropper $cropper): RedirectResponse
    {
        $post = DB::transaction(function () use ($request, $cropper) {
            $post = $request->user()->posts()->create(
                $request->safe()->only(['title', 'content'])
            );

            foreach ($request->validated('images') as $index => $image) {
                $post->images()->create([
                    'image_path' => $cropper->store($image, 'post-images'),
                    'sort_order' => $index,
                ]);
            }

            return $post;
        });

        return redirect()
            ->route('posts.show', [$request->user(), $post])
            ->with('status', 'post-created');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user, Post $post): View
    {
        $post->load(['user', 'images']);

        return view('posts.show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, Post $post): View
    {
        $post->load('images');

        return view('posts.edit', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, User $user, Post $post, PostImageCropper $cropper): RedirectResponse
    {
        DB::transaction(function () use ($request, $post, $cropper) {
            $post->update($request->safe()->only(['title', 'content']));

            $deletedImageIds = $request->validated('deleted_image_ids', []);
            if (! empty($deletedImageIds)) {
                $imagesToDelete = $post->images()
                    ->whereIn('id', $deletedImageIds)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            $newImages = $request->validated('images', []);
            if (! empty($newImages)) {
                $nextSortOrder = ($post->images()->max('sort_order') ?? -1) + 1;

                foreach ($newImages as $image) {
                    $post->images()->create([
                        'image_path' => $cropper->store($image, 'post-images'),
                        'sort_order' => $nextSortOrder++,
                    ]);
                }
            }
        });

        return redirect()
            ->route('posts.show', [$user, $post])
            ->with('status', 'post-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Post $post): RedirectResponse
    {
        DB::transaction(function () use ($post) {
            foreach ($post->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $post->delete();
        });

        return redirect()
            ->route('posts.index')
            ->with('status', 'post-deleted');
    }
}
