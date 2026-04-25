@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border-0 border-b border-gray-300 pb-2 text-gray-900 text-base focus:ring-0 focus:border-gray-900 focus:outline-none bg-transparent placeholder:text-gray-400']) }}>