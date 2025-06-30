<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Spin Wheel Game</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
                <body class="bg-gradient-to-br from-blue-100 to-indigo-200 min-h-screen font-sans antialiased">

    <div class="relative min-h-screen flex flex-col items-center justify-start py-6">
        <!-- Header -->
        <div class="w-full max-w-7xl px-6">
            <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                <div class="flex lg:justify-center lg:col-start-2">
                    <svg class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]" viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Your full SVG logo code here -->
                    </svg>
                </div>
                @if (Route::has('login'))
                    <livewire:welcome.navigation />
                @endif
            </header>
        </div>

        <!-- Hero Section -->
        <div class="text-center px-4 max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6"> Welcome to Spin & Win!</h1>
            <p class="text-lg md:text-xl text-gray-700 mb-8">
                Spin the colorful wheel, test your luck, and win exciting rewards. Sign in to get started!
            </p>

            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-full shadow-lg text-lg transition-all">
                     Login to Start Playing
                </a>
            @endif
        </div>

        <!-- Image -->
        <div class="mt-10">
        </div>

        <!-- Footer -->
        <footer class="mt-16 text-sm text-gray-600 text-center">
            &copy; {{ date('Y') }} Spin Wheel Game. All rights reserved.
        </footer>
    </div>

</body>
</html>
