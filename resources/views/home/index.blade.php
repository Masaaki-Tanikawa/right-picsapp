<x-app-layout>
		<div class="max-w-7xl mx-auto px-4 py-12">
				<p class="text-gray-500">This is a dummy top page.</p>

				<form method="POST" action="{{ route('logout') }}" class="mt-6">
						@csrf
						<button type="submit" class="text-red-500 underline">
								Log out
						</button>
				</form>
		</div>
</x-app-layout>