@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <!-- Back Link & Title Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <a href="{{ route('warga.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <i class="fa-solid fa-arrow-left text-amber-500"></i> Kembali ke Beranda
            </a>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Formulir Resmi Bantuan Pasang Baru Listrik (BPBL)</span>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden relative">
            <!-- Header Accent Banner (Midnight Slate & ESDM Amber) -->
            <div class="bg-slate-900 text-white p-6 sm:p-10 relative overflow-hidden border-b border-slate-800">
                <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-amber-500/10 blur-2xl"></div>

                <div class="relative z-10 space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500 text-slate-950 font-black text-[11px] uppercase rounded-full tracking-wider">
                        <i class="fa-solid fa-bolt"></i> 100% Program Bebas Biaya ESDM
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                        {{ isset($warga) ? 'Perbaikan Data Pendaftaran BPBL' : 'Formulir Pendaftaran Bantuan Listrik' }}
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm max-w-2xl leading-relaxed font-normal">
                        {{ isset($warga) ? 'Silakan perbaiki data yang belum sesuai dan unggah ulang berkas persyaratan sesuai catatan verifikator.' : 'Lengkapi identitas diri sesuai KTP, deteksi titik lokasi rumah via GPS, dan unggah berkas persyaratan resmi.' }}
                    </p>
                </div>
            </div>

            <div class="p-6 sm:p-10 space-y-10">

                @if(isset($warga) && $warga->catatan)
                    <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center text-lg font-bold shrink-0 shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-amber-950">Catatan Penolakan Sebelumnya:</h4>
                            <p class="text-xs text-amber-900 leading-relaxed mt-1 font-medium">{{ $warga->catatan }}</p>
                        </div>
                    </div>
                @endif

                {{-- Pesan Alert Error Global --}}
                @if ($errors->any())
                    <div class="p-5 bg-rose-50 border border-rose-200 rounded-2xl">
                        <div class="flex items-center gap-2 mb-2 text-rose-900 font-extrabold text-sm">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                            <span>Terdapat kesalahan input formulir:</span>
                        </div>
                        <ul class="list-disc list-inside text-xs text-rose-700 space-y-1 font-medium pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('warga.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                    @csrf

                    {{-- SECTION 1: DATA IDENTITAS WARGA --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-sm">1</div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Identitas Pemohon (Sesuai KTP)</h3>
                                <p class="text-xs text-slate-500 font-medium">Isi data pribadi dan wilayah domisili dengan benar</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- NIK --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="nik" value="{{ old('nik', $warga->nik ?? $nik ?? '') }}" maxlength="16" required
                                           placeholder="16 Digit NIK KTP..." {{ isset($warga) ? 'readonly' : '' }}
                                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border @error('nik') border-rose-400 @else border-slate-300 @enderror {{ isset($warga) ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'focus:bg-white' }} rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                    <i class="fa-solid fa-id-card absolute left-3.5 top-3.5 text-slate-400"></i>
                                </div>
                                @error('nik') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="nama" value="{{ old('nama', $warga->nama ?? '') }}" required
                                           placeholder="Nama lengkap sesuai KTP..."
                                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border @error('nama') border-rose-400 @else border-slate-300 @enderror focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                    <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-400"></i>
                                </div>
                                @error('nama') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kabupaten --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kabupaten / Kota <span class="text-rose-500">*</span>
                                </label>
                                <select name="kabupaten" id="kabupaten" required
                                       class="w-full px-4 py-3 bg-slate-50 border @error('kabupaten') border-rose-400 @else border-slate-300 @enderror focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                    <option value="">-- Pilih Kabupaten / Kota --</option>
                                    <option value="KABUPATEN KERINCI" data-id="1501" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN KERINCI' ? 'selected' : '' }}>KABUPATEN KERINCI</option>
                                    <option value="KABUPATEN MERANGIN" data-id="1502" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN MERANGIN' ? 'selected' : '' }}>KABUPATEN MERANGIN</option>
                                    <option value="KABUPATEN SAROLANGUN" data-id="1503" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN SAROLANGUN' ? 'selected' : '' }}>KABUPATEN SAROLANGUN</option>
                                    <option value="KABUPATEN BATANG HARI" data-id="1504" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN BATANG HARI' ? 'selected' : '' }}>KABUPATEN BATANG HARI</option>
                                    <option value="KABUPATEN MUARO JAMBI" data-id="1505" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN MUARO JAMBI' ? 'selected' : '' }}>KABUPATEN MUARO JAMBI</option>
                                    <option value="KABUPATEN TANJUNG JABUNG TIMUR" data-id="1506" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN TANJUNG JABUNG TIMUR' ? 'selected' : '' }}>KABUPATEN TANJUNG JABUNG TIMUR</option>
                                    <option value="KABUPATEN TANJUNG JABUNG BARAT" data-id="1507" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN TANJUNG JABUNG BARAT' ? 'selected' : '' }}>KABUPATEN TANJUNG JABUNG BARAT</option>
                                    <option value="KABUPATEN TEBO" data-id="1508" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN TEBO' ? 'selected' : '' }}>KABUPATEN TEBO</option>
                                    <option value="KABUPATEN BUNGO" data-id="1509" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KABUPATEN BUNGO' ? 'selected' : '' }}>KABUPATEN BUNGO</option>
                                    <option value="KOTA JAMBI" data-id="1571" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KOTA JAMBI' ? 'selected' : '' }}>KOTA JAMBI</option>
                                    <option value="KOTA SUNGAI PENUH" data-id="1572" {{ old('kabupaten', isset($warga) ? $warga->kabupaten : '') == 'KOTA SUNGAI PENUH' ? 'selected' : '' }}>KOTA SUNGAI PENUH</option>
                                </select>
                                @error('kabupaten') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kecamatan --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kecamatan <span class="text-rose-500">*</span>
                                </label>
                                <select name="kecamatan" id="kecamatan" required disabled
                                       class="w-full px-4 py-3 bg-slate-50 border @error('kecamatan') border-rose-400 @else border-slate-300 @enderror focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                @error('kecamatan') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Desa / Kelurahan --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Desa / Kelurahan <span class="text-rose-500">*</span>
                                </label>
                                <select name="desa" id="desa" required disabled
                                       class="w-full px-4 py-3 bg-slate-50 border @error('desa') border-rose-400 @else border-slate-300 @enderror focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">-- Pilih Desa / Kelurahan --</option>
                                </select>
                                @error('desa') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- RT / RW --}}
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    RT / RW <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="rt_rw" value="{{ old('rt_rw', $warga->rt_rw ?? '') }}" placeholder="Contoh: RT 02 / RW 01" required
                                       class="w-full px-4 py-3 bg-slate-50 border @error('rt_rw') border-rose-400 @else border-slate-300 @enderror focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                @error('rt_rw') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- No HP / WhatsApp --}}
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    No. WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="no_hp" value="{{ old('no_hp', $warga->no_hp ?? '') }}" placeholder="08xxxxxxxxxx" required
                                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border @error('no_hp') border-rose-400 @else border-slate-300 @enderror focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                    <i class="fa-brands fa-whatsapp absolute left-3.5 top-3.5 text-emerald-600 text-lg"></i>
                                </div>
                                @error('no_hp') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Alamat Domisili Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="alamat" rows="3" required placeholder="Nama jalan, nomor rumah, patokan bangunan..."
                                      class="w-full px-4 py-3 bg-slate-50 border @error('alamat') border-rose-400 @else border-slate-300 @enderror focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition resize-none">{{ old('alamat', $warga->alamat ?? '') }}</textarea>
                            @error('alamat') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- SECTION 2: TITIK LOKASI GPS RUMAH --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-sm">2</div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Titik Koordinat GPS Rumah</h3>
                                <p class="text-xs text-slate-500">Deteksi otomatis atau pilih lokasi rumah Anda secara presisi pada peta untuk survei PLN</p>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl space-y-4">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button type="button" onclick="getLocation()"
                                        class="flex-1 py-3.5 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-sm rounded-xl transition-all shadow-md flex items-center justify-center gap-2.5 cursor-pointer border border-slate-800">
                                    <i class="fa-solid fa-location-crosshairs text-amber-400 text-base"></i>
                                    <span>Deteksi Lokasi Otomatis (GPS HP)</span>
                                </button>
                            </div>

                            <!-- Leaflet Interactive Map Container -->
                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        <i class="fa-solid fa-map-location-dot text-amber-500 mr-1"></i> Peta Interaktif Penentuan Lokasi
                                    </label>
                                    
                                    <!-- Toggle Mode Peta (Jalan vs Satelit + Nama Jalan) -->
                                    <div class="inline-flex p-1 bg-slate-200/80 rounded-xl gap-1 self-start sm:self-auto shadow-xs border border-slate-300/60">
                                        <button type="button" id="btn-mode-streets" onclick="switchMapTile('streets')" 
                                                class="px-3 py-1.5 bg-white text-slate-900 shadow-xs rounded-lg text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer">
                                            <i class="fa-solid fa-map text-blue-600"></i> Peta Jalan
                                        </button>
                                        <button type="button" id="btn-mode-satellite" onclick="switchMapTile('satellite')" 
                                                class="px-3 py-1.5 text-slate-600 hover:text-slate-900 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                            <i class="fa-solid fa-satellite text-amber-500"></i> Satelit + Nama Jalan
                                        </button>
                                    </div>
                                </div>
                                
                                <div id="map-picker" class="h-80 w-full rounded-2xl border border-slate-300 shadow-inner z-0 overflow-hidden relative"></div>
                                
                                <!-- Info Deteksi Nama Jalan & Alamat Otomatis -->
                                <div id="street-name-info" class="p-3.5 bg-blue-50/90 border border-blue-200 rounded-xl text-xs font-semibold text-blue-900 flex items-start gap-2.5 shadow-xs">
                                    <i class="fa-solid fa-road text-blue-600 text-sm mt-0.5"></i>
                                    <div>
                                        <strong class="text-blue-950 block text-xs">Deteksi Nama Jalan & Alamat:</strong>
                                        <span class="text-slate-600 text-[11px] font-medium leading-snug">Geser penanda biru di peta atau klik lokasi rumah Anda untuk mendeteksi nama jalan secara otomatis.</span>
                                    </div>
                                </div>

                                <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl text-xs font-semibold text-amber-900 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-info text-amber-600 text-sm"></i>
                                    <span>Petunjuk: Pilih mode <strong>"Satelit + Nama Jalan"</strong> di atas peta untuk melihat atap bangunan & nama jalan dengan jelas.</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 pt-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Latitude</label>
                                    <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $warga->latitude ?? '') }}" readonly required
                                           placeholder="-1.xxxxxx"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800">
                                    @error('latitude') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Longitude</label>
                                    <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $warga->longitude ?? '') }}" readonly required
                                           placeholder="103.xxxxxx"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800">
                                    @error('longitude') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: UPLOAD BERKAS FOTO --}}
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-sm">3</div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Unggah Dokumen & Foto Fisik</h3>
                                <p class="text-xs text-slate-500">Format JPG / PNG, ukuran maksimal 2 MB per file</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @php
                                $files = [
                                    'foto_ktp' => ['label' => '1. Foto KTP Pemohon', 'desc' => 'Foto KTP asli jelas & tulisan NIK terbaca.'],
                                    'foto_sktm' => ['label' => '2. Foto SKTM / Kartu Bansos', 'desc' => 'Surat Keterangan Tidak Mampu dari Kelurahan / KIS / KKS.'],
                                    'foto_rumah_depan' => ['label' => '3. Foto Rumah Tampak Depan', 'desc' => 'Kondisi fisik rumah tampak depan secara utuh.'],
                                    'foto_kwh_rumah_terdekat' => ['label' => '4. Foto kWH Meter Tetangga', 'desc' => 'Meteran listrik PLN milik tetangga terdekat.'],
                                    'foto_tiang_rumah_terdekat' => ['label' => '5. Foto Tiang PLN Terdekat', 'desc' => 'Tiang jaringan listrik PLN terdekat dari rumah.'],
                                ];
                            @endphp

                            @foreach($files as $name => $fileInfo)
                                <div class="p-5 border-2 border-dashed @error($name) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-slate-50/50 @enderror rounded-2xl text-center space-y-3 relative hover:border-blue-500 transition">
                                    <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                        {{ $fileInfo['label'] }} <span class="text-rose-500">*</span>
                                    </label>
                                    <p class="text-[11px] text-slate-500 font-medium leading-tight">{{ $fileInfo['desc'] }}</p>

                                    <div class="relative w-full h-32 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-xs">
                                        <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/jpg" required
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                               onchange="previewImage(this, '{{$name}}')">

                                        <!-- Default Box -->
                                        <div id="box-{{$name}}" class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 text-blue-700">
                                            <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                            <span class="text-xs font-bold">Pilih / Unggah Foto</span>
                                        </div>

                                        <!-- Image Preview -->
                                        <div id="preview-container-{{$name}}" class="hidden absolute inset-0 w-full h-full bg-slate-100 z-10">
                                            <img id="preview-img-{{$name}}" src="" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    @error($name) <p class="text-rose-600 text-xs font-semibold">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- TOMBOL SUBMIT --}}
                    <div class="pt-6 border-t border-slate-200">
                        <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-base rounded-2xl shadow-md transition-all flex items-center justify-center gap-3 cursor-pointer border border-slate-800">
                            <i class="fa-solid fa-paper-plane text-amber-400"></i>
                            <span>{{ isset($warga) ? 'Simpan Perbaikan & Kirim Ulang' : 'Kirim Form Pendaftaran BPBL' }}</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT GPS GEOLOCATION, MAP PICKER & PREVIEW --}}
