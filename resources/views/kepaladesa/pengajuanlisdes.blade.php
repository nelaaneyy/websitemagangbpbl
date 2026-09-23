@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header Navigation -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
        <a href="{{ route('kepaladesa.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-700 transition mb-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard Kades
        </a>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Form Pengajuan Lisdes (Listrik Desa)</h2>
        <p class="text-xs text-slate-500 mt-0.5">Usulan resmi Kepala Desa untuk pembangunan & perluasan jaringan listrik di wilayah yang belum berlistrik.</p>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-900 flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('kepaladesa.lisdes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid lg:grid-cols-12 gap-6">
            <!-- Kolom Kiri: Form Data Wilayah & Upload Berkas (7 Col) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Card 1: Data Wilayah -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 font-extrabold text-slate-900">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-house-laptop"></i>
                        </div>
                        <h3 class="text-base">Data Wilayah Dusun Usulan</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label for="nama_dusun" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Nama Dusun / RT / RW <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama_dusun" name="nama_dusun" value="{{ old('nama_dusun') }}" required placeholder="Contoh: Dusun III RT 08"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                            @error('nama_dusun') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="jumlah_kk" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Jumlah KK Sasaran <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" min="1" id="jumlah_kk" name="jumlah_kk" value="{{ old('jumlah_kk') }}" required placeholder="Jumlah KK"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                                @error('jumlah_kk') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="estimasi_jarak" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Jarak ke Jaringan PLN (Meter) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" min="1" id="estimasi_jarak" name="estimasi_jarak" value="{{ old('estimasi_jarak') }}" required placeholder="Contoh: 1500"
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                                @error('estimasi_jarak') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="keterangan_wilayah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Kondisi / Catatan Kebutuhan Wilayah
                            </label>
                            <textarea id="keterangan_wilayah" name="keterangan_wilayah" rows="3" placeholder="Jelaskan akses jalan atau urgensi kebutuhan listrik..."
                                      class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">{{ old('keterangan_wilayah') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Dokumen Pendukung -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100 font-extrabold text-slate-900">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <h3 class="text-base">Dokumen Pendukung Resmi (PDF & Foto)</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label for="surat_permohonan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Surat Permohonan Resmi Kades (PDF) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" id="surat_permohonan" name="surat_permohonan" accept=".pdf" required
                                   class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 transition cursor-pointer">
                        </div>

                        <div class="space-y-1">
                            <label for="proposal_lisdes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Proposal Rincian Usulan Lisdes (PDF) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" id="proposal_lisdes" name="proposal_lisdes" accept=".pdf" required
                                   class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 transition cursor-pointer">
                        </div>

                        <div class="space-y-1">
                            <label for="foto_wilayah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Foto Kondisi Dusun / Akses Jalan (Gambar) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" id="foto_wilayah" name="foto_wilayah" accept="image/*" required
                                   class="w-full p-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 transition cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Peta Leaflet Interaktif (5 Col) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100 font-extrabold text-slate-900">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <h3 class="text-base">Penentuan Koordinat GPS Wilayah</h3>
                    </div>

                    <!-- 1-to-N TOPOLOGY CANVAS COMPONENT SPECIFICATION -->
                    <div id="pinStatusBadge" class="mb-2 px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded-lg font-medium flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span id="pinStatusText">Langkah 1: Klik peta untuk menentukan <b>1 Titik Tiang TR Pangkal</b></span>
                    </div>

                    <!-- Hidden Inputs for Exported Payload Schema -->
                    <input type="hidden" name="topology_data" id="topology_data">
                    <input type="hidden" name="jarak_pln_meter" id="jarak_pln_meter" value="0">

                    <!-- Peta Canvas Interaktif Leaflet -->
                    <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm relative z-0">
                        <div id="mapLisdes" class="w-full h-72 rounded-xl border border-slate-300 relative z-0"></div>
                    </div>

                    <!-- Calculation Display Panel -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl grid grid-cols-2 gap-3 text-center">
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Total Bentang Jaringan</span>
                            <span id="totalDistanceLabel" class="text-sm font-extrabold text-blue-600">0 meter</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Est. Kebutuhan Tiang</span>
                            <span id="estTiangLabel" class="text-sm font-extrabold text-emerald-600">0 Tiang</span>
                        </div>
                    </div>

                    <!-- Display Coordinate Inputs -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Latitude Pangkal</label>
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly required placeholder="Contoh: -1.6101"
                                   class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-extrabold text-slate-800 focus:outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Longitude Pangkal</label>
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly required placeholder="Contoh: 103.6131"
                                   class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-extrabold text-slate-800 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 space-y-2">
                        <div class="flex gap-2">
                            <button type="button" onclick="deteksiGPS()" class="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-location-crosshairs text-blue-600"></i> GPS Saya
                            </button>
                            <button type="button" onclick="resetTopology()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-300 transition">
                                Reset Topologi
                            </button>
                        </div>
                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Permohonan Usulan Lisdes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const defaultLat = {{ old('latitude', -1.6101) }};
            const defaultLng = {{ old('longitude', 103.6131) }};

            // Jambi Regional Scope Bounding Box
            const jambiBounds = L.latLngBounds(
                L.latLng(-2.8500, 101.1000), // South-West
                L.latLng(-0.7500, 104.5500)  // North-East
            );

            // Inisialisasi Peta Leaflet #mapLisdes
            const mapLisdes = L.map('mapLisdes', {
                center: [defaultLat, defaultLng],
                zoom: 14, // Desa Form Default Zoom = 14
                maxBounds: jambiBounds,
                maxBoundsViscosity: 0.8
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors | WebGIS SIPELITA ESDM Jambi'
            }).addTo(mapLisdes);

            // FIX-01: Invalidate map size after DOM render & on window resize
            setTimeout(function() {
                if (mapLisdes) mapLisdes.invalidateSize();
            }, 200);

            window.addEventListener('resize', function() {
                if (mapLisdes) mapLisdes.invalidateSize();
            });

            // State Variables & Layer Groups
            let sourceNode = null; // { lat, lng }
            let targetNodes = [];  // [{ id: N, lat, lng }]
            
            const markersGroup = L.layerGroup().addTo(mapLisdes);
            const linesGroup = L.layerGroup().addTo(mapLisdes);
            const labelsGroup = L.layerGroup().addTo(mapLisdes);

            // Custom DivIcon Markers
            const sourceIcon = L.divIcon({
                className: 'custom-pin-source',
                html: `<div class="bg-rose-600 text-white w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs shadow-lg border-2 border-white">⚡</div>`,
                iconAnchor: [14, 14]
            });

            function getTargetIcon(index) {
                return L.divIcon({
                    className: 'custom-pin-target',
                    html: `<div class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center font-bold text-[10px] shadow-lg border-2 border-white">${index}</div>`,
                    iconAnchor: [12, 12]
                });
            }

            function updateInputs(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);
            }

            // Map Click Interaction Workflow
            mapLisdes.on('click', function(e) {
                const { lat, lng } = e.latlng;

                if (!sourceNode) {
                    // STEP 1: Source Pinning (First Click)
                    sourceNode = { lat: parseFloat(lat.toFixed(7)), lng: parseFloat(lng.toFixed(7)) };
                    updateInputs(sourceNode.lat, sourceNode.lng);

                    L.marker([sourceNode.lat, sourceNode.lng], { icon: sourceIcon }).addTo(markersGroup);

                    // Update Status Badge UI
                    document.getElementById('pinStatusText').innerHTML = 'Langkah 2: Klik titik-titik <b>Rumah Warga Sasaran (N)</b>';
                } else {
                    // STEP 2: Target Pinning (Consecutive Clicks)
                    const targetIndex = targetNodes.length + 1;
                    const newTarget = {
                        id: targetIndex,
                        lat: parseFloat(lat.toFixed(7)),
                        lng: parseFloat(lng.toFixed(7))
                    };
                    targetNodes.push(newTarget);

                    L.marker([newTarget.lat, newTarget.lng], { icon: getTargetIcon(targetIndex) }).addTo(markersGroup);

                    // STEP 3: Redraw & Compute
                    redrawTopology();
                }
            });

            // STEP 3: Redraw & Compute Function
            function redrawTopology() {
                linesGroup.clearLayers();
                labelsGroup.clearLayers();

                if (!sourceNode || targetNodes.length === 0) return;

                let totalDist = 0;
                let currentPoint = L.latLng(sourceNode.lat, sourceNode.lng);
                let latLngList = [currentPoint];

                targetNodes.forEach((target) => {
                    let targetLatLng = L.latLng(target.lat, target.lng);
                    latLngList.push(targetLatLng);

                    // Geodesic Distance per segment
                    let dist = currentPoint.distanceTo(targetLatLng);
                    totalDist += dist;

                    // Midpoint Geocoding for Floating Distance Badge
                    let midLat = (currentPoint.lat + targetLatLng.lat) / 2;
                    let midLng = (currentPoint.lng + targetLatLng.lng) / 2;

                    let labelIcon = L.divIcon({
                        className: 'midpoint-label-badge',
                        html: `<div class="bg-slate-900 text-amber-300 border border-amber-500/50 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold shadow-md whitespace-nowrap">${dist.toFixed(1)}m</div>`,
                        iconSize: [60, 20],
                        iconAnchor: [30, 10]
                    });

                    L.marker([midLat, midLng], { icon: labelIcon }).addTo(labelsGroup);
                    currentPoint = targetLatLng;
                });

                // Polyline Rendering (#2563EB, weight: 3, dashArray: "6, 6", opacity: 0.8)
                L.polyline(latLngList, {
                    color: '#2563EB',
                    weight: 3,
                    dashArray: '6, 6',
                    opacity: 0.8
                }).addTo(linesGroup);

                // Pole Estimation: Math.ceil(totalDist / 45)
                let totalMeters = Math.round(totalDist);
                let estPoles = Math.ceil(totalMeters / 45);

                // Update UI Display Labels & Form Inputs
                document.getElementById('totalDistanceLabel').textContent = totalMeters + ' meter';
                document.getElementById('estTiangLabel').textContent = estPoles + ' Tiang';
                document.getElementById('jarak_pln_meter').value = totalMeters;

                const estimasiJarakElem = document.getElementById('estimasi_jarak');
                if (estimasiJarakElem) estimasiJarakElem.value = totalMeters;

                const jumlahKkElem = document.getElementById('jumlah_kk');
                if (jumlahKkElem && targetNodes.length > 0) jumlahKkElem.value = targetNodes.length;

                // Exported Payload Schema Injection into #topology_data
                const payload = {
                    source: sourceNode,
                    targets: targetNodes,
                    total_distance: totalMeters,
                    estimated_poles: estPoles
                };

                document.getElementById('topology_data').value = JSON.stringify(payload);
            }

            // STEP 4: Reset Function
            window.resetTopology = function() {
                sourceNode = null;
                targetNodes = [];

                markersGroup.clearLayers();
                linesGroup.clearLayers();
                labelsGroup.clearLayers();

                document.getElementById('totalDistanceLabel').textContent = '0 meter';
                document.getElementById('estTiangLabel').textContent = '0 Tiang';
                document.getElementById('topology_data').value = '';
                document.getElementById('jarak_pln_meter').value = '0';
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';

                document.getElementById('pinStatusText').innerHTML = 'Langkah 1: Klik peta untuk menentukan <b>1 Titik Tiang TR Pangkal</b>';
            };

            window.deteksiGPS = function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        mapLisdes.setView([lat, lng], 15);
                        if (!sourceNode) {
                            sourceNode = { lat: parseFloat(lat.toFixed(7)), lng: parseFloat(lng.toFixed(7)) };
                            updateInputs(sourceNode.lat, sourceNode.lng);
                            L.marker([sourceNode.lat, sourceNode.lng], { icon: sourceIcon }).addTo(markersGroup);
                            document.getElementById('pinStatusText').innerHTML = 'Langkah 2: Klik titik-titik <b>Rumah Warga Sasaran (N)</b>';
                        }
                    }, function(error) {
                        alert("Gagal mendeteksi lokasi GPS device. Silakan tentukan titik dengan klik peta secara langsung.");
                    }, { enableHighAccuracy: true });
                } else {
                    alert("Browser Anda tidak mendukung Geolocation GPS.");
                }
            };
        });
    </script>
@endpush

