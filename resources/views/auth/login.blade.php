<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                          type="email"
                          name="email"
                          :value="old('email')"
                          :placeholder="__('Enter your email')"
                          required autofocus
                          autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Password --}}
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password"
                          type="password"
                          name="password"
                          :placeholder="__('Enter your password')"
                          required
                          autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Forgot password? --}}
        <div class="flex justify-end">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-xs text-gray-500 hover:text-gray-900">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember"
                  class="w-4 h-4 border-gray-300 rounded text-gray-900 focus:ring-0">
            <label for="remember_me" class="ms-2 text-xs text-gray-500">
                {{ __('Remember me') }}
            </label>
        </div>

        <div class="pt-8">
            <x-primary-button>{{ __('LOGIN') }}</x-primary-button>
        </div>

        <div class="text-center pt-4">
            <a href="{{ route('register') }}"
               class="text-xs text-gray-500 hover:text-gray-900 underline">
                {{ __("Don't have an account?") }}
            </a>
        </div>
    </form>
</x-guest-layout>