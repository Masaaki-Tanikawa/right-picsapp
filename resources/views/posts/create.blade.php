<x-app-layout>
    <div class="max-w-xl mx-auto py-4 px-4 sm:px-0" x-data="postForm()">
        <h1 class="text-lg font-semibold text-gray-900 mb-4">{{ __('New post') }}</h1>

        <div x-show="unsupported" x-cloak class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-md text-sm" role="alert">
            <p class="font-semibold mb-1">{{ __('Your browser is not supported') }}</p>
            <p>{{ __('Multi-image upload requires a modern browser. Please update Safari, Chrome, or Firefox to the latest version and try again.') }}</p>
        </div>

        <form
            x-show="!unsupported"
            x-cloak
            action="{{ route('posts.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="bg-white p-4 rounded-md flex flex-col gap-6"
            x-on:submit="syncFileInput()"
        >
            @csrf

            <div>
                <x-input-label :value="__('Images')" />

                <label
                    for="post-images"
                    class="mt-1 flex flex-col items-center justify-center gap-2 px-4 py-8 border-2 border-dashed border-gray-300 rounded-md cursor-pointer hover:border-gray-400 hover:bg-gray-50 transition focus-within:ring-2 focus-within:ring-indigo-500"
                    x-on:dragover.prevent="dragOver = true"
                    x-on:dragleave.prevent="dragOver = false"
                    x-on:drop.prevent="handleDrop($event)"
                    :class="{ 'border-indigo-500 bg-indigo-50': dragOver }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                    <span class="text-sm text-gray-600">{{ __('Drag images here, or click to select') }}</span>
                    <span class="text-xs text-gray-500">{{ __('Up to :max images, :size each', ['max' => 10, 'size' => '2MB']) }}</span>

                    <input
                        id="post-images"
                        type="file"
                        name="images[]"
                        accept="image/*"
                        multiple
                        class="sr-only"
                        x-ref="fileInput"
                        x-on:change="addFiles($event.target.files); $event.target.value = ''"
                    />
                </label>

                <x-input-error class="mt-2" :messages="$errors->get('images')" />
                <x-input-error class="mt-2" :messages="$errors->get('images.*')" />

                <p x-show="rejectedOversize > 0" x-cloak class="mt-2 text-xs text-red-600">
                    {{ __('Image size is too large. (Please select images :size or smaller)', ['size' => '2MB']) }}
                </p>

                <template x-if="images.length > 0">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex flex-col text-xs text-gray-600">
                                <span x-text="`${images.length} / ${maxImages}`"></span>
                                <span :class="overTotalLimit ? 'text-red-600 font-semibold' : 'text-gray-500'" x-text="`{{ __('Total') }}: ${totalMB} MB / ${maxTotalMB} MB`"></span>
                            </div>
                            <button type="button" x-on:click="clearAll()" class="text-xs text-gray-500 hover:text-gray-800 underline">
                                {{ __('Clear all') }}
                            </button>
                        </div>
                        <p x-show="overTotalLimit" x-cloak class="mb-2 text-xs text-red-600">
                            {{ __('Total size exceeds the upload limit. Remove some images.') }}
                        </p>
                        <ul class="grid grid-cols-3 gap-2">
                            <template x-for="image in images" :key="image.id">
                                <li class="relative aspect-square overflow-hidden rounded-md bg-gray-100 border border-gray-200">
                                    <img :src="image.url" alt="" class="w-full h-full object-cover" />
                                    <button
                                        type="button"
                                        x-on:click="removeImage(image.id)"
                                        aria-label="{{ __('Remove image') }}"
                                        class="absolute top-1 right-1 w-6 h-6 rounded-full bg-black/60 text-white flex items-center justify-center hover:bg-black/80 focus:outline-none focus:ring-2 focus:ring-white"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor" class="w-3.5 h-3.5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <div>
                <x-input-label for="title" :value="__('Title') . ' (' . __('Optional') . ')'" />
                <x-text-input
                    id="title"
                    name="title"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('title')"
                    maxlength="255"
                />
                <x-input-error class="mt-2" :messages="$errors->get('title')" />
            </div>

            <div x-data="{ content: @js(old('content', '')) }">
                <x-input-label for="content" :value="__('Content') . ' (' . __('Optional') . ')'" />
                <textarea
                    id="content"
                    name="content"
                    rows="5"
                    maxlength="10000"
                    x-model="content"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                ></textarea>
                <p class="mt-1 text-xs text-gray-500 text-right">
                    <span x-text="content.length"></span> / 10000
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('content')" />
            </div>

            <div class="flex justify-center">
                <x-primary-button
                    class="w-[280px] text-center disabled:opacity-50 disabled:cursor-not-allowed"
                    x-bind:disabled="images.length === 0 || overTotalLimit"
                >
                    {{ __('Submit post') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        function postForm() {
            const newId = () => (typeof crypto !== 'undefined' && crypto.randomUUID)
                ? crypto.randomUUID()
                : `${Date.now()}-${Math.random().toString(36).slice(2)}`;

            let dataTransferSupported = false;
            try {
                new DataTransfer();
                dataTransferSupported = true;
            } catch (e) {
                // Safari < 14.5 などの旧ブラウザでは new DataTransfer() が throw する
            }

            return {
                images: [],
                dragOver: false,
                maxImages: 10,
                maxImageBytes: 2 * 1024 * 1024,
                maxTotalMB: 24,
                rejectedOversize: 0,
                unsupported: ! dataTransferSupported,
                get totalBytes() {
                    return this.images.reduce((sum, image) => sum + image.file.size, 0);
                },
                get totalMB() {
                    return (this.totalBytes / 1024 / 1024).toFixed(1);
                },
                get overTotalLimit() {
                    return this.totalBytes > this.maxTotalMB * 1024 * 1024;
                },
                addFiles(fileList) {
                    this.rejectedOversize = 0;
                    const slots = this.maxImages - this.images.length;
                    const files = Array.from(fileList).slice(0, slots);
                    for (const file of files) {
                        if (! file.type.startsWith('image/')) continue;
                        if (file.size > this.maxImageBytes) {
                            this.rejectedOversize++;
                            continue;
                        }
                        this.images.push({
                            id: newId(),
                            file,
                            url: URL.createObjectURL(file),
                        });
                    }
                },
                handleDrop(event) {
                    this.dragOver = false;
                    if (event.dataTransfer?.files) {
                        this.addFiles(event.dataTransfer.files);
                    }
                },
                removeImage(id) {
                    const target = this.images.find((image) => image.id === id);
                    if (target) URL.revokeObjectURL(target.url);
                    this.images = this.images.filter((image) => image.id !== id);
                },
                clearAll() {
                    for (const image of this.images) URL.revokeObjectURL(image.url);
                    this.images = [];
                },
                syncFileInput() {
                    const dataTransfer = new DataTransfer();
                    for (const image of this.images) dataTransfer.items.add(image.file);
                    this.$refs.fileInput.files = dataTransfer.files;
                },
            };
        }
    </script>
</x-app-layout>
