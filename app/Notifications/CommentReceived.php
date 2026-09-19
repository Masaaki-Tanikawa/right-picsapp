<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CommentReceived extends Notification
{
    public function __construct(public Comment $comment) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $comment = $this->comment;
        $post = $comment->post;

        return [
            'comment_id' => $comment->id,
            'post_id' => $post->id,
            'commenter_id' => $comment->user_id,
            'commenter_username' => $comment->user->username,
            'excerpt' => Str::limit($comment->content, 50),
            'url' => route('posts.show', [$post->user, $post]),
        ];
    }
}
