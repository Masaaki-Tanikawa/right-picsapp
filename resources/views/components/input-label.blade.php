@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs text-gray-500 mb-1 tracking-wide']) }}>
    {{ $value ?? $slot }}
</label>
