<x-app-layout>
    <div class="max-w-xl mx-auto py-4 px-4 sm:px-0">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
                @switch(session('status'))
                    @case('post-deleted') {{ __('Post deleted.') }} @break
                    @default {{ session('status') }}
                @endswitch
            </div>
        @endif

        @if ($posts->isEmpty())
            <div class="py-12 text-center">
                <p class="text-gray-500">{{ __('No posts yet') }}</p>
                @auth
                    <a
                        href="{{ route('posts.create') }}"
                        class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold text-sm rounded-md transition-colors duration-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>{{ __('New post') }}</span>
                    </a>
                @endauth
            </div>
        @else
            <div
                x-data="{
                    nextUrl: @js($posts->nextPageUrl()),
                    loading: false,
                    async loadMore() {
                        if (!this.nextUrl || this.loading) return;
                        this.loading = true;
                        try {
                            const res = await fetch(this.nextUrl, { headers: { 'Accept': 'application/json' } });
                            const data = await res.json();
                            this.$refs.list.insertAdjacentHTML('beforeend', data.html);
                            this.nextUrl = data.nextUrl;
                        } finally {
                            this.loading = false;
                        }
                    }
                }"
            >
                <div x-ref="list" class="flex flex-col gap-8">
                    @include('posts._post-list-items', ['posts' => $posts])
                </div>

                <div class="mt-8 flex justify-center">
                    <button
                        type="button"
                        x-show="nextUrl"
                        x-cloak
                        :disabled="loading"
                        @click="loadMore"
                        class="px-6 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200 disabled:opacity-60 transition"
                    >
                        <span x-show="!loading">{{ __('Load more') }}</span>
                        <span x-show="loading" x-cloak>{{ __('Loading...') }}</span>
                    </button>
                </div>
            </div>

            @auth
                <a
                    href="{{ route('posts.create') }}"
                    class="sm:hidden fixed bottom-6 right-6 z-40 flex items-center justify-center w-14 h-14 rounded-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 shadow-lg transition-colors duration-200"
                    aria-label="{{ __('New post') }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
            @endauth
        @endif
    </div>
</x-app-layout>
