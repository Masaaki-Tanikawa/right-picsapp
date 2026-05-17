<x-app-layout>
    <x-slot name="title">{{ __('Edit Profile') }}</x-slot>

    <div class="py-12">
        <div class="max-w-[640px] mx-auto px-4 space-y-6">
            @if (session('status') === 'profile-updated')
                <x-auth-session-status :status="__('Profile information saved.')" />
            @elseif (session('status') === 'password-updated')
                <x-auth-session-status :status="__('Password saved.')" />
            @endif

            <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
