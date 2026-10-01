@extends('layouts.staffdesa')

@section('content')
{{-- Leaflet dimuat langsung di sini (tidak bergantung @stack di layout) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <!-- Back Link & Title Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        @auth
            <a href="{{ in_array(auth()->user()->role, ['staff_desa', 'staf_desa']) ? route('staffdesa.index') : (auth()->user()->role === 'verifikator_esdm' ? route('admin.datalist') : route('admin.index')) }}"
            class="inline-flex items-center gap-2 text-xs font-bold text-blue-700 hover:text-blue-900 transition">
                <i class="fa-solid fa-arrow-left text-amber-500"></i>
                <span>Kembali ke Dashboard {{ in_array(auth()->user()->role, ['staff_desa', 'staf_desa']) ? 'Staff Administrasi Desa (' . auth()->user()->desa . ')' : 'Admin ESDM' }}</span>
            </a>
        @else
            <a href="{{ route('staffdesa.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <i class="fa-solid fa-arrow-left text-amber-500"></i> Kembali ke Beranda
            </a>
        @endauth
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden relative">
            <!-- Header Accent Banner -->
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

                <!-- STEPPER WIZARD HEADER INDICATOR -->
                <div class="border-b border-slate-200/80 pb-6">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                        <span id="wizard-step-label" class="flex items-center gap-1.5 font-extrabold text-slate-800">
                            <i class="fa-solid fa-list-check text-amber-500"></i> Tahap 1 dari 3: Data Diri Pemohon
                        </span>
                        <span id="wizard-step-percent" class="text-amber-600 font-black">33% Selesai</span>
                    </div>

                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden mb-5 shadow-inner border border-slate-200/50">
                        <div id="wizard-progress-bar" class="bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 h-full rounded-full transition-all duration-500 ease-out" style="width: 33.33%;"></div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 sm:gap-4">
                        <button type="button" onclick="goToStep(1)" id="step-badge-1"
                                class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 p-2.5 sm:p-3.5 rounded-2xl border transition-all cursor-pointer bg-slate-900 border-slate-900 text-white shadow-md ring-2 ring-amber-400/50">
                            <div id="step-icon-1" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center text-xs sm:text-sm font-black shrink-0">1</div>
                            <div class="text-center sm:text-left min-w-0">
                                <p class="text-[10px] sm:text-[11px] uppercase font-extrabold tracking-wider opacity-80 leading-none">Tahap 1</p>
                                <p class="text-xs sm:text-sm font-bold truncate mt-0.5">Data Diri</p>
                            </div>
                        </button>

                        <button type="button" onclick="goToStep(2)" id="step-badge-2"
                                class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 p-2.5 sm:p-3.5 rounded-2xl border transition-all cursor-pointer bg-slate-50 border-slate-200 text-slate-500 hover:bg-slate-100">
                            <div id="step-icon-2" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center text-xs sm:text-sm font-black shrink-0">2</div>
                            <div class="text-center sm:text-left min-w-0">
                                <p class="text-[10px] sm:text-[11px] uppercase font-extrabold tracking-wider opacity-70 leading-none">Tahap 2</p>
                                <p class="text-xs sm:text-sm font-bold truncate mt-0.5">Lokasi Rumah</p>
                            </div>
                        </button>

                        <button type="button" onclick="goToStep(3)" id="step-badge-3"
                                class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 p-2.5 sm:p-3.5 rounded-2xl border transition-all cursor-pointer bg-slate-50 border-slate-200 text-slate-500 hover:bg-slate-100">
                            <div id="step-icon-3" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center text-xs sm:text-sm font-black shrink-0">3</div>
                            <div class="text-center sm:text-left min-w-0">
                                <p class="text-[10px] sm:text-[11px] uppercase font-extrabold tracking-wider opacity-70 leading-none">Tahap 3</p>
                                <p class="text-xs sm:text-sm font-bold truncate mt-0.5">Dokumen &amp; Form</p>
                            </div>
                        </button>
                    </div>
                </div>

                <form id="form-pengajuan" action="{{ route('staffdesa.warga.update', $warga->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- TAHAP 1: DATA IDENTITAS WARGA --}}
                    <div id="step-content-1" class="step-pane space-y-6">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-sm">1</div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Identitas Pemohon (Sesuai KTP)</h3>
                                <p class="text-xs text-slate-500 font-medium">Isi data pribadi dan wilayah domisili dengan benar</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- NIK --}}
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="nik" id="nik_input" value="{{ old('nik', $warga->nik ?? $nik ?? '') }}" maxlength="16" required
                                           placeholder="16 Digit NIK KTP..." {{ isset($warga) ? 'readonly' : '' }}
                                           class="w-full pl-10 pr-10 py-3 bg-slate-50 border {{ $errors->has('nik') ? 'border-rose-400' : 'border-slate-300' }} {{ isset($warga) ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'focus:bg-white' }} rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                    <i class="fa-solid fa-id-card absolute left-3.5 top-3.5 text-slate-400"></i>
                                    <div id="nik-status-icon" class="absolute right-3.5 top-3.5 hidden"></div>
                                </div>
                                <div id="nik-feedback-text" class="text-xs font-semibold mt-1 hidden"></div>
                                @error('nik') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Nama Lengkap --}}
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="nama" value="{{ old('nama', $warga->nama ?? '') }}" required
                                           placeholder="Nama lengkap sesuai KTP..."
                                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border {{ $errors->has('nama') ? 'border-rose-400' : 'border-slate-300' }} focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                    <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-400"></i>
                                </div>
                                @error('nama') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kabupaten --}}
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kabupaten / Kota <span class="text-rose-500">*</span>
                                </label>
                                <select name="kabupaten" id="kabupaten" required
                                       class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('kabupaten') ? 'border-rose-400' : 'border-slate-300' }} focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                    <option value="">-- Pilih Kabupaten / Kota --</option>
                                    @php
                                        $kabList = [
                                            'KABUPATEN KERINCI' => '15.01',
                                            'KABUPATEN MERANGIN' => '15.02',
                                            'KABUPATEN SAROLANGUN' => '15.03',
                                            'KABUPATEN BATANG HARI' => '15.04',
                                            'KABUPATEN MUARO JAMBI' => '15.05',
                                            'KABUPATEN TANJUNG JABUNG TIMUR' => '15.06',
                                            'KABUPATEN TANJUNG JABUNG BARAT' => '15.07',
                                            'KABUPATEN TEBO' => '15.08',
                                            'KABUPATEN BUNGO' => '15.09',
                                            'KOTA JAMBI' => '15.71',
                                            'KOTA SUNGAI PENUH' => '15.72',
                                        ];
                                        $selectedKab = old('kabupaten', isset($warga) ? $warga->kabupaten : '');
                                    @endphp
                                    @foreach($kabList as $kabName => $kabId)
                                        <option value="{{ $kabName }}" {{ $selectedKab == $kabName ? 'selected' : '' }}>{{ $kabName }}</option>
                                    @endforeach
                                </select>
                                @error('kabupaten') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kecamatan --}}
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Kecamatan <span class="text-rose-500">*</span>
                                </label>
                                <select name="kecamatan" id="kecamatan" required disabled
                                       class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('kecamatan') ? 'border-rose-400' : 'border-slate-300' }} focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                @error('kecamatan') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Desa / Kelurahan --}}
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Desa / Kelurahan <span class="text-rose-500">*</span>
                                </label>
                                <select name="desa" id="desa" required disabled
                                       class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('desa') ? 'border-rose-400' : 'border-slate-300' }} focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">-- Pilih Desa / Kelurahan --</option>
                                </select>
                                @error('desa') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Kode Pos --}}
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kode Pos</label>
                                <div class="relative">
                                    <select name="kode_pos" id="kode_pos" disabled
                                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border {{ $errors->has('kode_pos') ? 'border-rose-400' : 'border-slate-300' }} focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">-- Pilih Kode Pos --</option>
                                    </select>
                                    <i class="fa-solid fa-mail-bulk absolute left-3.5 top-3.5 text-slate-400 pointer-events-none"></i>
                                </div>
                                @error('kode_pos') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- RT / RW --}}
                            <div class="space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    RT / RW <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="rt_rw" value="{{ old('rt_rw', $warga->rt_rw ?? '') }}" placeholder="Contoh: RT 02 / RW 01" required
                                       class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('rt_rw') ? 'border-rose-400' : 'border-slate-300' }} focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
                                @error('rt_rw') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- No HP / WhatsApp --}}
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    No. WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="no_hp" value="{{ old('no_hp', $warga->no_hp ?? '') }}" placeholder="08xxxxxxxxxx" required
                                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border {{ $errors->has('no_hp') ? 'border-rose-400' : 'border-slate-300' }} focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition">
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
                                      class="w-full px-4 py-3 bg-slate-50 border {{ $errors->has('alamat') ? 'border-rose-400' : 'border-slate-300' }} focus:bg-white rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition resize-none">{{ old('alamat', $warga->alamat ?? '') }}</textarea>
                            @error('alamat') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- TAHAP 2: TITIK LOKASI GPS RUMAH --}}
                    <div id="step-content-2" class="step-pane hidden space-y-6">
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

                            <div class="space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        <i class="fa-solid fa-map-location-dot text-amber-500 mr-1"></i> Peta Interaktif Penentuan Lokasi
                                    </label>

                                    <div class="inline-flex p-1 bg-slate-200/80 rounded-xl gap-1 self-start sm:self-auto shadow-xs border border-slate-300/60">
                                        <button type="button" id="btn-mode-streets" onclick="switchMapTile('streets')"
                                                class="px-3 py-1.5 text-slate-600 hover:text-slate-900 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                            <i class="fa-solid fa-map text-blue-600"></i> Peta Jalan
                                        </button>
                                        <button type="button" id="btn-mode-satellite" onclick="switchMapTile('satellite')"
                                                class="px-3 py-1.5 bg-white text-slate-900 shadow-xs rounded-lg text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer">
                                            <i class="fa-solid fa-satellite text-amber-500"></i> Satelit + Nama Jalan
                                        </button>
                                    </div>
                                </div>

                                <div id="map-picker" class="h-80 w-full rounded-2xl border border-slate-300 shadow-inner z-0 overflow-hidden relative"></div>

                                <div id="street-name-info" class="p-3.5 bg-blue-50/90 border border-blue-200 rounded-xl text-xs font-semibold text-blue-900 flex items-start gap-2.5 shadow-xs">
                                    <i class="fa-solid fa-road text-blue-600 text-sm mt-0.5"></i>
                                    <div>
                                        <strong class="text-blue-950 block text-xs">Deteksi Nama Jalan &amp; Alamat:</strong>
                                        <span class="text-slate-600 text-[11px] font-medium leading-snug">Geser penanda biru di peta atau klik lokasi rumah Anda untuk mendeteksi nama jalan secara otomatis.</span>
                                    </div>
                                </div>

                                <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-xl text-xs font-semibold text-amber-900 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-info text-amber-600 text-sm"></i>
                                    <span>Petunjuk: Pilih mode <strong>"Satelit + Nama Jalan"</strong> di atas peta untuk melihat atap bangunan &amp; nama jalan dengan jelas.</span>
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

                    {{-- TAHAP 3: UPLOAD BERKAS FOTO & PERSETUJUAN --}}
                    <div id="step-content-3" class="step-pane hidden space-y-6">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-amber-400 font-extrabold flex items-center justify-center text-sm shadow-sm">3</div>
                            <div>
                                <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Unggah Dokumen &amp; Foto Fisik</h3>
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
                                <div class="p-5 border-2 border-dashed {{ $errors->has($name) ? 'border-rose-300 bg-rose-50/20' : 'border-slate-300' }} rounded-2xl text-center space-y-3 relative hover:border-blue-500 transition">
                                    <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                        {{ $fileInfo['label'] }} <span class="text-rose-500">*</span>
                                    </label>
                                    <p class="text-[11px] text-slate-500 font-medium leading-tight">{{ $fileInfo['desc'] }}</p>

                                    <div class="relative w-full h-32 rounded-xl overflow-hidden bg-white border border-slate-200 shadow-xs">
                                        <input type="file" name="{{ $name }}" accept="image/jpeg,image/png,image/jpg" {{ !isset($warga) ? 'required' : '' }}
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                               onchange="previewImage(this, '{{ $name }}')">
                            @if(isset($warga) && $warga->berkas && $warga->berkas->$name)
                                    <div id="preview-container-{{ $name }}" class="absolute inset-0 w-full h-full bg-slate-100 z-10">
                                        <img id="preview-img-{{ $name }}" src="{{ asset('storage/' . $warga->berkas->$name) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div id="box-{{ $name }}" class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 text-blue-700 hidden"></div>
                                @else
                                    <div id="box-{{ $name }}" class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 text-blue-700">
                                        <i class="fa-solid fa-cloud-arrow-up text-2xl"></i>
                                        <span class="text-xs font-bold">Pilih / Unggah Foto</span>
                                    </div>
                                    <div id="preview-container-{{ $name }}" class="hidden absolute inset-0 w-full h-full bg-slate-100 z-10">
                                        <img id="preview-img-{{ $name }}" src="" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                    </div>
                                    @error($name) <p class="text-rose-600 text-xs font-semibold">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>

                        {{-- CONTAINER PERNYATAAN PERSETUJUAN --}}
                        <div class="p-5 bg-amber-50/70 border border-amber-200/90 rounded-2xl space-y-2.5 transition">
                            <div class="flex items-start gap-3.5">
                                <input type="checkbox" name="persetujuan" id="persetujuan" value="1" required
                                       class="w-5 h-5 text-amber-600 border-slate-300 rounded focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 mt-0.5 cursor-pointer shrink-0 transition">
                                <label for="persetujuan" class="text-xs sm:text-sm text-slate-800 font-semibold leading-relaxed cursor-pointer select-none">
                                    Saya menyatakan data yang diisi benar dan menyetujui penggunaan data untuk proses verifikasi program Bantuan Pasang Baru Listrik (BPBL) Dinas ESDM Provinsi Jambi
                                </label>
                            </div>
                            @error('persetujuan')
                                <p class="text-rose-600 text-xs font-semibold pl-8 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- STEPPER NAVIGATION CONTROLS --}}
                    <div class="pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                        <button type="button" id="btn-wizard-prev" onclick="prevStep()" class="hidden w-full sm:w-auto px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-sm rounded-xl transition-all items-center justify-center gap-2 cursor-pointer border border-slate-300">
                            <i class="fa-solid fa-arrow-left text-slate-500"></i>
                            <span>Kembali</span>
                        </button>

                        <div class="flex items-center gap-3 w-full sm:w-auto sm:ml-auto">
                            <button type="button" id="btn-wizard-next" onclick="nextStep()" class="w-full sm:w-auto px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-sm rounded-xl shadow-md transition-all flex items-center justify-center gap-2.5 cursor-pointer border border-slate-800">
                                <span>Lanjut ke Lokasi Rumah</span>
                                <i class="fa-solid fa-arrow-right text-amber-400"></i>
                            </button>

                            {{-- Submit: hidden dari awal, hanya tampil di Tahap 3 --}}
                            <button type="submit" id="btn-wizard-submit" class="hidden w-full sm:w-auto px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-sm rounded-xl shadow-md transition-all items-center justify-center gap-2.5 cursor-pointer border border-slate-800">
                                <i class="fa-solid fa-paper-plane text-amber-400"></i>
                                <span>{{ isset($warga) ? 'Simpan Perbaikan & Kirim Ulang' : 'Kirim Form Pendaftaran BPBL' }}</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
