<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-3">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Account')" />
            <x-text-input id="name"
                          type="text"
                          name="name"
                          :value="old('name')"
                          :placeholder="__('Enter your account name')"
                          required autofocus />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                          type="email"
                          name="email"
                          :value="old('email')"
                          :placeholder="__('Enter your email')"
                          required />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password"
                          type="password"
                          name="password"
                          :placeholder="__('At least 8 characters')"
                          required
                          autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation"
                          type="password"
                          name="password_confirmation"
                          :placeholder="__('Re-enter your password')"
                          required
                          autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <div class="pt-4">
            <x-primary-button>{{ __('CREATE ACCOUNT') }}</x-primary-button>
        </div>

        <div class="text-center pt-2">
            <a href="{{ route('login') }}"
               class="text-xs text-gray-500 hover:text-gray-900 underline">
                {{ __('Already registered?') }}
            </a>
        </div>
    </form>
</x-guest-layout>