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

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
    </style>
</head>

<body class="font-sans antialiased">

    <!-- PAGE WRAPPER -->
    <div class="min-h-screen flex items-center justify-center
                bg-gradient-to-br from-orange-600 via-orange-400 to-amber-300
                relative overflow-hidden">

        <!-- Decorative glow blobs (Bell-style) -->
        <div class="absolute -top-40 -right-40 w-[32rem] h-[32rem]
                    bg-orange-500 opacity-20 rounded-full blur-3xl"></div>

        <div class="absolute -bottom-40 -left-40 w-[32rem] h-[32rem]
                    bg-amber-400 opacity-20 rounded-full blur-3xl"></div>

        <!-- CONTENT -->
        <div class="relative z-10 w-full px-4">
            {{ $slot }}
        </div>

    </div>

    @livewireScripts
</body>

</html>
