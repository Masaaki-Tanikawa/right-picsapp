<x-app-layout>
    <div class="max-w-xl mx-auto py-4 px-4 sm:px-0">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
                @switch(session('status'))
                    @case('post-created') {{ __('Post created.') }} @break
                    @case('post-updated') {{ __('Post updated.') }} @break
                    @default {{ session('status') }}
                @endswitch
            </div>
        @endif

        <x-post-card :post="$post" />

        {{-- TODO: コメント機能実装後に $sampleComments を $post->comments（eager load）に差し替え --}}
        @php
            $sampleComments = [
                ['username' => 'alice_photo', 'created_at' => now()->subHours(2), 'content' => '素敵な写真ですね！どこで撮ったんですか？'],
                ['username' => 'bob_2024', 'created_at' => now()->subDay(), 'content' => 'いいね'],
                ['username' => 'charlie', 'created_at' => now()->subDays(3), 'content' => '光の入り方がとても綺麗です'],
            ];
        @endphp

        <section class="mt-4 bg-white p-4 rounded-md">
            <h2 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Comments') }} ({{ count($sampleComments) }})</h2>

            <ul class="flex flex-col gap-3">
                @foreach ($sampleComments as $comment)
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 shrink-0 rounded-full bg-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600">
                            {{ mb_strtoupper(mb_substr($comment['username'], 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline gap-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $comment['username'] }}</span>
                                <span class="text-xs text-gray-500">{{ $comment['created_at']->format('Y-m-d') }}</span>
                            </div>
                            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $comment['content'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</x-app-layout>
