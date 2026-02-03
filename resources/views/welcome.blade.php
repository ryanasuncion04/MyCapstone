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
        html, body {
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
<header class="absolute top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur border-b">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

        <div class="flex items-center gap-3">
            <img src="{{ asset('storage/logo/mylogo.png') }}"
                 class="h-8 w-auto"
                 alt="Logo">

            <div class="leading-tight">
                <p class="font-semibold text-gray-800">
                    Ilocos Norte Farm Produce GIS
                </p>
                <p class="text-xs text-gray-500">
                    Geographic Information System
                </p>
            </div>
        </div>

        <nav class="flex gap-3">
            @auth
                <a href="{{ route('admin.dashboard') }}"
                   class="px-4 py-2 text-sm rounded-md bg-green-700 text-white hover:bg-green-800">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 text-sm rounded-md border border-green-700 text-green-700 hover:bg-green-50">
                    Login
                </a>
            @endauth
        </nav>

    </div>
</header>

{{-- INFO PANEL OVERLAY --}}
<section class="absolute bottom-6 left-6 z-40 max-w-md bg-white rounded-xl shadow-lg p-6">

    <h1 class="text-xl font-semibold text-gray-800">
        Farm Produce Monitoring System
    </h1>

    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
        A GIS-based platform for marketing, visualizing agricultural production,
        spatial distribution, and analytics of farm produce across
        municipalities and barangays in Ilocos Norte.
    </p>

    <ul class="mt-4 space-y-2 text-sm text-gray-700">
        <li>• Alternative Farm Produce Marketing</li>
        <li>• Spatial visualization of produce density</li>
        <li>• Municipality and barangay-level analytics</li>
        <li>• Farmer yield and price monitoring</li>
    </ul>

    <div class="mt-4">
        <a href="{{ route('login') }}"
           class="inline-block px-5 py-2 text-sm rounded-md bg-green-700 text-white hover:bg-green-800">
            Access the System
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

    L.control.zoom({ position: 'bottomright' }).addTo(map);
</script>

</body>
</html>
