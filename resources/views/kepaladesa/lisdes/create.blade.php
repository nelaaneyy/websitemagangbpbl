@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    <!-- Header Form -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('kepaladesa.lisdes.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-700 flex items-center gap-1 transition">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Lisdes
                </a>
                <span class="text-slate-300">•</span>
                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-extrabold rounded-md uppercase tracking-wider">Program Lisdes ESDM</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Formulir Usulan Listrik Desa (1-to-N Topologi)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Rancang rute penarikan jaringan dari 1 tiang TR eksisting ke rumah-rumah sasaran di Desa {{ auth()->user()->desa }}.</p>
        </div>
    </div>

    <!-- Main Form Container -->
    <form action="{{ route('kepaladesa.lisdes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Kolom Kiri: Form Isian Administrasi & Dokumen (5 Col) -->
            <div class="lg:col-span-5 space-y-5">
                <!-- Identitas Dusun -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-house-laptop text-amber-600"></i> Identitas & Kebutuhan Dusun
                    </h4>

                    <div class="space-y-1">
                        <label for="nama_dusun" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Nama Dusun / RT / RW <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama_dusun" name="nama_dusun" value="{{ old('nama_dusun') }}" required placeholder="Contoh: Dusun III RT 08"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="jumlah_kk" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Sasaran KK <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" min="1" id="jumlah_kk" name="jumlah_kk" value="{{ old('jumlah_kk') }}" required placeholder="Jumlah KK"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>

                        <div class="space-y-1">
                            <label for="estimasi_jarak" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Jarak ke PLN (Meter) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" min="0" id="estimasi_jarak" name="estimasi_jarak" value="{{ old('estimasi_jarak', 0) }}" required readonly
                                   class="w-full px-4 py-2.5 bg-slate-100 border border-slate-300 rounded-xl text-xs font-bold font-mono text-blue-600 outline-none cursor-not-allowed">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label for="keterangan_wilayah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Keterangan Akses & Kebutuhan Wilayah
                        </label>
                        <textarea id="keterangan_wilayah" name="keterangan_wilayah" rows="3" placeholder="Jelaskan kondisi akses jalan, medan, atau urgensi penerangan listrik..."
                                  class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-amber-500 outline-none">{{ old('keterangan_wilayah') }}</textarea>
                    </div>
                </div>

                <!-- Dokumen Pendukung -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                    <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-file-pdf text-rose-600"></i> Dokumen Pendukung Resmi
                    </h4>

                    <div class="space-y-1">
                        <label for="surat_permohonan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Surat Permohonan Resmi Kades (PDF) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" id="surat_permohonan" name="surat_permohonan" accept=".pdf" required
                               class="w-full p-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 cursor-pointer">
                    </div>

                    <div class="space-y-1">
                        <label for="proposal_lisdes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Proposal Rincian Usulan Lisdes (PDF) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" id="proposal_lisdes" name="proposal_lisdes" accept=".pdf" required
                               class="w-full p-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 cursor-pointer">
                    </div>

                    <div class="space-y-1">
                        <label for="foto_wilayah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Foto Kondisi Dusun (Gambar / GPS Camera) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" id="foto_wilayah" name="foto_wilayah" accept="image/*" required
                               class="w-full p-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: 1-to-N Topologi Canvas WebGIS (7 Col) -->
            <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-route text-blue-600"></i> Kanvas Topologi 1-to-N Jaringan Lisdes
                        </h4>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Klik titik sumber tiang PLN, lalu klik titik-titik sasaran rumah penerima.</p>
                    </div>
                    <button type="button" onclick="resetTopology()" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs rounded-xl border border-rose-200 transition cursor-pointer flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left"></i> Reset Kanvas
                    </button>
                </div>

                <!-- Step Indicator Status -->
                <div id="pinStatusBadge" class="px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-900 text-xs rounded-2xl font-bold flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Langkah 1: Klik pada peta untuk menentukan <b>1 Titik Tiang TR / Sumber Listrik Eksisting</b>.</span>
                </div>

                <!-- Live Location Search Bar (Nominatim Geocoding) -->
                <div class="flex gap-2 mb-3">
                    <div class="relative flex-1">
                        <input type="text" id="geocodingQuery" placeholder="Cari nama jalan, dusun, atau desa di Jambi..." 
                               onkeydown="if(event.key==='Enter'){event.preventDefault();searchLocation();}"
                               class="w-full pl-9 pr-4 py-2.5 bg-slate-900 border border-slate-700 text-white rounded-xl text-xs font-semibold focus:outline-none focus:border-amber-400 placeholder:text-slate-500">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                    </div>
                    <button type="button" onclick="searchLocation()" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer shadow-sm">
                        <i class="fa-solid fa-location-arrow"></i> Cari Lokasi
                    </button>
                </div>

                <!-- Map Container (FIX-MAP-COLLAPSE-01 + Google Earth Style) -->
                <div class="rounded-2xl overflow-hidden border border-slate-300 shadow-sm relative w-full mb-3" style="min-height: 420px;">
                    <div id="canvasLisdesMap" style="width: 100%; height: 420px; min-height: 420px; z-index: 1;"></div>
                </div>

                <!-- Live Calculation Counters -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 text-center">
                        <span class="text-[10px] text-slate-400 font-extrabold uppercase block">Titik Sasaran (N)</span>
                        <span id="targetCountLabel" class="text-lg font-black text-slate-900">0 Rumah</span>
                    </div>
                    <div class="bg-blue-50 p-3.5 rounded-2xl border border-blue-200 text-center">
                        <span class="text-[10px] text-blue-600 font-extrabold uppercase block">Total Panjang Bentang</span>
                        <span id="totalDistanceLabel" class="text-lg font-black text-blue-700">0 Meter</span>
                    </div>
                    <div class="bg-emerald-50 p-3.5 rounded-2xl border border-emerald-200 text-center">
                        <span class="text-[10px] text-emerald-600 font-extrabold uppercase block">Estimasi Tiang Baru</span>
                        <span id="estTiangLabel" class="text-lg font-black text-emerald-700">0 Tiang</span>
                    </div>
                </div>

                <!-- Hidden JSON & Coordinates for Controller -->
                <input type="hidden" name="latitude" id="form_latitude">
                <input type="hidden" name="longitude" id="form_longitude">
                <input type="hidden" name="topology_data" id="topology_data">

                <!-- Action Buttons -->
                <div class="pt-2 border-t border-slate-100 flex gap-3">
                    <a href="{{ route('kepaladesa.lisdes.index') }}" class="w-1/3 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-xl transition text-center">
                        Batal
                    </a>
                    <button type="submit" class="w-2/3 py-3.5 bg-slate-900 hover:bg-slate-800 text-amber-400 font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-paper-plane"></i> Simpan & Kirim Usulan Lisdes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <style>
        .distance-badge-ge {
            background: rgba(15, 23, 42, 0.9);
            color: #fde047;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 9999px;
            border: 1px solid rgba(253, 224, 71, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            white-space: nowrap;
        }
        .ge-source-pin {
            cursor: move;
        }
        .ge-target-pin {
            cursor: move;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        let map;
        let sourceNode = null;
        let targetNodes = [];
        let markersGroup, linesGroup, labelsGroup;

        // Custom DivIcon Markers
        const sourceIcon = L.divIcon({
            className: 'ge-source-pin',
            html: `<div class="bg-rose-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-black text-sm shadow-xl border-2 border-white ring-4 ring-rose-500/30">⚡</div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        function getTargetIcon(index) {
            return L.divIcon({
                className: 'ge-target-pin',
                html: `<div class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center font-bold text-[10px] shadow-lg border-2 border-white ring-2 ring-blue-500/30">${index}</div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });
        }

        function startMap() {
            if (map) return;

            const jambiCenter = [-1.6101, 103.6131];

            // Layer Configuration: Google Hybrid + Esri Satellite Fallback
            const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                maxZoom: 20,
                attribution: '&copy; Google Earth Imagery | WebGIS SIPELITA ESDM Jambi'
            });

            const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: '&copy; Esri World Imagery'
            });

            const openStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            });

            map = L.map('canvasLisdesMap', {
                center: jambiCenter,
                zoom: 14,
                layers: [googleHybrid]
            });

            L.control.layers({
                "Google Satelit Hybrid": googleHybrid,
                "Esri World Imagery": esriSatellite,
                "OpenStreetMap Standard": openStreetMap
            }).addTo(map);

            markersGroup = L.layerGroup().addTo(map);
            linesGroup = L.layerGroup().addTo(map);
            labelsGroup = L.layerGroup().addTo(map);

            // Event Lifecycle: Map Click Handling
            map.on('click', function(e) {
                const { lat, lng } = e.latlng;

                if (!sourceNode) {
                    // Step 1: Source Pinning (First Click)
                    sourceNode = { lat: parseFloat(lat.toFixed(7)), lng: parseFloat(lng.toFixed(7)) };
                    document.getElementById('form_latitude').value = sourceNode.lat;
                    document.getElementById('form_longitude').value = sourceNode.lng;

                    const sourceMarker = L.marker([lat, lng], { icon: sourceIcon, draggable: true }).addTo(markersGroup);
                    sourceMarker.bindPopup("<b>Tiang TR Sumber / Pangkal (Dapat Digeser)</b>").openPopup();

                    // Draggable Source Pin Listener
                    sourceMarker.on('drag', function(evt) {
                        const pos = evt.target.getLatLng();
                        sourceNode.lat = parseFloat(pos.lat.toFixed(7));
                        sourceNode.lng = parseFloat(pos.lng.toFixed(7));
                        document.getElementById('form_latitude').value = sourceNode.lat;
                        document.getElementById('form_longitude').value = sourceNode.lng;
                        redrawTopologyConnections();
                    });

                    const badge = document.getElementById('pinStatusBadge');
                    badge.className = "px-4 py-2.5 bg-blue-50 border border-blue-200 text-blue-900 text-xs rounded-2xl font-bold flex items-center gap-2.5";
                    badge.innerHTML = `<span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-ping"></span><span>Langkah 2: Klik titik-titik <b>Rumah Warga Sasaran (N)</b> pada peta satelit. Pin dapat digeser bebas.</span>`;
                } else {
                    // Step 2: Target Pinning (Consecutive Clicks)
                    const targetIdx = targetNodes.length + 1;
                    const newTarget = { id: targetIdx, lat: parseFloat(lat.toFixed(7)), lng: parseFloat(lng.toFixed(7)) };
                    targetNodes.push(newTarget);

                    const targetMarker = L.marker([lat, lng], { icon: getTargetIcon(targetIdx), draggable: true }).addTo(markersGroup);
                    targetMarker.bindPopup(`<b>Sasaran #${targetIdx} (Dapat Digeser)</b>`);

                    // Draggable Target Pin Listener
                    targetMarker.on('drag', function(evt) {
                        const pos = evt.target.getLatLng();
                        targetNodes[targetIdx - 1].lat = parseFloat(pos.lat.toFixed(7));
                        targetNodes[targetIdx - 1].lng = parseFloat(pos.lng.toFixed(7));
                        redrawTopologyConnections();
                    });

                    redrawTopologyConnections();
                }
            });

            // FIX-MAP-COLLAPSE-01: Post-init reflow invalidateSize
            setTimeout(() => { if (map) map.invalidateSize(); }, 250);
            window.addEventListener('resize', () => { if (map) map.invalidateSize(); });
        }

        // Live Location Search Bar (OpenStreetMap Nominatim Geocoding Engine)
        window.searchLocation = function() {
            const query = document.getElementById('geocodingQuery').value;
            if (!query) return;

            const fullQuery = query.toLowerCase().includes('jambi') ? query : query + ', Jambi, Indonesia';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullQuery)}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        if (map) {
                            map.flyTo([lat, lon], 16, { animate: true, duration: 1.5 });
                        }
                    } else {
                        alert('Lokasi tidak ditemukan. Silakan gunakan nama desa atau dusun yang lebih spesifik.');
                    }
                })
                .catch(() => {
                    alert('Gagal menghubungi layanan pencarian lokasi.');
                });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startMap);
        } else {
            startMap();
        }

        // Redraw Topology & Recalculate Geodesic Distance
        function redrawTopologyConnections() {
            linesGroup.clearLayers();
            labelsGroup.clearLayers();

            if (!sourceNode || targetNodes.length === 0) return;

            let totalDist = 0;
            let maxSegment = 0;

            targetNodes.forEach((target) => {
                const originLatLng = L.latLng(sourceNode.lat, sourceNode.lng);
                const targetLatLng = L.latLng(target.lat, target.lng);
                const distance = originLatLng.distanceTo(targetLatLng);

                totalDist += distance;
                if (distance > maxSegment) maxSegment = distance;

                // Visual Styles: Neon Magenta Polyline (#e879f9, weight: 3.5, dashArray: "5, 7", opacity: 0.95)
                L.polyline([originLatLng, targetLatLng], {
                    color: '#e879f9',
                    weight: 3.5,
                    dashArray: '5, 7',
                    opacity: 0.95
                }).addTo(linesGroup);

                // Midpoint Geocoding & Floating Distance Label Badge
                const midLat = (sourceNode.lat + target.lat) / 2;
                const midLng = (sourceNode.lng + target.lng) / 2;

                const labelMarker = L.marker([midLat, midLng], {
                    icon: L.divIcon({
                        className: 'distance-container-ge',
                        html: `<div class="distance-badge-ge">${Math.round(distance)} m</div>`,
                        iconAnchor: [24, 10]
                    }),
                    interactive: false
                });
                labelMarker.addTo(labelsGroup);
            });

            const roundedTotal = Math.round(totalDist);
            const estimatedPoles = Math.ceil(roundedTotal / 45);

            // Live Calculation UI Counters
            document.getElementById('targetCountLabel').innerText = `${targetNodes.length} Rumah`;
            document.getElementById('totalDistanceLabel').innerText = `${roundedTotal} Meter`;
            document.getElementById('estTiangLabel').innerText = `${estimatedPoles} Tiang`;
            document.getElementById('estimasi_jarak').value = roundedTotal;

            // Output Payload Schema Injection into #topology_data
            const payload = {
                source: sourceNode,
                targets: targetNodes,
                total_distance_meter: roundedTotal,
                estimated_new_poles: estimatedPoles
            };

            document.getElementById('topology_data').value = JSON.stringify(payload);
        }

        // On Reset Handler
        window.resetTopology = function() {
            sourceNode = null;
            targetNodes = [];
            if (markersGroup) markersGroup.clearLayers();
            if (linesGroup) linesGroup.clearLayers();
            if (labelsGroup) labelsGroup.clearLayers();

            document.getElementById('form_latitude').value = '';
            document.getElementById('form_longitude').value = '';
            document.getElementById('estimasi_jarak').value = 0;
            document.getElementById('topology_data').value = '';

            document.getElementById('targetCountLabel').innerText = '0 Rumah';
            document.getElementById('totalDistanceLabel').innerText = '0 Meter';
            document.getElementById('estTiangLabel').innerText = '0 Tiang';

            const badge = document.getElementById('pinStatusBadge');
            badge.className = "px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-900 text-xs rounded-2xl font-bold flex items-center gap-2.5";
            badge.innerHTML = `<span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span><span>Langkah 1: Klik pada peta untuk menentukan <b>1 Titik Tiang TR / Sumber Listrik Eksisting</b>.</span>`;
        };
    </script>
@endpush
