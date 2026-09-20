<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use App\Notifications\CommentReceived;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(3)->create()->each(function (User $user, int $index) {
            $user->forceFill([
                'profile_photo_path' => "https://picsum.photos/seed/avatar{$index}/200/200",
            ])->save();
        });

        $captions = [
            ['title' => 'Summer holiday :3', 'content' => 'Beach days and sunsets are the best therapy.'],
            ['title' => null, 'content' => 'Morning coffee with a view ☕'],
            ['title' => 'Weekend trip', 'content' => 'Exploring new corners of the city.'],
            ['title' => null, 'content' => 'Golden hour never disappoints.'],
            ['title' => 'Foodie diary', 'content' => 'This pasta was unreal. Asking for the recipe.'],
            ['title' => null, 'content' => 'Just a quiet evening at home.'],
        ];

        $imageCounts = [3, 1, 2, 3, 1, 2];

        foreach ($captions as $i => $caption) {
            $user = $users[$i % $users->count()];

            $post = Post::factory()->create([
                'user_id' => $user->id,
                'title' => $caption['title'],
                'content' => $caption['content'],
            ]);

            $count = $imageCounts[$i];
            for ($j = 0; $j < $count; $j++) {
                PostImage::factory()->create([
                    'post_id' => $post->id,
                    'image_path' => "https://picsum.photos/seed/post{$post->id}-{$j}/800/800",
                    'sort_order' => $j,
                ]);
            }

            $likers = $users->random(fake()->numberBetween(0, $users->count()));
            $post->likers()->attach($likers);

            $comments = Comment::factory()
                ->count(fake()->numberBetween(0, 4))
                ->create([
                    'post_id' => $post->id,
                    'user_id' => fn () => $users->random()->id,
                ]);

            // Notify the post owner about comments left by other users.
            foreach ($comments as $comment) {
                if ($comment->user_id !== $post->user_id) {
                    $comment->setRelation('post', $post);
                    $post->user->notify(new CommentReceived($comment));
                }
            }
        }
    }
}
