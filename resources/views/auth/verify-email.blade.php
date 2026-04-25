<x-guest-layout>
			@if (session('status') == 'verification-link-sent')
					<x-auth-session-status class="mb-4" :status="__('Verification email has been resent. Please check your inbox.')" />
			@endif

		<div class="mb-4">
				<h2 class="text-lg font-bold text-gray-900 mb-1">
						{{ __('Verify your email') }}
				</h2>
				<p class="text-xs text-gray-500 leading-relaxed">
						{{ __('We have sent a verification email to your registered email address.') }}<br>
						{{ __('Please click the link in the email to complete the verification.') }}
				</p>
				<p class="text-xs text-gray-500 leading-relaxed mt-2">
						{{ __('If you did not receive the email, you can resend it from the button below.') }}
				</p>
		</div>

		<form method="POST" action="{{ route('verification.send') }}" class="pt-8">
				@csrf
				<x-primary-button>{{ __('RESEND VERIFICATION EMAIL') }}</x-primary-button>
		</form>

		<form method="POST" action="{{ route('logout') }}" class="text-center pt-2">
				@csrf
				<button type="submit"
								class="text-xs text-gray-500 hover:text-gray-900 underline">
						{{ __('Log Out') }}
				</button>
		</form>
</x-guest-layout>