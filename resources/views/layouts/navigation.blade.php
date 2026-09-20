<nav class="bg-white border-b border-gray-100 sticky top-0 z-40">
    <div class="max-w-xl mx-auto px-4 sm:px-0">
        <div class="flex justify-between items-center h-16">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900" aria-label="{{ config('app.name') }}">
                <x-application-logo class="w-7 h-7" />
            </a>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('posts.create') }}" class="text-gray-700 hover:text-gray-900" aria-label="{{ __('New post') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75a2.25 2.25 0 0 1 2.25-2.25h10.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25H6.75a2.25 2.25 0 0 1-2.25-2.25V6.75Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v7.5M8.25 12h7.5" />
                        </svg>
                    </a>

                    <div
                        x-data="{
                            open: false,
                            unread: {{ (int) $unreadNotificationCount }},
                            async toggle() {
                                this.open = ! this.open;
                                if (this.open && this.unread > 0) {
                                    try {
                                        await fetch(@js(route('notifications.read')), {
                                            method: 'POST',
                                            headers: { 'X-CSRF-TOKEN': @js(csrf_token()), 'Accept': 'application/json' },
                                        });
                                        this.unread = 0;
                                    } catch {}
                                }
                            },
                        }"
                        @keydown.escape.window="open = false"
                        class="relative"
                    >
                        <button type="button" @click="toggle" class="relative flex text-gray-700 hover:text-gray-900" aria-label="{{ __('Notifications') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                            <span
                                x-show="unread > 0"
                                x-cloak
                                x-text="unread > 9 ? '9+' : unread"
                                class="absolute -top-1 -right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold leading-none text-white"
                            ></span>
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            @click.outside="open = false"
                            x-transition
                            class="absolute right-0 mt-2 w-80 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 z-50 overflow-hidden"
                        >
                            <div class="px-4 py-2 border-b border-gray-100 text-sm font-semibold text-gray-700">{{ __('Notifications') }}</div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse ($recentNotifications as $notification)
                                    <a
                                        href="{{ $notification->data['url'] ?? route('posts.index') }}"
                                        @class(['block px-4 py-3 border-b border-gray-50 hover:bg-gray-50', 'bg-yellow-50' => is_null($notification->read_at)])
                                    >
                                        <p class="text-sm text-gray-800">
                                            <span class="font-semibold text-gray-900">{{ $notification->data['commenter_username'] ?? '' }}</span>
                                            {{ __('commented on your post:') }}
                                        </p>
                                        <p class="text-sm text-gray-600 truncate">{{ $notification->data['excerpt'] ?? '' }}</p>
                                        <p class="mt-0.5 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                    </a>
                                @empty
                                    <p class="px-4 py-6 text-center text-sm text-gray-400">{{ __('No notifications yet') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="rounded-full focus:outline-none" aria-label="{{ Auth::user()->username }}">
                                <x-user-avatar :user="Auth::user()" />
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('users.show', Auth::user())">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Edit Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">{{ __('LOGIN') }}</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold text-sm rounded-md transition-colors duration-200">{{ __('CREATE ACCOUNT') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