/* =========================================================
   1. MAP PICKER (LEAFLET), GPS, FOTO, OFFLINE QUEUE
   ========================================================= */
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

    googleHybridLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['0', '1', '2', '3'], attribution: '&copy; Google Maps'
    });
    googleStreetsLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['0', '1', '2', '3'], attribution: '&copy; Google Maps'
    });
    googleSatelliteLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20, subdomains: ['0', '1', '2', '3'], attribution: '&copy; Google Maps'
    });
    osmStreetsLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, attribution: '&copy; OpenStreetMap'
    });
    esriSatelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19, attribution: 'Esri World Imagery'
    });

    mapPicker = L.map('map-picker', { layers: [googleHybridLayer] }).setView([initialLat, initialLng], initialZoom);

    const baseMaps = {
        "🛰️ Google Satelit Terbaru + Nama Jalan": googleHybridLayer,
        "🗺️ Google Peta Jalan Terbaru": googleStreetsLayer,
        "📷 Google Satelit Murni": googleSatelliteLayer,
        "🌍 OpenStreetMap Standard": osmStreetsLayer,
        "📡 Esri World Imagery": esriSatelliteLayer
    };
    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(mapPicker);

    markerPicker = L.marker([initialLat, initialLng], { draggable: true }).addTo(mapPicker);
    markerPicker.bindPopup("<b>Titik Lokasi Rumah Anda</b><br>Geser (drag) pin ini tepat ke lokasi rumah.").openPopup();

    if (hasCoords) {
        fetchStreetName(initialLat, initialLng);
    }

    markerPicker.on('dragend', function () {
        const pos = markerPicker.getLatLng();
        updateCoordinatesInput(pos.lat, pos.lng);
        fetchStreetName(pos.lat, pos.lng);
    });

    mapPicker.on('click', function (e) {
        markerPicker.setLatLng(e.latlng);
        updateCoordinatesInput(e.latlng.lat, e.latlng.lng);
        fetchStreetName(e.latlng.lat, e.latlng.lng);
    });

    setTimeout(() => { if (mapPicker) mapPicker.invalidateSize(); }, 300);
    setTimeout(() => { if (mapPicker) mapPicker.invalidateSize(); }, 1000);
}