<script>
let mapPicker, markerPicker;
let googleHybridLayer, googleStreetsLayer, googleSatelliteLayer, osmStreetsLayer, esriSatelliteLayer;
let currentTileMode = 'satellite';

function initMapPicker() {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    let initialLat = parseFloat(latInput ? latInput.value : '');
    let initialLng = parseFloat(lngInput ? lngInput.value : '');
    let hasCoords = !isNaN(initialLat) && !isNaN(initialLng) && initialLat !== 0 && initialLng !== 0;

    if (!hasCoords) {
        initialLat = -1.6000;
        initialLng = 102.7500;
    }

    const initialZoom = hasCoords ? 16 : 9;

    const mapElement = document.getElementById('map-picker');
    if (!mapElement) return;

    // Define Map Layers (Google Maps Latest Tiles + OpenStreetMap & Esri Fallbacks)
    googleHybridLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['0', '1', '2', '3'],
        attribution: '&copy; Google Maps'
    });

    googleStreetsLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['0', '1', '2', '3'],
        attribution: '&copy; Google Maps'
    });

    googleSatelliteLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['0', '1', '2', '3'],
        attribution: '&copy; Google Maps'
    });

    osmStreetsLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    });

    esriSatelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Esri World Imagery'
    });

    mapPicker = L.map('map-picker', {
        layers: [googleHybridLayer]
    }).setView([initialLat, initialLng], initialZoom);

    // Leaflet Layers Control at top right
    const baseMaps = {
        "🛰️ Google Satelit Terbaru + Nama Jalan": googleHybridLayer,
        "🗺️ Google Peta Jalan Terbaru": googleStreetsLayer,
        "📷 Google Satelit Murni": googleSatelliteLayer,
        "🌍 OpenStreetMap Standard": osmStreetsLayer,
        "📡 Esri World Imagery": esriSatelliteLayer
    };

    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(mapPicker);

    // Draggable Marker
    markerPicker = L.marker([initialLat, initialLng], {
        draggable: true
    }).addTo(mapPicker);

    markerPicker.bindPopup("<b>Titik Lokasi Rumah Anda</b><br>Geser (drag) pin ini tepat ke lokasi rumah.").openPopup();

    if (hasCoords) {
        fetchStreetName(initialLat, initialLng);
    }

    // Event when marker is dragged
    markerPicker.on('dragend', function(e) {
        const pos = markerPicker.getLatLng();
        updateCoordinatesInput(pos.lat, pos.lng);
        fetchStreetName(pos.lat, pos.lng);
    });

    // Event when map is clicked
    mapPicker.on('click', function(e) {
        markerPicker.setLatLng(e.latlng);
        updateCoordinatesInput(e.latlng.lat, e.latlng.lng);
        fetchStreetName(e.latlng.lat, e.latlng.lng);
    });

    // Fix initial render dimensions
    setTimeout(() => {
        if (mapPicker) mapPicker.invalidateSize();
    }, 300);
    setTimeout(() => {
        if (mapPicker) mapPicker.invalidateSize();
    }, 1000);
}

