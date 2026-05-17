<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <div
        x-data="{
            photoUrl: @js($user->profile_photo_path ? Storage::url($user->profile_photo_path) : null),
            uploading: false,
            error: null,
            async upload(event) {
                const file = event.target.files[0];
                if (! file) return;

                this.error = null;
                this.uploading = true;
                const previewUrl = URL.createObjectURL(file);
                const previousUrl = this.photoUrl;
                this.photoUrl = previewUrl;

                const formData = new FormData();
                formData.append('profile_photo', file);

                try {
                    const response = await fetch('{{ route('profile.photo.update') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (! response.ok) {
                        const data = await response.json().catch(() => ({}));
                        this.error = (data.errors && data.errors.profile_photo && data.errors.profile_photo[0]) || data.message || '{{ __('Failed to upload the image.') }}';
                        this.photoUrl = previousUrl;
                        return;
                    }

                    const data = await response.json();
                    this.photoUrl = data.url;
                } catch (e) {
                    this.error = '{{ __('Failed to upload the image.') }}';
                    this.photoUrl = previousUrl;
                } finally {
                    URL.revokeObjectURL(previewUrl);
                    this.uploading = false;
                    event.target.value = '';
                }
            }
        }"
        class="mt-6"
    >
        <x-input-label for="profile_photo" :value="__('Profile Photo')" />

        <div class="mt-2 flex flex-col items-center">
            <label for="profile_photo" class="relative group cursor-pointer rounded-full focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                <template x-if="photoUrl">
                    <img :src="photoUrl" alt="{{ __('Profile Photo') }}" class="w-24 h-24 rounded-full object-cover border border-gray-200">
                </template>
                <template x-if="! photoUrl">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 96 96"
                        role="img"
                        aria-label="{{ __('Profile Photo') }}"
                        class="w-24 h-24 rounded-full border border-gray-200 bg-gray-100 text-gray-400"
                    >
                        <circle cx="48" cy="31" r="16" fill="currentColor" />
                        <path d="M16 81c0-17.673 14.327-32 32-32s32 14.327 32 32z" fill="currentColor" />
                    </svg>
                </template>

                <div class="absolute inset-0 rounded-full bg-black/40 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 flex items-center justify-center text-white transition" x-show="! uploading">
                    <span class="text-xs font-medium">{{ __('Change') }}</span>
                </div>

                <div class="absolute inset-0 rounded-full bg-black/50 flex items-center justify-center" x-show="uploading" x-cloak>
                    <svg class="w-6 h-6 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </div>

                <span class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-white border border-gray-200 shadow flex items-center justify-center text-gray-700" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h2.586a1 1 0 00.707-.293l1.414-1.414A1 1 0 0110.414 5h3.172a1 1 0 01.707.293l1.414 1.414A1 1 0 0016.414 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>

                <input
                    id="profile_photo"
                    name="profile_photo"
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    x-bind:disabled="uploading"
                    x-on:change="upload($event)"
                />
            </label>
        </div>

        <p class="mt-2 text-center text-sm text-red-600" x-show="error" x-text="error" x-cloak></p>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required autocomplete="username" />
            <p class="mt-1 text-xs text-gray-500">{{ __('3-20 lowercase letters, numbers, or underscores') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div x-data="{ bio: @js(old('bio', $user->bio ?? '')) }">
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio"
                      name="bio"
                      rows="3"
                      maxlength="160"
                      x-model="bio"
                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                      placeholder="{{ __('A short description about yourself (up to 160 characters)') }}"></textarea>
            <p class="mt-1 text-xs text-gray-500 text-right">
                <span x-text="bio.length"></span> / 160
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex flex-col items-center gap-4">
            <x-primary-button class="w-[280px] text-center">{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
