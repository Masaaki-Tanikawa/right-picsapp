<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-white md:bg-gray-100 min-h-screen">
        <div class="w-full max-w-[375px] mx-auto bg-white min-h-screen md:min-h-0 md:my-8 md:shadow-md md:rounded-lg overflow-hidden">
            <div class="px-6 pt-6">
                <div class="relative w-full h-[180px] overflow-hidden">
                    <img src="{{ asset('images/auth-bg.png') }}"
                         alt="Auth Background"
                         class="w-full h-full object-cover">
                </div>
            </div>
            <div class="px-6 pt-6 pb-6">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
