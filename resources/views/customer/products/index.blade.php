<x-layouts.app :title="__('Farm Produces Ilocos Norte')">

    <div class="space-y-6 p-6 max-w-7xl mx-auto">

        <!-- Header Section -->
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">
                Discover Farm Fresh Produces
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400">
                Explore produce directly from local Ilocos Norte farmers. Browse, filter, and place your orders.
            </p>
        </div>

        <!-- Filter Section -->
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-primary-200 dark:border-primary-900 p-4 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="produceFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Produce Type
                    </label>
                    <select id="produceFilter"
                        class="w-full px-4 py-2.5 rounded-lg border border-primary-300 dark:border-primary-700 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        <option value="">All Produce</option>
                        @foreach ($products as $product)
                            <option value="{{ $product }}">{{ $product }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="municipalityFilter"
                        class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Municipality
                    </label>
                    <select id="municipalityFilter"
                        class="w-full px-4 py-2.5 rounded-lg border border-primary-300 dark:border-primary-700 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all">
                        <option value="">All Municipalities</option>
                    </select>
                </div>

                <div>
                    <label for="farmerFilter" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Farmer Name
                    </label>
                    <input type="text" id="farmerFilter" placeholder="Search farmer..."
                        class="w-full px-4 py-2.5 rounded-lg border border-primary-300 dark:border-primary-700 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-500 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all" />
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="rounded-xl overflow-hidden shadow-md border border-primary-200 dark:border-primary-900">
            <div wire:ignore id="map" class="w-full h-[70vh] bg-cream-100 dark:bg-zinc-800">
            </div>
        </div>
    </div>

    <script>
        const produces = @json($produces);

        let map;
        let markerLayer;
        let routingControl = null;
        let userLatLng = null;

        function initIlocosProductsMap() {
            const el = document.getElementById('map');
            if (!el || el.dataset.loaded) return;
            el.dataset.loaded = true;

            map = L.map(el).setView([18.1647, 120.7116], 9);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            markerLayer = L.layerGroup().addTo(map);

            map.locate({
                setView: false
            });

            map.on('locationfound', function(e) {
                userLatLng = e.latlng;

                L.circleMarker(userLatLng, {
                    radius: 6,
                    color: '#15803d',
                    fillColor: '#22c55e',
                    fillOpacity: 0.8,
                    weight: 2
                }).addTo(map).bindPopup("📍 You are here");
            });

            fetch('/maps/ilocos-norte.geojson')
                .then(res => res.json())
                .then(data => {

                    L.geoJSON(data, {
                        style: {
                            color: '#15803d',
                            weight: 2,
                            fillOpacity: 0.1
                        }
                    }).addTo(map);

                    populateMunicipalities(data);
                    applyFilters();

                    setTimeout(() => map.invalidateSize(), 300);
                });
        }

        function populateMunicipalities(data) {
            const select = document.getElementById('municipalityFilter');
            const municipalities = new Set();

            data.features.forEach(f => {
                municipalities.add(f.properties.Municipality);
            });

            [...municipalities].sort().forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                select.appendChild(opt);
            });
        }

        function applyFilters() {
            const selectedProduct = document.getElementById('produceFilter').value.toLowerCase();
            const selectedMunicipality = document.getElementById('municipalityFilter').value;
            const farmerSearch = document.getElementById('farmerFilter').value.toLowerCase();

            let filtered = produces;

            if (selectedProduct) {
                filtered = filtered.filter(p =>
                    p.product.toLowerCase() === selectedProduct
                );
            }

            if (selectedMunicipality) {
                filtered = filtered.filter(p =>
                    p.farmer &&
                    p.farmer.municipality === selectedMunicipality
                );
            }

            if (farmerSearch) {
                filtered = filtered.filter(p =>
                    p.farmer &&
                    p.farmer.name.toLowerCase().includes(farmerSearch)
                );
            }

            renderMarkers(filtered);
        }

        // function isValidProduce(p) {
        //     const now = new Date();

        //     if (!p.available_from || !p.available_until) return false;

        //     const from = new Date(String(p.available_from).replace(' ', 'T'));
        //     const until = new Date(String(p.available_until).replace(' ', 'T'));

        //     return (
        //         p.status === 'available' &&
        //         now >= from &&
        //         now <= until
        //     );
        // }
        function isValidProduce(p) {
            const now = new Date();

            if (!p.available_until) return true;

            const until = new Date(
                String(p.available_until).replace(' ', 'T')
            );

            // show current + future listings
            // only hide expired products
            return now <= until;
        }



        function formatDate(dateStr) {
            if (!dateStr) return '—';
            const d = new Date(String(dateStr).replace(' ', 'T'));
            return isNaN(d) ? '—' : d.toLocaleDateString('en-PH');
        }

        function renderMarkers(filteredProduces) {

            markerLayer.clearLayers();

            const valid = filteredProduces.filter(isValidProduce);

            const grouped = {};

            valid.forEach(p => {
                if (!p.farmer?.latitude || !p.farmer?.longitude) return;

                if (!grouped[p.farmer.id]) {
                    grouped[p.farmer.id] = {
                        farmer: p.farmer,
                        produces: []
                    };
                }

                grouped[p.farmer.id].produces.push(p);
            });

            Object.values(grouped).forEach(entry => {

                const farmer = entry.farmer;
                const products = entry.produces;

                // ⭐ calculate rating safely
                const avgRating = farmer.average_rating ?
                    parseFloat(farmer.average_rating).toFixed(1) :
                    '0.0';

                let rows = '';

                // products.forEach(prod => {
                //     const preorderUrl = `/customer/preorders/create/${prod.id}`;

                //     rows += `
                //         <tr class="border-t">
                //             <td class="px-2 py-1">${prod.product}</td>
                //             <td class="px-2 py-1 text-center">${prod.quantity} Kgs</td>
                //             <td class="px-2 py-1 text-right">₱${prod.price}</td>
                //             <td class="px-2 py-1 text-center">
                //                 <a href="${preorderUrl}"
                //                     class="bg-green-600 !text-white text-xs px-3 py-1.5 rounded font-semibold hover:bg-green-700">
                //                     Preorder
                //                 </a>
                //             </td>
                //         </tr>
                //     `;
                // });
                products.forEach(prod => {

                    const preorderUrl = `/customer/preorders/create/${prod.id}`;

                    const now = new Date();

                    const from = new Date(
                        String(prod.available_from)
                            .replace(' ','T')
                    );

                    let statusBadge='';

                    if(now < from){

                        statusBadge=`
                            <span class="inline-block mt-1
                                bg-yellow-100 text-yellow-700
                                text-[10px] px-2 py-1 rounded-full">
                                Upcoming ${formatDate(prod.available_from)}
                            </span>
                        `;

                    }else{

                        statusBadge=`
                            <span class="inline-block mt-1
                                bg-green-100 text-green-700
                                text-[10px] px-2 py-1 rounded-full">
                                Available
                            </span>
                        `;
                    }


                    rows += `
                        <tr class="border-t">
                            <td class="px-2 py-1">
                                <div>
                                    ${prod.product}
                                </div>

                                ${statusBadge}
                            </td>

                            <td class="px-2 py-1 text-center">
                                ${prod.quantity} Kgs
                            </td>

                            <td class="px-2 py-1 text-right">
                                ₱${prod.price}
                            </td>

                            <td class="px-2 py-1 text-center">
                                <a href="${preorderUrl}"
                                    class="bg-green-600 !text-white text-xs px-3 py-1.5 rounded font-semibold hover:bg-green-700">
                                    Preorder
                                </a>
                            </td>
                        </tr>
                    `;
                });


                const farmerImage = farmer.image ?
                    `/storage/${farmer.image}` :
                    `https://ui-avatars.com/api/?name=${encodeURIComponent(farmer.name)}`;

                // ⭐ RATING COMMENTS HTML
                let reviewsHtml = '';

                if (farmer.ratings && farmer.ratings.length > 0) {
                    reviewsHtml = farmer.ratings.slice(0, 2).map(r => `
                        <div class="text-xs border-b py-1">
                            <span class="text-yellow-500">★ ${r.rating}</span>
                            <span class="text-gray-600">
                                ${r.comment ? `"${r.comment}"` : 'No comment'}
                            </span>
                            <div class="text-[10px] text-gray-400">
                                by ${r.customer?.name ?? 'User'}
                            </div>
                        </div>
                    `).join('');
                } else {
                    reviewsHtml = `<div class="text-xs text-gray-400">No reviews yet</div>`;
                }

                const popup = `
                    <div class="w-full max-w-[300px] text-sm bg-white rounded-xl overflow-hidden">
                        <!-- HEADER -->
                        <div class="bg-green-600 p-3 text-white">
                            <div class="flex items-center gap-3">
                                <img src="${farmerImage}"
                                    class="w-14 h-14 rounded-full border-2 border-white object-cover shadow">

                                <div class="min-w-0">
                                    <div class="text-base font-semibold truncate">
                                        ${farmer.name}
                                    </div>

                                    <div class="text-xs text-green-100 truncate">
                                        ${farmer.barangay ?? ''}, ${farmer.municipality ?? ''}
                                    </div>

                                    <div class="text-xs mt-1 text-yellow-200 font-medium">
                                        ⭐ ${avgRating} / 5.0
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CONTENT -->
                        <div class="p-3 space-y-3">

                            <!-- PRODUCTS -->
                            <div>
                                <div class="text-xs font-semibold mb-2 text-gray-700">
                                    Available Products
                                </div>

                                <div class="max-h-36 overflow-y-auto border rounded-lg overflow-x-auto">
                                    <table class="w-full min-w-[280px] text-xs">
                                        <tbody>
                                            ${rows}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- REVIEWS -->
                            <div class="border-t pt-2">
                                <div class="text-xs font-semibold mb-2 text-gray-700">
                                    Recent Reviews
                                </div>

                                <div class="max-h-24 overflow-y-auto space-y-1">
                                    ${reviewsHtml}
                                </div>
                            </div>

                            <!-- ROUTE BUTTON -->
                            <button onclick="routeTo(${farmer.latitude}, ${farmer.longitude})"
                                class="w-full bg-green-600 hover:bg-green-700 text-white text-xs py-2.5 rounded-lg font-medium">
                                📍 Route to Farmer
                            </button>

                        </div>
                    </div>
                    `;

                const icon = L.divIcon({
                    className: '',
                    html: `
                        <div class="relative flex items-center justify-center drop-shadow-lg">
                            <div class="w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center ring-2 ring-primary-600">
                                <div class="w-10 h-10 rounded-full border-2 border-primary-500 overflow-hidden bg-primary-100">
                                    <img src="${products[0].image ? '/storage/' + products[0].image : farmerImage}"
                                         class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="absolute -bottom-1 w-3 h-3 bg-white rotate-45 border-r border-b border-primary-600"></div>
                        </div>
                    `,
                    iconSize: [48, 52],
                    iconAnchor: [24, 52]
                });

                L.marker([farmer.latitude, farmer.longitude], {
                        icon
                    })
                    .addTo(markerLayer)
                    .bindPopup(popup, {
                        maxWidth: 340,
                        minWidth: 320,
                        className: 'custom-farmer-popup'
                    });

            });
        }

        function routeTo(lat, lng) {
            if (!userLatLng) {
                alert("Location not detected yet.");
                return;
            }

            if (routingControl) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: [
                    userLatLng,
                    L.latLng(lat, lng)
                ],
                routeWhileDragging: false,
                show: false
            }).addTo(map);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initIlocosProductsMap();

            document.getElementById('produceFilter').addEventListener('change', applyFilters);
            document.getElementById('municipalityFilter').addEventListener('change', applyFilters);
            document.getElementById('farmerFilter').addEventListener('input', applyFilters);
        });

        document.addEventListener('livewire:navigated', initIlocosProductsMap);
    </script>

    <style>
        .leaflet-popup-content {
            margin: 0 !important;
            width: auto !important;
        }

        .leaflet-popup-content-wrapper {
            padding: 0 !important;
            border-radius: 16px;
            overflow: hidden;
        }

        .custom-farmer-popup .leaflet-popup-tip {
            background: white;
        }
    </style>


</x-layouts.app>
