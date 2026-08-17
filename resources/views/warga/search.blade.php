@extends('layouts.app')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-8">

        <!-- Back link -->
        <div>
            <a href="{{ route('warga.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                <i class="fa-solid fa-arrow-left text-amber-500"></i> Kembali ke Beranda
            </a>
        </div>

        {{-- Card Pencarian NIK --}}
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
            <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-amber-400 flex items-center justify-center text-xl font-bold shadow-md">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Cek Status Berkas Pendaftaran NIK</h2>
                    <p class="text-xs text-slate-500 font-medium">Masukkan 16 digit Nomor Induk Kependudukan (NIK) Anda untuk melacak posisi bantuan</p>
                </div>
            </div>

            <form action="{{ route('warga.search') }}" method="GET" class="space-y-4">
                <div class="space-y-2">
                    <label for="search_nik_input" class="block text-xs font-bold text-slate-800 uppercase tracking-wider">Nomor Induk Kependudukan (NIK)</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <input type="text" id="search_nik_input" name="nik" value="{{ $nik ?? '' }}" maxlength="16" required
                                   placeholder="Contoh: 150101XXXXXXXXXX"
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border @error('nik') border-rose-400 @else border-slate-300 @enderror focus:bg-white rounded-2xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm font-semibold text-slate-900 transition">
                            <i class="fa-solid fa-fingerprint absolute left-4 top-4 text-slate-400 text-base"></i>
                        </div>
                        <button type="submit" class="px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-sm rounded-2xl shadow-md transition flex items-center justify-center gap-2 border border-slate-800 cursor-pointer">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Lacak Berkas
                        </button>
                    </div>
                    @error('nik')
                        <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>

        {{-- Alert Notifikasi Sukses Pendaftaran --}}
        @if(session('success'))
            <div class="p-5 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs sm:text-sm flex items-start gap-3.5 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base font-bold shrink-0 shadow-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-emerald-950">Pendaftaran Berhasil Terkirim!</h4>
                    <p class="text-xs text-emerald-800 mt-1 leading-relaxed font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Kondisi 1: Data Warga Ditemukan --}}
        @if(isset($warga) && $warga)
            @php
                // Tentukan step aktif (1-4) berdasarkan status_verifikasi
                $currentStep = 1;
                if(in_array($warga->status_verifikasi, ['terkirim', 'pending'])) {
                    $currentStep = 1; // Step 1: Pendaftaran Terkirim
                } elseif($warga->status_verifikasi === 'disetujui_desa') {
                    $currentStep = 2; // Step 2: Disetujui Kepala Desa
                } elseif($warga->status_verifikasi === 'lolos_verifikasi_pusat') {
                    $currentStep = 3; // Step 3: Verifikasi Dinas ESDM
                } elseif($warga->status_verifikasi === 'terpasang') {
                    $currentStep = 4; // Step 4: KWH Meter PLN Terpasang
                }
            @endphp

            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-8">
                <!-- Header Profil Pemohon & Tombol Unduh PDF -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-slate-100 pb-5 gap-4">
                    <div>
                        <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Identitas Pemohon Terdaftar</span>
                        <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ $warga->nama }}</h3>
                        <p class="text-xs text-slate-600 font-semibold mt-1 flex flex-wrap items-center gap-2">
                            <span><i class="fa-solid fa-id-card text-slate-400"></i> NIK: {{ substr($warga->nik, 0, 4) }}************</span>
                            <span>•</span>
                            <span>Desa {{ $warga->desa }}, Kec. {{ $warga->kecamatan }}, Kab. {{ $warga->kabupaten }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        <!-- Tombol Unduh Bukti PDF -->
                        <a href="{{ route('warga.bukti.pdf', ['nik' => $warga->nik]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-amber-400 text-xs font-extrabold rounded-xl transition shadow-sm border border-slate-800">
                            <i class="fa-solid fa-file-pdf text-rose-400 text-sm"></i>
                            <span>Unduh Bukti PDF</span>
                        </a>

                        @if($warga->status_verifikasi === 'terpasang' || $warga->status_verifikasi === 'lolos_verifikasi_pusat')
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-extrabold rounded-xl">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ $warga->status_verifikasi === 'terpasang' ? 'Terpasang (PLN)' : 'Terverifikasi (ESDM)' }}
                            </span>
                        @elseif($warga->status_verifikasi === 'ditolak/perlu_perbaikan')
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 text-rose-800 border border-rose-200 text-xs font-extrabold rounded-xl">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                                Perlu Perbaikan Berkas
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-extrabold rounded-xl">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Dalam Proses Verifikasi
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Visual Stepper Tracking Status -->
                <div class="space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-200/80">
                    <h4 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-bars-progress text-amber-500"></i> Visual Progress Alur Bantuan Listrik
                    </h4>

                    <!-- Progress Stepper Track -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- Step 1 -->
                        <div class="p-4 rounded-2xl border transition-all text-left space-y-2 {{ $currentStep >= 1 ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-400 border-slate-200' }}">
                            <div class="flex items-center justify-between">
                                <span class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center {{ $currentStep >= 1 ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-400' }}">1</span>
                                <i class="fa-solid fa-file-signature text-sm {{ $currentStep >= 1 ? 'text-amber-400' : 'text-slate-300' }}"></i>
                            </div>
                            <h5 class="font-extrabold text-xs">1. Data DTKS</h5>
                            <p class="text-[11px] leading-tight opacity-80 font-medium">Pendaftaran awal berkas</p>
                        </div>

                        <!-- Step 2 -->
                        <div class="p-4 rounded-2xl border transition-all text-left space-y-2 {{ $currentStep >= 2 ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-400 border-slate-200' }}">
                            <div class="flex items-center justify-between">
                                <span class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center {{ $currentStep >= 2 ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-400' }}">2</span>
                                <i class="fa-solid fa-building-user text-sm {{ $currentStep >= 2 ? 'text-amber-400' : 'text-slate-300' }}"></i>
                            </div>
                            <h5 class="font-extrabold text-xs">2. Survei Kades</h5>
                            <p class="text-[11px] leading-tight opacity-80 font-medium">Validasi lokasi Desa</p>
                        </div>

                        <!-- Step 3 -->
                        <div class="p-4 rounded-2xl border transition-all text-left space-y-2 {{ $currentStep >= 3 ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-400 border-slate-200' }}">
                            <div class="flex items-center justify-between">
                                <span class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center {{ $currentStep >= 3 ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-400' }}">3</span>
                                <i class="fa-solid fa-certificate text-sm {{ $currentStep >= 3 ? 'text-amber-400' : 'text-slate-300' }}"></i>
                            </div>
                            <h5 class="font-extrabold text-xs">3. Penetapan SLO</h5>
                            <p class="text-[11px] leading-tight opacity-80 font-medium">Verifikasi Dinas ESDM</p>
                        </div>

                        <!-- Step 4 -->
                        <div class="p-4 rounded-2xl border transition-all text-left space-y-2 {{ $currentStep >= 4 ? 'bg-emerald-700 text-white border-emerald-700' : 'bg-white text-slate-400 border-slate-200' }}">
                            <div class="flex items-center justify-between">
                                <span class="w-7 h-7 rounded-lg text-xs font-black flex items-center justify-center {{ $currentStep >= 4 ? 'bg-white text-emerald-800' : 'bg-slate-100 text-slate-400' }}">4</span>
                                <i class="fa-solid fa-bolt text-sm {{ $currentStep >= 4 ? 'text-amber-300' : 'text-slate-300' }}"></i>
                            </div>
                            <h5 class="font-extrabold text-xs">4. Penyalaan Listrik</h5>
                            <p class="text-[11px] leading-tight opacity-80 font-medium">Instalasi PLN selesai</p>
                        </div>
                    </div>
                </div>

                {{-- Card Penjelasan Detail Status --}}
                <div class="space-y-3">
                    <p class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Detail Informasi Berkas Pendaftaran:</p>

                    @switch($warga->status_verifikasi)
                        @case('terkirim')
                        @case('pending')
                            <div class="p-5 bg-amber-50 text-amber-800 border border-amber-200 rounded-2xl space-y-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-extrabold text-amber-900 bg-amber-100 rounded-full border border-amber-300">
                                    <i class="fa-solid fa-clock text-amber-600"></i> Menunggu Survei & Verifikasi Kepala Desa
                                </span>
                                <p class="text-xs text-amber-900 leading-relaxed font-medium">
                                    Berkas pendaftaran NIK Anda telah diterima sistem dan sedang dijadwalkan untuk peninjauan lapangan serta pencocokan data fisik oleh Kepala Desa setempat.
                                </p>
                            </div>
                            @break

                        @case('diverifikasi_kades')
                            <div class="p-5 bg-amber-50 text-amber-800 border border-amber-200 rounded-2xl space-y-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-extrabold text-amber-900 bg-amber-100 rounded-full border border-amber-300">
                                    <i class="fa-solid fa-check-double text-amber-700"></i> Lolos Verifikasi Kepala Desa
                                </span>
                                <p class="text-xs text-amber-900 leading-relaxed font-medium">
                                    Data Anda telah divalidasi secara resmi oleh Kepala Desa dan sedang diteruskan ke Dinas ESDM Provinsi Jambi untuk penetapan alokasi kuota daya 450 / 900 VA.
                                </p>
                            </div>
                            @break

                        @case('menunggu_verifikasi_pusat')
                            <div class="p-5 bg-amber-50 text-amber-800 border border-amber-200 rounded-2xl space-y-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-extrabold text-amber-900 bg-amber-100 rounded-full border border-amber-300">
                                    <i class="fa-solid fa-building-columns text-amber-700"></i> Penilaian Final & Terbit SLO Dinas ESDM
                                </span>
                                <p class="text-xs text-amber-900 leading-relaxed font-medium">
                                    Berkas dalam tahap evaluasi final oleh Tim Verifikator Dinas ESDM untuk penerbitan Work Order (WO) dan Sertifikat Laik Operasi (SLO) ke PLN Rayon.
                                </p>
                            </div>
                            @break

                        @case('lolos_verifikasi_pusat')
                            <div class="p-5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl space-y-3">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 text-xs font-extrabold text-emerald-900 bg-emerald-100 rounded-full border border-emerald-300">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> DISETUJUI & LISTRIK TERPASANG (DISALURKAN)
                                </span>
                                <p class="text-xs text-emerald-950 leading-relaxed font-semibold">
                                    Selamat! Data permohonan Anda telah disetujui secara resmi oleh Dinas ESDM. Bantuan instalasi 3 titik lampu, kWH meter gratis, dan token listrik perdana telah diterbitkan.
                                </p>
                            </div>
                            @break

                        @case('ditolak/perlu_perbaikan')
                            <div class="p-5 bg-rose-50 text-rose-800 border border-rose-200 rounded-2xl space-y-3">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 text-xs font-extrabold text-rose-900 bg-rose-100 rounded-full border border-rose-300">
                                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Ditolak / Tidak Memenuhi Syarat / Perlu Perbaikan
                                </span>
                                <div class="text-xs text-rose-900 leading-relaxed font-medium">
                                    <p>Permohonan Anda membutuhkan perbaikan dokumen foto KTP atau foto kondisi rumah.</p>
                                    @if($warga->catatan)
                                        <div class="mt-2 p-3 bg-white/80 rounded-xl border border-rose-200 text-xs font-bold text-rose-950">
                                            Catatan Verifikator: "{{ $warga->catatan }}"
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('warga.pengajuan', ['nik' => $warga->nik]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl transition shadow-md">
                                    <i class="fa-solid fa-wrench"></i> Perbaiki Berkas Pendaftaran
                                </a>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>

        {{-- Kondisi 2: NIK Tidak Ditemukan --}}
        @elseif(isset($notFound) && $notFound)
            <div class="p-8 bg-amber-50 border border-amber-200 rounded-3xl text-center space-y-4 shadow-sm">
                <div class="w-14 h-14 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-2xl font-bold mx-auto shadow-md">
                    <i class="fa-solid fa-magnifying-glass-location"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="font-extrabold text-amber-950 text-lg">NIK Tidak Ditemukan</h3>
                    <p class="text-xs text-amber-900 font-medium">
                        Nomor Induk Kependudukan <strong>{{ $nik }}</strong> belum terdaftar dalam sistem bantuan pasang listrik baru.
                    </p>
                </div>

                <div class="pt-2">
                    <a href="{{ route('warga.pengajuan', ['nik' => $nik]) }}"
                       class="inline-flex items-center gap-2 px-7 py-3.5 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs rounded-2xl shadow-lg transition border border-slate-800">
                        <i class="fa-solid fa-paper-plane text-amber-400"></i>
                        Daftar Bantuan Listrik Sekarang (Gratis)
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