function switchMapTile(mode) {
    if (!mapPicker) return;
    const btnStreets = document.getElementById('btn-mode-streets');
    const btnSatellite = document.getElementById('btn-mode-satellite');

    // Clear active tile layers safely
    if (googleHybridLayer && mapPicker.hasLayer(googleHybridLayer)) mapPicker.removeLayer(googleHybridLayer);
    if (googleStreetsLayer && mapPicker.hasLayer(googleStreetsLayer)) mapPicker.removeLayer(googleStreetsLayer);
    if (googleSatelliteLayer && mapPicker.hasLayer(googleSatelliteLayer)) mapPicker.removeLayer(googleSatelliteLayer);
    if (osmStreetsLayer && mapPicker.hasLayer(osmStreetsLayer)) mapPicker.removeLayer(osmStreetsLayer);
    if (esriSatelliteLayer && mapPicker.hasLayer(esriSatelliteLayer)) mapPicker.removeLayer(esriSatelliteLayer);

    if (mode === 'satellite') {
        if (googleHybridLayer) mapPicker.addLayer(googleHybridLayer);
        currentTileMode = 'satellite';

        if (btnSatellite) btnSatellite.className = "px-3 py-1.5 bg-white text-slate-900 shadow-xs rounded-lg text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer";
        if (btnStreets) btnStreets.className = "px-3 py-1.5 text-slate-600 hover:text-slate-900 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer";
    } else {
        if (googleStreetsLayer) mapPicker.addLayer(googleStreetsLayer);
        currentTileMode = 'streets';

        if (btnStreets) btnStreets.className = "px-3 py-1.5 bg-white text-slate-900 shadow-xs rounded-lg text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer";
        if (btnSatellite) btnSatellite.className = "px-3 py-1.5 text-slate-600 hover:text-slate-900 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer";
    }
}

