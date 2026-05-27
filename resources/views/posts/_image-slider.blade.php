@php
    $count = $images->count();
    $resolve = fn ($path) => Str::startsWith($path, ['http://', 'https://']) ? $path : asset('storage/'.$path);
    $extractDimensions = function ($path) {
        if (preg_match('#picsum\.photos/(?:seed/[^/]+/)?(\d+)/(\d+)#', $path, $m)) {
            return ['width' => (int) $m[1], 'height' => (int) $m[2]];
        }

        return null;
    };
    $loading = $loading ?? 'lazy';
    $aspectMode = $aspectMode ?? 'square';
    $altPrefix = $altPrefix ?? '';
    $href = $href ?? null;

    $aspect = 1.0;
    if ($aspectMode === 'first' && $count > 0) {
        $first = $extractDimensions($images[0]->image_path);
        if ($first) {
            $aspect = round($first['height'] / $first['width'], 4);
        }
    }
@endphp

@if ($count > 0)
    <div
        x-data="{
            current: 0,
            count: {{ $count }},
            go(i) { this.$refs.slider.scrollTo({ left: i * this.$refs.slider.clientWidth, behavior: 'smooth' }); }
        }"
        class="relative"
    >
        <div
            x-ref="slider"
            style="aspect-ratio: 1 / {{ $aspect }};"
            @scroll.debounce.50ms="current = Math.round($refs.slider.scrollLeft / $refs.slider.clientWidth)"
            class="flex snap-x snap-mandatory overflow-x-auto scroll-smooth rounded-md [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
        >
            @foreach ($images as $index => $image)
                @php($dim = $extractDimensions($image->image_path))
                @if ($href)<a href="{{ $href }}" class="block w-full h-full shrink-0 snap-center">@endif
                    <img
                        src="{{ $resolve($image->image_path) }}"
                        alt="{{ $count > 1 ? $altPrefix.' '.__('Image :number', ['number' => $index + 1]) : $altPrefix }}"
                        loading="{{ $loading }}"
                        @if ($dim) width="{{ $dim['width'] }}" height="{{ $dim['height'] }}" @endif
                        class="{{ $href ? 'w-full h-full object-cover' : 'w-full h-full shrink-0 snap-center object-cover' }}"
                    >
                @if ($href)</a>@endif
            @endforeach
        </div>

        @if ($count > 1)
            <button
                type="button"
                x-show="current > 0"
                x-cloak
                @click="go(current - 1)"
                class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60"
                aria-label="{{ __('Previous image') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </button>
            <button
                type="button"
                x-show="current < count - 1"
                x-cloak
                @click="go(current + 1)"
                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60"
                aria-label="{{ __('Next image') }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            <div class="flex items-center justify-center gap-1.5 mt-2">
                <template x-for="i in count" :key="i">
                    <button
                        type="button"
                        @click="go(i - 1)"
                        class="block w-1.5 h-1.5 rounded-full transition-colors"
                        :class="(i - 1) === current ? 'bg-sky-500' : 'bg-gray-300'"
                        :aria-label="@js(__('Go to image :number')).replace(':number', i)"
                    ></button>
                </template>
            </div>
        @endif
    </div>
@endif
