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

        <section
            x-data="{
                count: {{ (int) $post->comments_count }},
                content: '',
                submitting: false,
                error: '',
                nextUrl: @js($comments->nextPageUrl()),
                loadingMore: false,
                autoGrow(el) {
                    el.style.height = 'auto';
                    el.style.height = el.scrollHeight + 'px';
                },
                async submit() {
                    if (this.submitting || ! this.content.trim()) return;
                    this.submitting = true;
                    this.error = '';
                    try {
                        const res = await fetch(@js(route('comments.store', [$post->user, $post])), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': @js(csrf_token()),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ content: this.content }),
                        });
                        if (res.status === 422) {
                            const data = await res.json();
                            this.error = Object.values(data.errors)[0][0];
                            return;
                        }
                        if (! res.ok) throw new Error();
                        const data = await res.json();
                        this.$refs.list.insertAdjacentHTML('afterbegin', data.html);
                        this.count = data.comments_count;
                        this.content = '';
                        this.autoGrow(this.$refs.input);
                    } catch {
                        this.error = @js(__('Failed to post comment.'));
                    } finally {
                        this.submitting = false;
                    }
                },
                async loadMore() {
                    if (! this.nextUrl || this.loadingMore) return;
                    this.loadingMore = true;
                    try {
                        const res = await fetch(this.nextUrl, { headers: { 'Accept': 'application/json' } });
                        const data = await res.json();
                        this.$refs.list.insertAdjacentHTML('beforeend', data.html);
                        this.nextUrl = data.nextUrl;
                    } finally {
                        this.loadingMore = false;
                    }
                },
                async remove(url, el) {
                    if (! confirm(@js(__('Are you sure you want to delete this comment?')))) return;
                    try {
                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': @js(csrf_token()),
                                'Accept': 'application/json',
                            },
                        });
                        if (! res.ok) throw new Error();
                        const data = await res.json();
                        el.remove();
                        this.count = data.comments_count;
                    } catch {
                        alert(@js(__('Failed to delete comment.')));
                    }
                },
            }"
            class="mt-4 bg-white p-4 rounded-md"
        >
            <h2 class="text-sm font-semibold text-gray-700 mb-3">
                {{ __('Comments') }} (<span x-text="count">{{ (int) $post->comments_count }}</span>)
            </h2>

            @auth
                <form @submit.prevent="submit" class="mb-4 flex items-start gap-2">
                    <textarea
                        x-ref="input"
                        x-model="content"
                        x-on:input="autoGrow($el)"
                        rows="1"
                        maxlength="1000"
                        aria-label="{{ __('Add a comment...') }}"
                        placeholder="{{ __('Add a comment...') }}"
                        class="flex-1 resize-none overflow-hidden rounded-md border-gray-300 text-sm focus:border-gray-900 focus:ring-0"
                    ></textarea>
                    <button
                        type="submit"
                        :disabled="submitting || ! content.trim()"
                        class="shrink-0 px-4 py-2 rounded-md bg-gray-900 text-white text-sm font-medium hover:bg-gray-700 disabled:opacity-50 transition"
                    >
                        {{ __('Comment') }}
                    </button>
                </form>
                <p x-show="error" x-cloak x-text="error" class="mb-3 text-sm text-red-600" role="alert"></p>
            @else
                <p class="mb-4 text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="font-medium text-gray-900 hover:underline">{{ __('Log in') }}</a>
                    {{ __('to leave a comment.') }}
                </p>
            @endauth

            <ul x-ref="list" class="flex flex-col gap-3">
                @include('posts._comment-list-items', ['comments' => $comments])
            </ul>

            <p x-show="count === 0" x-cloak class="text-sm text-gray-400">{{ __('No comments yet') }}</p>

            <div class="mt-3 flex justify-center">
                <button
                    type="button"
                    x-show="nextUrl"
                    x-cloak
                    :disabled="loadingMore"
                    @click="loadMore"
                    class="px-4 py-1.5 text-sm font-medium rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200 disabled:opacity-60 transition"
                >
                    <span x-show="!loadingMore">{{ __('Load more') }}</span>
                    <span x-show="loadingMore" x-cloak>{{ __('Loading...') }}</span>
                </button>
            </div>
        </section>
    </div>
</x-app-layout>