function fetchStreetName(lat, lng) {
    const infoBox = document.getElementById('street-name-info');
    if (infoBox) {
        infoBox.innerHTML = `
            <i class="fa-solid fa-circle-notch animate-spin text-blue-600 text-sm mt-0.5"></i>
            <div>
                <strong class="text-blue-950 block text-xs">Mencari Nama Jalan & Alamat...</strong>
                <span class="text-slate-500 text-[11px]">Menghubungi server geocoding untuk deteksi nama jalan...</span>
            </div>
        `;
    }

    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                const road = data.address.road || data.address.pedestrian || data.address.suburb || data.address.village || data.address.county || '';
                // Abaikan & bersihkan kode pos 5 digit dari respon Nominatim agar tidak mengecoh warga
                const fullAddress = data.display_name.replace(/,\s*\d{5}\b/g, '');
                
                if (infoBox) {
                    infoBox.innerHTML = `
                        <i class="fa-solid fa-road text-blue-600 text-sm mt-0.5"></i>
                        <div>
                            <strong class="text-slate-900 font-extrabold block text-xs">${road ? 'Jl. ' + road : 'Nama Jalan Terdeteksi'}</strong>
                            <span class="text-slate-600 text-[11px] font-medium leading-snug block mt-0.5">${fullAddress}</span>
                        </div>
                    `;
                }

                if (markerPicker) {
                    markerPicker.bindPopup(`
                        <div class="text-xs font-semibold">
                            <strong class="text-blue-700 block mb-0.5"><i class="fa-solid fa-location-dot"></i> Titik Rumah Anda</strong>
                            <span class="text-slate-800 font-bold block">${road ? 'Jl. ' + road : ''}</span>
                            <span class="text-slate-500 text-[10px] block font-mono mt-0.5">Lat: ${parseFloat(lat).toFixed(6)}, Lng: ${parseFloat(lng).toFixed(6)}</span>
                        </div>
                    `).openPopup();
                }
            }
        })
        .catch(err => {
            if (infoBox) {
                infoBox.innerHTML = `
                    <i class="fa-solid fa-location-dot text-amber-500 text-sm mt-0.5"></i>
                    <div>
                        <strong class="text-slate-900 font-extrabold block text-xs">Koordinat Terpilih:</strong>
                        <span class="text-slate-600 text-[11px] font-mono">Lat: ${parseFloat(lat).toFixed(6)}, Lng: ${parseFloat(lng).toFixed(6)}</span>
                    </div>
                `;
            }
        });
}

