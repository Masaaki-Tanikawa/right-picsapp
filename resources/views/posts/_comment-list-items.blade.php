@foreach ($comments as $comment)
    @include('posts._comment', ['comment' => $comment])
@endforeach
