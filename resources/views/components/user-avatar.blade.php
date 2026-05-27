@props(['user'])

@if ($user->profile_photo_url)
    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->username }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-orange-400 p-0.5">
@else
    <div class="w-10 h-10 rounded-full bg-gray-200 ring-2 ring-orange-400 p-0.5 flex items-center justify-center text-sm font-semibold text-gray-600">
        {{ mb_strtoupper(mb_substr($user->username, 0, 1)) }}
    </div>
@endif