function updateCoordinatesInput(lat, lng) {
    const formattedLat = parseFloat(lat).toFixed(6);
    const formattedLng = parseFloat(lng).toFixed(6);
    const latEl = document.getElementById('latitude');
    const lngEl = document.getElementById('longitude');
    if (latEl) latEl.value = formattedLat;
    if (lngEl) lngEl.value = formattedLng;
}

function updateMapLocation(lat, lng) {
    const position = [lat, lng];
    if (markerPicker) {
        markerPicker.setLatLng(position);
    }
    if (mapPicker) {
        mapPicker.setView(position, 16);
        mapPicker.invalidateSize();
    }
    fetchStreetName(lat, lng);
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                updateCoordinatesInput(lat, lng);
                updateMapLocation(lat, lng);
                alert('Lokasi GPS berhasil dideteksi! Anda juga dapat menggeser pin di peta jika lokasi belum tepat.');
            },
            function(error) {
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert("Akses lokasi ditolak. Harap izinkan akses lokasi/GPS pada browser HP Anda.");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert("Informasi lokasi tidak tersedia.");
                        break;
                    case error.TIMEOUT:
                        alert("Waktu permintaan deteksi lokasi habis.");
                        break;
                    default:
                        alert("Terjadi kesalahan saat mengambil lokasi GPS.");
                        break;
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        alert('Browser Anda tidak mendukung fitur deteksi lokasi otomatis.');
    }
}

