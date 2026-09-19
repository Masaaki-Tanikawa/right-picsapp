@props([
    'post',
    'compact' => false,
])

<article class="bg-white px-4 py-6 rounded-md">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('users.show', $post->user) }}" class="shrink-0">
            <x-user-avatar :user="$post->user" />
        </a>
        <div class="flex flex-col leading-tight">
            <a href="{{ route('users.show', $post->user) }}" class="font-semibold text-gray-900 hover:underline">
                {{ $post->user->username }}
            </a>
            @if ($compact)
                <a href="{{ route('posts.show', [$post->user, $post]) }}" class="text-xs text-gray-500 hover:underline">
                    {{ $post->created_at->diffForHumans() }}
                </a>
            @else
                <span class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>

    @include('posts._image-slider', [
        'images' => $post->images,
        'loading' => $compact ? 'lazy' : 'eager',
        'altPrefix' => $post->title ?: __('Post image by :username', ['username' => $post->user->username]),
        'href' => $compact ? route('posts.show', [$post->user, $post]) : null,
    ])

    <div class="flex items-center gap-4 mt-3">
        @auth
            <div
                x-data="{
                    liked: {{ $post->is_liked ? 'true' : 'false' }},
                    count: {{ (int) ($post->likers_count ?? 0) }},
                    pending: false,
                    async toggle() {
                        if (this.pending) return;
                        this.pending = true;
                        const wasLiked = this.liked;
                        this.liked = !wasLiked;
                        this.count += this.liked ? 1 : -1;
                        try {
                            const response = await fetch(@js(route('likes.store', [$post->user, $post])), {
                                method: wasLiked ? 'DELETE' : 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': @js(csrf_token()),
                                    'Accept': 'application/json',
                                },
                            });
                            if (! response.ok) throw new Error();
                            const data = await response.json();
                            this.liked = data.liked;
                            this.count = data.likers_count;
                        } catch {
                            this.liked = wasLiked;
                            this.count += wasLiked ? 1 : -1;
                        } finally {
                            this.pending = false;
                        }
                    },
                }"
                class="flex items-center gap-1"
            >
                <button type="button" @click="toggle" :aria-pressed="liked" aria-label="{{ __('Like') }}" class="hover:text-red-500" :class="liked ? 'text-red-500' : 'text-gray-700'">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6" aria-hidden="true" :fill="liked ? 'currentColor' : 'none'">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </button>
                <span class="text-sm" x-text="count"></span>
            </div>
        @else
            <div class="flex items-center gap-1 text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                </svg>
                <span class="text-sm">{{ (int) ($post->likers_count ?? 0) }}</span>
            </div>
        @endauth
        {{-- コメント数はコメント機能のブランチで表示予定 --}}
        <button type="button" aria-label="{{ __('Comment') }}" class="flex items-center gap-1 text-gray-700 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
            </svg>
        </button>
        <div
            x-data="{
                toast: '',
                timer: null,
                async share() {
                    const url = @js(route('posts.show', [$post->user, $post]));
                    const title = @js($post->title ?: __('Post by :username', ['username' => $post->user->username]));
                    if (navigator.share) {
                        try {
                            await navigator.share({ title, url });
                            return;
                        } catch (e) {
                            if (e.name === 'AbortError') return;
                        }
                    }
                    try {
                        await navigator.clipboard.writeText(url);
                        this.showToast(@js(__('Link copied')));
                    } catch {
                        this.showToast(@js(__('Failed to copy link')));
                    }
                },
                showToast(message) {
                    this.toast = message;
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => this.toast = '', 2000);
                },
            }"
            class="relative"
        >
            <button type="button" aria-label="{{ __('Share') }}" @click="share" class="block text-gray-700 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-6 h-6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                </svg>
            </button>
            <span
                x-show="toast"
                x-cloak
                x-transition.opacity
                x-text="toast"
                class="absolute left-1/2 top-full mt-1 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white shadow-md"
                role="status"
            ></span>
        </div>
    </div>

    @if ($post->title || $post->content)
        <div
            class="mt-3 flex flex-col gap-2 text-sm text-gray-900"
            @if ($compact)
                x-data="{ truncated: false }"
                x-init="$nextTick(() => truncated = [$refs.title, $refs.body].some((el) => el && el.scrollHeight > el.clientHeight))"
            @endif
        >
            @if ($post->title)
                <p @class(['text-lg font-bold', 'line-clamp-1' => $compact]) @if ($compact) x-ref="title" @endif>{{ $post->title }}</p>
            @endif
            @if ($post->content)
                <p @class(['line-clamp-1' => $compact, 'whitespace-pre-line' => ! $compact]) @if ($compact) x-ref="body" @endif>{{ $post->content }}</p>
            @endif
            @if ($compact)
                <a x-show="truncated" x-cloak href="{{ route('posts.show', [$post->user, $post]) }}" class="self-start text-sm text-gray-500 hover:underline">{{ __('Read more') }}</a>
            @endif
        </div>
    @endif
</article>
