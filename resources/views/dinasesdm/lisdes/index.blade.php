@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ rejectModalOpen: false, activeLisdes: null, activeLisdesDesa: '', activeActionUrl: '', designMode: false }">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-800 text-[10px] font-extrabold rounded-md uppercase">Program Infrastruktur</span>
                <span class="text-xs text-slate-400 font-semibold">Dinas ESDM (UC-ESDM-LISDES-02)</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Perancangan Jaringan Lisdes Interaktif & Dual Export</h1>
            <p class="text-xs text-slate-500 mt-0.5">Penetapan kelayakan usulan Lisdes, perancangan multi-pin 1-to-N dengan dynamic midpoint label, dan ekspor KML/PDF.</p>
        </div>

        <div class="flex gap-2">
            <button @click="designMode = !designMode" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-draw-polygon"></i> <span x-text="designMode ? 'Tutup Canvas Desain' : 'Mode Desain Lisdes (Multi-Pin)'"></span>
            </button>
            <a href="{{ route('dinasesdm.export.kml') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                <i class="fa-solid fa-earth-americas"></i> Ekspor KML (Google Earth)
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-900 flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- LEAFLET INTERACTIVE CANVAS FOR MULTI-PINNING 1-TO-N (UC-ESDM-LISDES-02) -->
    <div x-show="designMode" class="bg-slate-900 p-4 rounded-3xl border border-slate-700 space-y-4 shadow-xl">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-300">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 rounded-lg font-bold">1-to-N Topologi Canvas</span>
                <span>Klik tiang TR pangkal, lalu klik titik rumah warga sasaran untuk membuat bentang kabel.</span>
            </div>
            <div class="flex gap-4 font-mono">
                <div>Total Jarak: <span id="canvas-total-dist" class="text-amber-400 font-bold">0.0 m</span></div>
                <div>Est. Tiang TR Baru: <span id="canvas-pole-count" class="text-emerald-400 font-bold">0 Tiang</span></div>
            </div>
        </div>

        <!-- Canvas Container (FIX-01: Locked height & z-0) -->
        <div id="lisdes-design-map" class="w-full h-[450px] min-h-[400px] rounded-2xl border border-slate-700 relative overflow-hidden z-0"></div>
        <div class="flex justify-end gap-2">
            <button onclick="clearLisdesCanvas()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-lg border border-slate-700">Reset Line Canvas</button>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Total Usulan</span>
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-slate-400">Pengajuan Lisdes Masuk</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-amber-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-amber-700 uppercase tracking-wider">Menunggu Review</span>
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-amber-900 tracking-tight">{{ $stats['menunggu_verifikasi'] }}</p>
            <p class="text-[11px] text-amber-600 font-medium">Perlu Peninjauan ESDM</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-emerald-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider">Disetujui ESDM</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-emerald-900 tracking-tight">{{ $stats['disetujui'] }}</p>
            <p class="text-[11px] text-emerald-600 font-medium">Usulan Lolos Verifikasi</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-rose-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-rose-700 uppercase tracking-wider">Ditolak / Revisi</span>
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-rose-900 tracking-tight">{{ $stats['ditolak'] }}</p>
            <p class="text-[11px] text-rose-600 font-medium">Dikembalikan Ke Desa</p>
        </div>
    </div>

    <!-- Table Pengajuan Lisdes -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-semibold">
                <thead class="bg-slate-900 text-white uppercase font-extrabold tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Desa & Pengaju</th>
                        <th class="px-5 py-4">Wilayah Usulan (Dusun)</th>
                        <th class="px-5 py-4 text-center">Sasaran KK</th>
                        <th class="px-5 py-4 text-center">Jarak ke PLN</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center">Dual Export</th>
                        <th class="px-5 py-4 text-center">Aksi ESDM</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lisdesList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $item->desa }}</div>
                                <div class="text-[11px] text-slate-400 font-medium">Kades: {{ $item->user->name ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-800">{{ $item->nama_dusun }}</div>
                            </td>
                            <td class="px-5 py-4 text-center font-extrabold text-slate-900">
                                {{ number_format($item->jumlah_kk) }} KK
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-slate-700">
                                {{ number_format($item->estimasi_jarak) }} m
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @if($item->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 text-[11px] font-extrabold rounded-full border border-emerald-200">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-800 text-[11px] font-extrabold rounded-full border border-amber-200">
                                        Menunggu ESDM
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('dinasesdm.export.kml', ['desa' => $item->desa]) }}" class="px-2.5 py-1 bg-emerald-100 text-emerald-800 hover:bg-emerald-200 text-[10px] font-bold rounded-lg transition">.KML</a>
                                    <a href="{{ route('dinasesdm.lisdes.map.pdf', $item->id) }}" class="px-2.5 py-1 bg-rose-100 text-rose-800 hover:bg-rose-200 text-[10px] font-bold rounded-lg transition">PDF Layout</a>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('dinasesdm.lisdes.show', $item->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">Belum ada pengajuan Lisdes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- LEAFLET JS SCRIPT UNTUK DYNAMIC MIDPOINT DISTANCE LABELS (UC-ESDM-LISDES-02) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let lisdesMap = null;
    let pinPoints = [];
    let polylineLayer = null;
    let midpointMarkers = [];

    document.addEventListener("DOMContentLoaded", function() {
        const jambiBounds = L.latLngBounds(
            L.latLng(-2.8500, 101.1000), // South-West
            L.latLng(-0.7500, 104.5500)  // North-East
        );

        lisdesMap = L.map('lisdes-design-map', {
            center: [-1.6101, 103.6131],
            zoom: 9, // Admin Overview Default Zoom = 9
            maxBounds: jambiBounds,
            maxBoundsViscosity: 0.8
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors | WebGIS SIPELITA ESDM Jambi'
        }).addTo(lisdesMap);

        lisdesMap.on('click', function(e) {
            addPinPoint(e.latlng);
        });

        // FIX-01: Invalidate map size after DOM render & on window resize
        setTimeout(function() {
            if (lisdesMap) lisdesMap.invalidateSize();
        }, 200);

        window.addEventListener('resize', function() {
            if (lisdesMap) lisdesMap.invalidateSize();
        });
    });

    function addPinPoint(latlng) {
        pinPoints.push(latlng);

        // Marker Pin
        L.marker(latlng).addTo(lisdesMap);

        // Draw Polyline & Midpoint Labels
        renderPolylineAndMidpoints();
    }

    function renderPolylineAndMidpoints() {
        if (polylineLayer) lisdesMap.removeLayer(polylineLayer);
        midpointMarkers.forEach(m => lisdesMap.removeLayer(m));
        midpointMarkers = [];

        if (pinPoints.length < 2) return;

        polylineLayer = L.polyline(pinPoints, { color: '#f59e0b', weight: 4, dashArray: '8, 8' }).addTo(lisdesMap);

        let totalDist = 0;
        for (let i = 0; i < pinPoints.length - 1; i++) {
            let p1 = pinPoints[i];
            let p2 = pinPoints[i+1];
            let dist = p1.distanceTo(p2);
            totalDist += dist;

            // Midpoint calculation
            let midLat = (p1.lat + p2.lat) / 2;
            let midLng = (p1.lng + p2.lng) / 2;

            // Render Dynamic Midpoint Distance Label Badge (UC-ESDM-LISDES-02 Step 4)
            let labelIcon = L.divIcon({
                className: 'midpoint-label-badge',
                html: `<div style="background:#1e293b; color:#fbbf24; border:1px solid #f59e0b; padding:2px 6px; border-radius:10px; font-size:10px; font-weight:bold; white-space:nowrap;">${dist.toFixed(1)} m</div>`,
                iconSize: [60, 20],
                iconAnchor: [30, 10]
            });

            let marker = L.marker([midLat, midLng], { icon: labelIcon }).addTo(lisdesMap);
            midpointMarkers.push(marker);
        }

        document.getElementById('canvas-total-dist').textContent = totalDist.toFixed(1) + ' m';
        let poleCount = Math.ceil(totalDist / 50.0); // Standar 50m per tiang TR
        document.getElementById('canvas-pole-count').textContent = poleCount + ' Tiang';
    }

    function clearLisdesCanvas() {
        pinPoints = [];
        if (polylineLayer) lisdesMap.removeLayer(polylineLayer);
        midpointMarkers.forEach(m => lisdesMap.removeLayer(m));
        midpointMarkers = [];
        document.getElementById('canvas-total-dist').textContent = '0.0 m';
        document.getElementById('canvas-pole-count').textContent = '0 Tiang';
    }
</script>
@endsection
