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

                    <p class="text-xs text-slate-500 font-medium leading-relaxed">
                        Klik pada peta di bawah ini untuk menempatkan pin lokasi dusun, atau gunakan tombol GPS Device untuk posisi presisi.
                    </p>

                    <!-- Peta Interaktif Leaflet -->
                    <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
                        <div id="map" class="w-full h-80 z-10"></div>
                    </div>

                    <!-- Display Inputs -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Latitude</label>
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly required placeholder="Contoh: -1.6101"
                                   class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-extrabold text-slate-800 focus:outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Longitude</label>
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly required placeholder="Contoh: 103.6131"
                                   class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs font-mono font-extrabold text-slate-800 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <button type="button" onclick="deteksiGPS()" class="w-full py-3 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 rounded-2xl text-xs font-extrabold transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-location-crosshairs text-blue-600"></i> Deteksi Lokasi GPS Saya
                        </button>
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

            const map = L.map('map').setView([defaultLat, defaultLng], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors | Dinas ESDM'
            }).addTo(map);

            let marker;

            function updateInputs(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);
            }

            @if(old('latitude') && old('longitude'))
                marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
                updateInputs(defaultLat, defaultLng);
            @endif

            map.on('click', function(e) {
                const { lat, lng } = e.latlng;
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    marker.on('dragend', function(evt) {
                        const pos = marker.getLatLng();
                        updateInputs(pos.lat, pos.lng);
                    });
                }
                updateInputs(lat, lng);
            });

            window.deteksiGPS = function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        map.setView([lat, lng], 15);
                        if (marker) {
                            marker.setLatLng([lat, lng]);
                        } else {
                            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                            marker.on('dragend', function(evt) {
                                const pos = marker.getLatLng();
                                updateInputs(pos.lat, pos.lng);
                            });
                        }
                        updateInputs(lat, lng);
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

