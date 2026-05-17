<x-app-layout>
    <x-slot name="title">{{ $user->name }}</x-slot>

    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-center gap-6">
                    @if ($user->profile_photo_path)
                        <img src="{{ Storage::url($user->profile_photo_path) }}"
                             alt="{{ $user->name }}"
                             class="w-24 h-24 rounded-full object-cover border border-gray-200">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 96 96"
                             role="img"
                             aria-label="{{ $user->name }}"
                             class="w-24 h-24 rounded-full border border-gray-200 bg-gray-100 text-gray-400">
                            <circle cx="48" cy="31" r="16" fill="currentColor" />
                            <path d="M16 81c0-17.673 14.327-32 32-32s32 14.327 32 32z" fill="currentColor" />
                        </svg>
                    @endif

                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h1>
                        <p class="text-sm text-gray-500">{{ '@'.$user->username }}</p>
                    </div>
                </div>

                @if ($user->bio)
                    <p class="mt-4 text-sm text-gray-700 whitespace-pre-wrap text-center">{{ $user->bio }}</p>
                @endif

                <div class="mt-6 flex justify-center">
                    @if (auth()->check() && auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}"
                           class="inline-flex items-center px-6 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200 transition">
                            {{ __('Edit Profile') }}
                        </a>
                    @elseif (auth()->check())
                        <button type="button"
                                disabled
                                class="inline-flex items-center px-6 py-2 text-sm font-medium rounded-md bg-indigo-600 text-white opacity-60 cursor-not-allowed">
                            {{ __('Follow') }}
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center px-6 py-2 text-sm font-medium rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition">
                            {{ __('Follow') }}
                        </a>
                    @endif
                </div>

                <div class="mt-8 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-lg font-semibold text-gray-900">0</p>
                        <p class="text-xs text-gray-500">{{ __('Posts') }}</p>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">0</p>
                        <p class="text-xs text-gray-500">{{ __('Followers') }}</p>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-900">0</p>
                        <p class="text-xs text-gray-500">{{ __('Following') }}</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 py-12 flex flex-col items-center text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.5"
                     class="w-12 h-12"
                     aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor" />
                    <path d="M21 15l-5-5L5 21" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-3 text-sm">{{ __('No posts yet') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
