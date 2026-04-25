<x-guest-layout>
    <div class="mb-3">
        <h2 class="text-lg font-bold text-gray-900 mb-1">
            {{ __('Reset Password') }}
        </h2>
        <p class="text-xs text-gray-500 leading-relaxed">
            {{ __('Please enter a new password.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-3">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                          type="email"
                          name="email"
                          :value="old('email', $request->email)"
                          :placeholder="__('Enter your email')"
                          required autofocus
                          autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password"
                          type="password"
                          name="password"
                          :placeholder="__('At least 8 characters')"
                          required
                          autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

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

        <div class="pt-6">
            <x-primary-button>{{ __('RESET PASSWORD') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>