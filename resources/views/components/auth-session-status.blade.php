@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'bg-green-100 text-green-800 text-sm p-3 text-center rounded']) }}>
        {{ $status }}
    </div>
@endif
