<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Like the given post for the authenticated user.
     */
    public function store(Request $request, User $user, Post $post): JsonResponse
    {
        $request->user()->likedPosts()->syncWithoutDetaching([$post->id]);

        return $this->likeState($post, liked: true);
    }

    /**
     * Remove the authenticated user's like from the given post.
     */
    public function destroy(Request $request, User $user, Post $post): JsonResponse
    {
        $request->user()->likedPosts()->detach($post->id);

        return $this->likeState($post, liked: false);
    }

    /**
     * @return JsonResponse{liked: bool, likers_count: int}
     */
    private function likeState(Post $post, bool $liked): JsonResponse
    {
        return response()->json([
            'liked' => $liked,
            'likers_count' => $post->likers()->count(),
        ]);
    }
}
