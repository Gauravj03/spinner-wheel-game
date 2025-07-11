<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Spin Wheel Game') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
         @livewireStyles
    </head>
    <body class="font-inter antialiased">
        <div class="min-h-screen bg-gray-100">
            <livewire:layout.navigation />
           
            <!-- Page Content -->
            <!-- <main>
               
            </main> -->
            @yield('content')
            @livewireScripts
            @stack('scripts')
        </div>
    </body>
</html>
