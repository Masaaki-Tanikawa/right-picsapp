<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Return a paginated page of the post's comments as rendered HTML.
     */
    public function index(User $user, Post $post): JsonResponse
    {
        $comments = $post->comments()->with('user')->paginate(10);
        $comments->each(fn (Comment $comment) => $comment->setRelation('post', $post));

        return response()->json([
            'html' => view('posts._comment-list-items', ['comments' => $comments])->render(),
            'nextUrl' => $comments->nextPageUrl(),
        ]);
    }

    /**
     * Store a newly created comment and return its rendered HTML.
     */
    public function store(StoreCommentRequest $request, User $user, Post $post): JsonResponse
    {
        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $request->validated('content'),
        ]);

        $post->loadMissing('user');
        $comment->setRelation('user', $request->user());
        $comment->setRelation('post', $post);

        return response()->json([
            'html' => view('posts._comment', ['comment' => $comment])->render(),
            'comments_count' => $post->comments()->count(),
        ], 201);
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(User $user, Post $post, Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json([
            'comments_count' => $post->comments()->count(),
        ]);
    }
}