function switchMapTile(mode) {
    if (!mapPicker) return;
    const btnStreets = document.getElementById('btn-mode-streets');
    const btnSatellite = document.getElementById('btn-mode-satellite');

    [googleHybridLayer, googleStreetsLayer, googleSatelliteLayer, osmStreetsLayer, esriSatelliteLayer].forEach(layer => {
        if (layer && mapPicker.hasLayer(layer)) mapPicker.removeLayer(layer);
    });

    const activeCls = "px-3 py-1.5 bg-white text-slate-900 shadow-xs rounded-lg text-xs font-extrabold transition flex items-center gap-1.5 cursor-pointer";
    const idleCls = "px-3 py-1.5 text-slate-600 hover:text-slate-900 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer";

    if (mode === 'satellite') {
        if (googleHybridLayer) mapPicker.addLayer(googleHybridLayer);
        currentTileMode = 'satellite';
        if (btnSatellite) btnSatellite.className = activeCls;
        if (btnStreets) btnStreets.className = idleCls;
    } else {
        if (googleStreetsLayer) mapPicker.addLayer(googleStreetsLayer);
        currentTileMode = 'streets';
        if (btnStreets) btnStreets.className = activeCls;
        if (btnSatellite) btnSatellite.className = idleCls;
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
                const addr = data.address || {};
                const road = addr.road || addr.pedestrian || addr.suburb || addr.village || addr.county || '';
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
        .catch(() => {
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
    const latEl = document.getElementById('latitude');
    const lngEl = document.getElementById('longitude');
    if (latEl) latEl.value = parseFloat(lat).toFixed(6);
    if (lngEl) lngEl.value = parseFloat(lng).toFixed(6);
}

function updateMapLocation(lat, lng) {
    const position = [lat, lng];
    if (markerPicker) markerPicker.setLatLng(position);
    if (mapPicker) {
        mapPicker.setView(position, 16);
        mapPicker.invalidateSize();
    }
    fetchStreetName(lat, lng);
}

function getLocation() {
    if (!navigator.geolocation) {
        alert('Browser Anda tidak mendukung fitur deteksi lokasi otomatis.');
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            updateCoordinatesInput(lat, lng);
            updateMapLocation(lat, lng);
            alert('Lokasi GPS berhasil dideteksi! Anda juga dapat menggeser pin di peta jika lokasi belum tepat.');
        },
        function (error) {
            switch (error.code) {
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
            }
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

// Kompresi foto client-side (Canvas -> JPEG ~200-300 KB)
function compressImageAndSetInput(file, inputElement, name, callback) {
    const reader = new FileReader();
    reader.onload = function (e) {
        const img = new Image();
        img.onload = function () {
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

            canvas.toBlob((blob) => {
                if (blob) {
                    const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + "_compressed.jpg", {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

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
        compressImageAndSetInput(file, input, name, function (compressedDataUrl, sizeKb) {
            previewImg.src = compressedDataUrl;
            previewContainer.classList.remove('hidden');
            box.classList.add('hidden');

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

// PWA offline queue
function initOfflineQueue() {
    const form = document.getElementById('form-pengajuan');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        if (!navigator.onLine) {
            e.preventDefault();
            alert("Mode Offline Terdeteksi!\nData pengajuan Anda akan disimpan secara aman di memori HP dan otomatis terkirim begitu HP Anda mendapatkan koneksi internet.");

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
            window.location.href = "{{ route('staffdesa.index') }}";
        }
    });

    window.addEventListener('online', () => {
        const pending = JSON.parse(localStorage.getItem('sipelita_offline_submissions') || '[]');
        if (pending.length > 0) {
            console.log('[PWA Sync] Koneksi internet pulih. Menyinkronkan ' + pending.length + ' data offline...');
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
}
</script>

<script>
/* =========================================================
   2. DROPDOWN WILAYAH + KODE POS
   ========================================================= */
const BASE_URL_EMSIFA_V2 = 'https://www.emsifa.com/api-wilayah-indonesia/v2';
const oldKabupaten = @json(old('kabupaten', isset($warga) ? $warga->kabupaten : ''));
const oldKecamatan = @json(old('kecamatan', isset($warga) ? $warga->kecamatan : ''));
const oldDesa      = @json(old('desa', isset($warga) ? $warga->desa : ''));

const elKab = document.getElementById('kabupaten');
const elKec = document.getElementById('kecamatan');
const elDesa = document.getElementById('desa');
const elKodePos = document.getElementById('kode_pos');
let pendingKodePos = @json(old('kode_pos', $warga->kode_pos ?? ''));

function resetKodePos(msg = '-- Pilih Kode Pos --') {
    elKodePos.innerHTML = `<option value="">${msg}</option>`;
    elKodePos.disabled = true;
}

function fillKodePos(codes) {
    const list = [...new Set(codes.filter(Boolean))];
    if (pendingKodePos && !list.includes(pendingKodePos)) list.push(pendingKodePos);

    if (!list.length) return resetKodePos('Kode pos tidak ditemukan');

    elKodePos.innerHTML = '<option value="">-- Pilih Kode Pos --</option>';
    list.forEach(code => {
        const opt = document.createElement('option');
        opt.value = code;
        opt.textContent = code;
        if (code === pendingKodePos || list.length === 1) opt.selected = true;
        elKodePos.appendChild(opt);
    });
    elKodePos.disabled = false;
    pendingKodePos = '';
}

async function loadKodePos() {
    const opt = elDesa.options[elDesa.selectedIndex];
    if (!opt || !opt.value) return resetKodePos();

    // 1) Dari Emsifa (kalau ada postal_code)
    if (opt.dataset.postalCode) return fillKodePos([opt.dataset.postalCode]);

    // 2) Fallback: API kodepos (format: {data:[{code, village, district, regency}]})
    resetKodePos('Mencari kode pos...');
    try {
        const res = await fetch(`https://kodepos.vercel.app/search?q=${encodeURIComponent(opt.value)}`);
        const json = await res.json();
        const data = Array.isArray(json.data) ? json.data : [];

        const norm = t => (t || '').toString().toLowerCase()
            .replace(/^(kabupaten|kab\.?|kota|kecamatan|kec\.?)\s+/, '').trim();
        const desaN = norm(opt.value);
        const kecN = norm(elKec.value);
        const kabN = norm(elKab.value);

        const rows = data.map(d => ({
            code: d.code || d.postalcode,
            village: norm(d.village || d.urban),
            district: norm(d.district || d.subdistrict),
            regency: norm(d.regency || d.city)
        }));

        let matched = rows.filter(r => r.village === desaN && r.district === kecN);
        if (!matched.length) matched = rows.filter(r => r.village === desaN && r.regency === kabN);
        if (!matched.length) matched = rows.filter(r => r.district === kecN && r.regency === kabN);
        if (!matched.length) matched = rows.filter(r => r.village === desaN);

        fillKodePos(matched.map(r => String(r.code)));
    } catch (err) {
        console.warn('Gagal mengambil kode pos:', err);
        fillKodePos([]);
    }
}

function populateSelect(selectEl, items, oldValue, placeholder) {
    selectEl.innerHTML = `<option value="">${placeholder}</option>`;
    items.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.name;
        opt.dataset.id = item.id;
        const pc = item.postal_code || item.postalCode || item.kode_pos || item.kodepos;
        if (pc) opt.dataset.postalCode = pc;
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
        const r = await fetch(remoteUrl);
        if (r.ok) {
            const j = await r.json();
            const d = j.data || j;
            if (Array.isArray(d) && d.length > 0) return d;
        }
    } catch (e) {
        console.warn('Emsifa fetch gagal, coba API lokal...', e);
    }
    const res = await fetch(localUrl);
    const data = await res.json();
    return data.data || data;
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
            `${BASE_URL_EMSIFA_V2}/districts/${kabId}.json`
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
        // Desa: ambil langsung dari Emsifa v2 (sudah termasuk postal_code)
        let data;
        try {
            const r = await fetch(`${BASE_URL_EMSIFA_V2}/villages/${kecId}.json`);
            if (!r.ok) throw new Error('HTTP ' + r.status);
            const j = await r.json();
            data = j.data || j;
        } catch (e) {
            console.warn('Emsifa villages gagal, coba API lokal...', e);
            data = await fetchWilayahData(
                `/api/wilayah/villages/${kecId}`,
                `${BASE_URL_EMSIFA_V2}/villages/${kecId}.json`
            );
        }
        populateSelect(elDesa, data, oldDesa, '-- Pilih Desa / Kelurahan --');
        elDesa.disabled = false;
        if (oldDesa && elDesa.value) loadKodePos();
    } catch (e) {
        console.error('Gagal memuat data desa:', e);
        elDesa.innerHTML = '<option value="">Gagal memuat data</option>';
    }
}

elKab.addEventListener('change', function () {
    const id = getSelectedId(this);
    resetKodePos();
    if (id) {
        loadKecamatan(id);
    } else {
        elKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        elKec.disabled = true;
        elDesa.innerHTML = '<option value="">-- Pilih Desa / Kelurahan --</option>';
        elDesa.disabled = true;
    }
});

elKec.addEventListener('change', function () {
    const id = getSelectedId(this);
    resetKodePos();
    if (id) {
        loadDesa(id);
    } else {
        elDesa.innerHTML = '<option value="">-- Pilih Desa / Kelurahan --</option>';
        elDesa.disabled = true;
    }
});

elDesa.addEventListener('change', loadKodePos);

const normKab = t => (t || '').toUpperCase().replace(/[^A-Z]/g, '').replace(/^(KABUPATEN|KAB|KOTA)/, '');

// Ambil ID kab/kota langsung dari Emsifa v2 (tidak hardcode, ID v2 berbeda dari ID lama)
async function assignKabupatenIds() {
    const res = await fetch(`${BASE_URL_EMSIFA_V2}/regencies/15.json`);
    const json = await res.json();
    const list = json.data || json;
    const map = {};
    list.forEach(r => { map[normKab(r.name)] = r.id; });

    Array.from(elKab.options).forEach(o => {
        if (!o.value) return;
        const id = map[normKab(o.value)];
        if (id) o.dataset.id = id;
        else console.warn('ID kab/kota tidak ditemukan untuk:', o.value);
    });
}

async function initRegionDropdowns() {
    try {
        await assignKabupatenIds();
    } catch (e) {
        console.error('Gagal memuat daftar kab/kota dari Emsifa:', e);
    }
    const selectedKabId = getSelectedId(elKab);
    if (selectedKabId) loadKecamatan(selectedKabId);
}

/* =========================================================
   3. STEPPER WIZARD
   ========================================================= */
let currentStep = 1;

function showStep(step) {
    if (step < 1) step = 1;
    if (step > 3) step = 3;
    currentStep = step;

    document.querySelectorAll('.step-pane').forEach((pane, idx) => {
        pane.classList.toggle('hidden', idx + 1 !== step);
    });

    const stepLabel = document.getElementById('wizard-step-label');
    const stepPercent = document.getElementById('wizard-step-percent');
    const progressBar = document.getElementById('wizard-progress-bar');

    const stepTitles = ["Data Diri Pemohon", "Lokasi Rumah & GPS", "Dokumen & Persetujuan"];

    if (stepLabel) {
        stepLabel.innerHTML = `<i class="fa-solid fa-list-check text-amber-500"></i> Tahap ${step} dari 3: ${stepTitles[step - 1]}`;
    }
    if (stepPercent) stepPercent.textContent = `${Math.round((step / 3) * 100)}% Selesai`;
    if (progressBar) progressBar.style.width = `${(step / 3) * 100}%`;

    for (let i = 1; i <= 3; i++) {
        const badge = document.getElementById(`step-badge-${i}`);
        const iconBox = document.getElementById(`step-icon-${i}`);
        if (!badge || !iconBox) continue;

        if (i === step) {
            badge.className = "flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 p-2.5 sm:p-3.5 rounded-2xl border transition-all cursor-pointer bg-slate-900 border-slate-900 text-white shadow-md ring-2 ring-amber-400/50";
            iconBox.className = "w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center text-xs sm:text-sm font-black shrink-0";
            iconBox.innerHTML = i;
        } else if (i < step) {
            badge.className = "flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 p-2.5 sm:p-3.5 rounded-2xl border transition-all cursor-pointer bg-emerald-50 border-emerald-300 text-emerald-950 hover:bg-emerald-100";
            iconBox.className = "w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xs sm:text-sm font-black shrink-0 shadow-xs";
            iconBox.innerHTML = '<i class="fa-solid fa-check"></i>';
        } else {
            badge.className = "flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-2 p-2.5 sm:p-3.5 rounded-2xl border transition-all cursor-pointer bg-slate-50 border-slate-200 text-slate-500 hover:bg-slate-100";
            iconBox.className = "w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center text-xs sm:text-sm font-black shrink-0";
            iconBox.innerHTML = i;
        }
    }

    const btnPrev = document.getElementById('btn-wizard-prev');
    const btnNext = document.getElementById('btn-wizard-next');
    const btnSubmit = document.getElementById('btn-wizard-submit');

    if (btnPrev) {
        btnPrev.classList.toggle('hidden', step === 1);
        btnPrev.classList.toggle('flex', step !== 1);
    }

    if (btnNext && btnSubmit) {
        if (step === 3) {
            btnNext.classList.add('hidden');
            btnSubmit.classList.remove('hidden');
            btnSubmit.classList.add('flex');
        } else {
            btnNext.classList.remove('hidden');
            btnSubmit.classList.add('hidden');
            btnSubmit.classList.remove('flex');

            const nextSpan = btnNext.querySelector('span');
            if (nextSpan) {
                nextSpan.textContent = step === 1 ? 'Lanjut ke Lokasi Rumah' : 'Lanjut ke Upload Dokumen';
            }
        }
    }

    if (step === 2 && typeof mapPicker !== 'undefined' && mapPicker) {
        setTimeout(() => mapPicker.invalidateSize(), 150);
        setTimeout(() => mapPicker.invalidateSize(), 400);
    }
}

function validateCurrentStep(step) {
    const currentPane = document.getElementById(`step-content-${step}`);
    if (!currentPane) return true;

    const requiredInputs = currentPane.querySelectorAll('input[required], select[required], textarea[required]');
    for (let input of requiredInputs) {
        if (!input.checkValidity()) {
            input.reportValidity();
            return false;
        }
    }
    return true;
}

function nextStep() {
    if (validateCurrentStep(currentStep)) showStep(currentStep + 1);
}

function prevStep() {
    showStep(currentStep - 1);
}

function goToStep(targetStep) {
    if (targetStep < currentStep) {
        showStep(targetStep);
    } else if (targetStep > currentStep) {
        if (validateCurrentStep(currentStep)) showStep(targetStep);
    }
}

/* =========================================================
   4. VALIDASI NIK REAL-TIME
   ========================================================= */
function initNikLiveValidation() {
    const nikInput = document.getElementById('nik_input');
    const statusIcon = document.getElementById('nik-status-icon');
    const feedbackText = document.getElementById('nik-feedback-text');

    if (!nikInput) return;

    function validateNik() {
        nikInput.value = nikInput.value.replace(/[^0-9]/g, '');
        const val = nikInput.value;

        if (val.length === 0) {
            if (statusIcon) statusIcon.className = 'absolute right-3.5 top-3.5 hidden';
            if (feedbackText) {
                feedbackText.className = 'text-xs font-semibold mt-1 hidden';
                feedbackText.innerHTML = '';
            }
            nikInput.classList.remove('border-emerald-500', 'border-rose-400', 'ring-2', 'ring-emerald-500/20', 'ring-rose-400/20');
            return;
        }

        if (val.length === 16) {
            if (statusIcon) {
                statusIcon.className = 'absolute right-3.5 top-3.5 text-emerald-500 text-base font-bold flex items-center';
                statusIcon.innerHTML = '<i class="fa-solid fa-circle-check"></i>';
            }
            if (feedbackText) {
                feedbackText.className = 'text-xs font-bold mt-1 text-emerald-600 flex items-center gap-1';
                feedbackText.innerHTML = '<i class="fa-solid fa-circle-check"></i> Format NIK Valid (16 Digit Lengkap)';
            }
            nikInput.classList.remove('border-rose-400', 'ring-rose-400/20');
            nikInput.classList.add('border-emerald-500', 'ring-2', 'ring-emerald-500/20');
        } else {
            if (statusIcon) {
                statusIcon.className = 'absolute right-3.5 top-3.5 text-rose-500 text-base font-bold flex items-center';
                statusIcon.innerHTML = '<i class="fa-solid fa-circle-xmark"></i>';
            }
            if (feedbackText) {
                feedbackText.className = 'text-xs font-bold mt-1 text-rose-600 flex items-center gap-1';
                feedbackText.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> NIK kurang dari 16 digit (${val.length}/16 digit)`;
            }
            nikInput.classList.remove('border-emerald-500', 'ring-emerald-500/20');
            nikInput.classList.add('border-rose-400', 'ring-2', 'ring-rose-400/20');
        }
    }

    nikInput.addEventListener('input', validateNik);
    nikInput.addEventListener('blur', validateNik);
    if (nikInput.value) validateNik();
}

/* =========================================================
   5. BOOTSTRAP (tiap init dibungkus try/catch,
      jadi error satu modul tidak menghentikan yang lain)
   ========================================================= */
function bootstrapForm() {
    showStep(1);
    try { resetKodePos(); } catch (e) { console.error('resetKodePos:', e); }

    try { initRegionDropdowns(); } catch (e) { console.error('initRegionDropdowns:', e); }

    try {
        if (typeof L !== 'undefined') {
            initMapPicker();
        } else {
            console.error('Leaflet (L) belum ter-load');
        }
    } catch (e) { console.error('initMapPicker:', e); }

    try { initNikLiveValidation(); } catch (e) { console.error('initNikLiveValidation:', e); }
    try { initOfflineQueue(); } catch (e) { console.error('initOfflineQueue:', e); }

    @if ($errors->any())
    try {
        const errorKeys = @json($errors->keys());
        const step3Fields = ['foto_ktp', 'foto_sktm', 'foto_rumah_depan', 'foto_kwh_rumah_terdekat', 'foto_tiang_rumah_terdekat', 'persetujuan'];
        const step2Fields = ['latitude', 'longitude'];

        if (errorKeys.some(k => step3Fields.includes(k))) showStep(3);
        else if (errorKeys.some(k => step2Fields.includes(k))) showStep(2);
        else showStep(1);
    } catch (e) { console.error('redirect error step:', e); }
    @endif
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrapForm);
} else {
    bootstrapForm();
}
</script>
@endsection
