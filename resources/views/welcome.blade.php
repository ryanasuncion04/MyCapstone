<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ilocos Norte Farm Produce GIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        #map {
            position: fixed;
            inset: 0;
            z-index: 0;
        }
    </style>
</head>

<body class="relative h-full w-full overflow-hidden">

    {{-- MAP (BACKGROUND) --}}
    <div id="map"></div>

    {{-- NAVBAR OVERLAY --}}
    <header class="absolute top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-primary-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

            <a href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-primary-600 to-primary-700 flex items-center justify-center shadow-md">
                    <img src="{{ asset('storage/logo/mylogo.png') }}" class="h-8 w-8 object-contain" alt="Logo">
                </div>

                <div class="leading-tight">
                    <p class="font-bold text-primary-700">
                        Farm Connect
                    </p>
                    <p class="text-xs text-zinc-600">
                        Agricultural Marketplace
                    </p>
                </div>
            </a>

            <nav class="flex gap-3">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 font-medium transition-all shadow-md hover:shadow-lg">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-5 py-2 text-sm rounded-lg border-2 border-primary-600 text-primary-600 hover:bg-primary-50 font-medium transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-5 py-2 text-sm rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 font-medium transition-all shadow-md hover:shadow-lg">
                        Sign Up
                    </a>
                @endauth
            </nav>

        </div>
    </header>

    {{-- INFO PANEL OVERLAY --}}
    <section class="absolute bottom-8 left-8 z-40 max-w-md">
        
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-8 border border-white/50">

            <div class="space-y-1 mb-4">
                <h1 class="text-2xl font-bold text-primary-700">
                    Farm Connect
                </h1>
                <p class="text-sm text-primary-600 font-medium">
                    Connecting Farmers with Consumers
                </p>
            </div>

            <p class="text-sm text-zinc-700 leading-relaxed mb-4">
                Discover fresh produce directly from Ilocos Norte farmers. Browse, order, and support local agriculture.
            </p>

            <ul class="space-y-2.5 text-sm text-zinc-700 mb-6">
                <li class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Fresh produce marketplace
                </li>
                <li class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Location-based farm discovery
                </li>
                <li class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Direct farmer ratings & reviews
                </li>
                <li class="flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Easy preordering system
                </li>
            </ul>

            <a href="{{ route('login') }}"
                class="inline-flex items-center gap-2 w-full justify-center px-6 py-3 text-sm rounded-lg bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 font-semibold transition-all shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
                Get Started
            </a>

        </div>

    </section>

    {{-- MAP SCRIPT --}}
    <script>
        const map = L.map('map', {
            zoomControl: false
        }).setView([18.1647, 120.7116], 9);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);
    </script>

</body>

</html>
