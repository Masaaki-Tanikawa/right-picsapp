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
            <p class="text-center text-gray-500 py-12">{{ __('No posts yet') }}</p>
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
        @endif
    </div>
</x-app-layout>
