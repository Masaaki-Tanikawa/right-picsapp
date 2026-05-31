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

        @can('update', $post)
            <div class="mt-3 flex justify-end gap-2">
                <a
                    href="{{ route('posts.edit', [$post->user, $post]) }}"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                    </svg>
                    {{ __('Edit') }}
                </a>

                <form
                    method="POST"
                    action="{{ route('posts.destroy', [$post->user, $post]) }}"
                    x-data
                    x-on:submit.prevent="confirm(@js(__('Are you sure you want to delete this post?'))) && $el.submit()"
                >
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md border border-red-200 bg-white text-sm font-medium text-red-600 hover:bg-red-50 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        @endcan

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