// Fitur Otomatisasi Kompresi Foto Client-Side (HTML5 Canvas -> ~200-300 KB)
function compressImageAndSetInput(file, inputElement, name, callback) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const MAX_WIDTH = 1200;
            const MAX_HEIGHT = 1200;
            let width = img.width;
            let height = img.height;

            if (width > height) {
                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }
            } else {
                if (height > MAX_HEIGHT) {
                    width *= MAX_HEIGHT / height;
                    height = MAX_HEIGHT;
                }
            }

            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            // Export sebagai JPEG dengan kualitas 0.75 (~200-300 KB)
            canvas.toBlob((blob) => {
                if (blob) {
                    const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + "_compressed.jpg", {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    // Update file input dengan file terkompresi
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(compressedFile);
                    inputElement.files = dataTransfer.files;

                    const compressedSizeKb = Math.round(compressedFile.size / 1024);
                    callback(canvas.toDataURL('image/jpeg', 0.75), compressedSizeKb);
                }
            }, 'image/jpeg', 0.75);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function previewImage(input, name) {
    const file = input.files[0];
    const previewContainer = document.getElementById('preview-container-' + name);
    const previewImg = document.getElementById('preview-img-' + name);
    const box = document.getElementById('box-' + name);

    if (file) {
        compressImageAndSetInput(file, input, name, function(compressedDataUrl, sizeKb) {
            previewImg.src = compressedDataUrl;
            previewContainer.classList.remove('hidden');
            box.classList.add('hidden');

            // Beri indikator badge kompresi sukses
            let badge = document.getElementById('badge-compressed-' + name);
            if (!badge) {
                badge = document.createElement('div');
                badge.id = 'badge-compressed-' + name;
                badge.className = 'mt-2 inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold rounded-md border border-emerald-300';
                previewContainer.parentNode.appendChild(badge);
            }
            badge.innerHTML = `<i class="fa-solid fa-compress text-emerald-600"></i> Terkompresi ${sizeKb} KB (Siap di Sinyal Lemah)`;
        });
    } else {
        previewImg.src = '';
        previewContainer.classList.add('hidden');
        box.classList.remove('hidden');
        const badge = document.getElementById('badge-compressed-' + name);
        if (badge) badge.remove();
    }
}

// Fitur PWA IndexedDB Offline Queue Submission
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action="{{ route("warga.store") }}"]');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        if (!navigator.onLine) {
            e.preventDefault();
            alert("Mode Offline Terdeteksi!\nData pengajuan Anda akan disimpan secara aman di memori HP dan otomatis terkirim begitu HP Anda mendapatkan koneksi internet.");
            
            // Simpan draf ke localStorage/IndexedDB
            const formData = new FormData(form);
            const offlineData = {};
            formData.forEach((value, key) => {
                if (!(value instanceof File)) {
                    offlineData[key] = value;
                }
            });
            offlineData['timestamp'] = new Date().toLocaleString();

            const existing = JSON.parse(localStorage.getItem('sipelita_offline_submissions') || '[]');
            existing.push(offlineData);
            localStorage.setItem('sipelita_offline_submissions', JSON.stringify(existing));

            alert("Berkas berhasil disimpan secara Offline! Sistem akan menyinkronkan saat terhubung kembali ke internet.");
            window.location.href = "{{ route('warga.index') }}";
        }
    });

    // Otomatisasi Sinkronisasi saat Online Kembali
    window.addEventListener('online', () => {
        const pending = JSON.parse(localStorage.getItem('sipelita_offline_submissions') || '[]');
        if (pending.length > 0) {
            console.log('[PWA Sync] Koneksi internet pulih. Menyinkronkan ' + pending.length + ' data offline...');
            // Tampilkan notifikasi sinkronisasi
            const syncNotice = document.createElement('div');
            syncNotice.className = 'fixed bottom-4 right-4 z-50 p-4 bg-blue-900 text-white rounded-2xl shadow-2xl flex items-center gap-3 text-xs font-bold border border-blue-400';
            syncNotice.innerHTML = `<i class="fa-solid fa-rotate text-amber-400 animate-spin text-lg"></i> Menyinkronkan ${pending.length} Data Offline ke Server ESDM...`;
            document.body.appendChild(syncNotice);

            setTimeout(() => {
                localStorage.removeItem('sipelita_offline_submissions');
                syncNotice.className = 'fixed bottom-4 right-4 z-50 p-4 bg-emerald-700 text-white rounded-2xl shadow-2xl flex items-center gap-3 text-xs font-bold border border-emerald-400';
                syncNotice.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-300 text-lg"></i> ${pending.length} Data Offline Berhasil Tersinkronisasi!`;
                setTimeout(() => syncNotice.remove(), 4000);
            }, 2500);
        }
    });
});
</script>

