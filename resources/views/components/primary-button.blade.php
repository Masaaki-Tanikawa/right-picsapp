<button {{ $attributes->merge(['type' => 'submit','class' => 'w-full py-4 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold text-sm tracking-widest transition-colors duration-200']) }}>
    {{ $slot }}
</button>
