<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-lg font-bold text-gray-900 mb-1">
            {{ __('Forgot your password?') }}
        </h2>
        <p class="text-xs text-gray-500 leading-relaxed">
            {{ __('Please enter the email address you registered.') }}<br>
            {{ __('We will send you a password reset link.') }}
        </p>
    </div>

    @if (session('status'))
        <div class="bg-green-100 text-green-800 text-sm p-3 text-center mb-4">
            {{ session('status') }}
        </div>
    @endif
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email"
                          type="email"
                          name="email"
                          :value="old('email')"
                          :placeholder="__('Enter your email')"
                          required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="pt-8">
            <x-primary-button>{{ __('SEND RESET LINK') }}</x-primary-button>
        </div>

        <div class="text-center pt-2">
            <a href="{{ route('login') }}"
               class="text-xs text-gray-500 hover:text-gray-900 underline">
                {{ __('Back to login') }}
            </a>
        </div>
    </form>
</x-guest-layout>