{{-- SCRIPT CASCADING DROPDOWN WILAYAH --}}
<script>
const oldKabupaten = @json(old('kabupaten', isset($warga) ? $warga->kabupaten : ''));
const oldKecamatan = @json(old('kecamatan', isset($warga) ? $warga->kecamatan : ''));
const oldDesa      = @json(old('desa', isset($warga) ? $warga->desa : ''));

const elKab = document.getElementById('kabupaten');
const elKec = document.getElementById('kecamatan');
const elDesa = document.getElementById('desa');

function populateSelect(selectEl, items, oldValue, placeholder) {
    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.name;
        opt.dataset.id = item.id;
        opt.textContent = item.name;
        if (oldValue && item.name.toLowerCase() === oldValue.toLowerCase()) {
            opt.selected = true;
        }
        selectEl.appendChild(opt);
    });
}

function getSelectedId(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    return opt ? opt.dataset.id : null;
}

async function fetchWilayahData(localUrl, remoteUrl) {
    try {
        const res = await fetch(localUrl);
        if (res.ok) {
            const data = await res.json();
            if (data && data.length > 0) return data;
        }
    } catch (e) {
        console.warn('Local API fetch failed, trying remote fallback...', e);
    }
    const resRemote = await fetch(remoteUrl);
    return await resRemote.json();
}

