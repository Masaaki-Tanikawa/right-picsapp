<x-guest-layout>
		<div class="mb-4">
				<h2 class="text-lg font-bold text-gray-900 mb-1">
						{{ __('Confirm Password') }}
				</h2>
				<p class="text-xs text-gray-500 leading-relaxed">
						{{ __('For your security, please enter your password again before important operations.') }}
				</p>
		</div>

		<form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
				@csrf

				<div>
						<x-input-label for="password" :value="__('Password')" />
						<x-text-input id="password"
													type="password"
													name="password"
													:placeholder="__('Enter your password')"
													required autofocus
													autocomplete="current-password" />
						<x-input-error :messages="$errors->get('password')" />
				</div>

				<div class="pt-8">
						<x-primary-button>{{ __('CONFIRM') }}</x-primary-button>
				</div>
		</form>
</x-guest-layout>