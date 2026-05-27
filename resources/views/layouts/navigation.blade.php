<nav class="bg-white border-b border-gray-100">
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