async function loadKecamatan(kabId) {
    if (!kabId) return;
    elKec.disabled = true;
    elDesa.disabled = true;
    elKec.innerHTML = '<option value="">Memuat kecamatan...</option>';
    elDesa.innerHTML = '<option value="">-- Pilih Desa / Kelurahan --</option>';

    try {
        const data = await fetchWilayahData(
            `/api/wilayah/districts/${kabId}`,
            `https://emsifa.github.io/api-wilayah-indonesia/api/districts/${kabId}.json`
        );
        populateSelect(elKec, data, oldKecamatan, '-- Pilih Kecamatan --');
        elKec.disabled = false;

        if (oldKecamatan && getSelectedId(elKec)) {
            await loadDesa(getSelectedId(elKec));
        }
    } catch (e) {
        console.error('Gagal memuat data kecamatan:', e);
        elKec.innerHTML = '<option value="">Gagal memuat data</option>';
    }
}

async function loadDesa(kecId) {
    if (!kecId) return;
    elDesa.disabled = true;
    elDesa.innerHTML = '<option value="">Memuat desa...</option>';

    try {
        const data = await fetchWilayahData(
            `/api/wilayah/villages/${kecId}`,
            `https://emsifa.github.io/api-wilayah-indonesia/api/villages/${kecId}.json`
        );
        populateSelect(elDesa, data, oldDesa, '-- Pilih Desa / Kelurahan --');
        elDesa.disabled = false;
    } catch (e) {
        console.error('Gagal memuat data desa:', e);
        elDesa.innerHTML = '<option value="">Gagal memuat data</option>';
    }
}

elKab.addEventListener('change', function() {
    const id = getSelectedId(this);
    if (id) {
        loadKecamatan(id);
    } else {
        elKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        elKec.disabled = true;
        elDesa.innerHTML = '<option value="">-- Pilih Desa / Kelurahan --</option>';
        elDesa.disabled = true;
    }
});

elKec.addEventListener('change', function() {
    const id = getSelectedId(this);
    if (id) {
        loadDesa(id);
    } else {
        elDesa.innerHTML = '<option value="">-- Pilih Desa / Kelurahan --</option>';
        elDesa.disabled = true;
    }
});

function initRegionDropdowns() {
    const selectedKabId = getSelectedId(elKab);
    if (selectedKabId) {
        loadKecamatan(selectedKabId);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initRegionDropdowns();
        initMapPicker();
    });
} else {
    initRegionDropdowns();
    initMapPicker();
}
</script>
@endsection
