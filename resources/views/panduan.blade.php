@extends('layouts.app')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-8">
        
        <!-- Header Title Banner -->
        <div class="bg-slate-900 text-white p-8 sm:p-10 rounded-3xl shadow-sm border border-slate-800 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 space-y-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 bg-amber-500 text-slate-950 text-[11px] font-black uppercase rounded-full tracking-wider">
                    <i class="fa-solid fa-graduation-cap"></i> Modul Pelatihan SPBE e-Government {{ date('Y') }}
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                    Panduan & Syarat Ketentuan Bantuan Listrik
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm max-w-3xl leading-relaxed font-normal">
                    Petunjuk teknis verifikasi awal permohonan BPBL, kriteria kelayakan DTKS/P3KE, pengusulan Lisdes dusun, serta pengelolaan verifikasi berjenjang Dinas ESDM.
                </p>
            </div>
        </div>

        <!-- Grid 4 Modul Pelatihan -->
        <div class="grid md:grid-cols-2 gap-6">
            
            <!-- Modul 1 -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-amber-400 font-extrabold flex items-center justify-center text-xl shadow-xs">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">1. Alur Verifikasi Lapangan Kades</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Kepala Desa dan Kasi Kesra bertugas memvalidasi kebenaran identitas NIK warga, mengecek status penerima manfaat pada DTKS Kemensos, dan memastikan calon penerima belum terdaftar dalam jaringan PLN.
                </p>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs space-y-2 text-slate-700 font-medium">
                    <div class="flex items-center gap-2 font-bold text-slate-900"><i class="fa-solid fa-circle-play text-amber-600"></i> Langkah Operasional:</div>
                    <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-600">
                        <li>Login ke dasbor Kepala Desa menggunakan kredensial resmi.</li>
                        <li>Pilih permohonan warga berstatus <strong>"Menunggu Verifikasi Kades"</strong>.</li>
                        <li>Periksa foto KTP, SKTM, dan kondisi fisik rumah.</li>
                        <li>Klik <strong>"Setujui & Teruskan ESDM"</strong> atau <strong>"Tolak / Perlu Perbaikan"</strong>.</li>
                    </ol>
                </div>
            </div>

            <!-- Modul 2 -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 font-extrabold flex items-center justify-center text-xl shadow-xs">
                    <i class="fa-solid fa-wifi"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">2. Penggunaan PWA Mode Offline</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Di daerah pelosok/terpencil tanpa sinyal internet, petugas desa tetap bisa melakukan survei dan penginputan data. Sistem PWA akan mengompres foto secara otomatis dan menyimpannya di HP.
                </p>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs space-y-2 text-slate-700 font-medium">
                    <div class="flex items-center gap-2 font-bold text-slate-900"><i class="fa-solid fa-mobile-screen-button text-amber-600"></i> Keunggulan PWA:</div>
                    <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600">
                        <li>Aplikasi bisa diinstal ke Layar Utama (*Add to Home Screen*).</li>
                        <li>Foto KTP & Rumah otomatis dikompres menjadi ~200-300 KB.</li>
                        <li>Data offline akan **otomatis tersinkron** saat HP mendapat sinyal.</li>
                    </ul>
                </div>
            </div>

            <!-- Modul 3 -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-2xl bg-amber-500 text-slate-950 font-extrabold flex items-center justify-center text-xl shadow-xs">
                    <i class="fa-solid fa-tower-cell"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">3. Usulan Listrik Desa (Lisdes)</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Untuk wilayah dusun/RT yang belum memiliki jaringan distribusi listrik PLN, Kepala Desa dapat mengusulkan pembangunan tiang & trafo secara komunal ke Dinas ESDM.
                </p>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs space-y-2 text-slate-700 font-medium">
                    <div class="flex items-center gap-2 font-bold text-slate-900"><i class="fa-solid fa-file-pdf text-amber-600"></i> Dokumen Lampiran:</div>
                    <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600">
                        <li>Surat Pengantar Usulan Lisdes dari Kades.</li>
                        <li>Data Jumlah KK & Titik Koordinat GPS Dusun.</li>
                        <li>Surat Pernyataan Hibah Lahan Tapak Tiang.</li>
                    </ul>
                </div>
            </div>

            <!-- Modul 4 -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-amber-400 font-extrabold flex items-center justify-center text-xl shadow-xs">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-900">4. Keamanan & Backup Data</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Seluruh data identitas warga dilindungi undang-undang privasi. Dinas ESDM melakukan pencadangan data (*Database Backup*) berkala secara otomatis untuk keamanan sistem.
                </p>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs space-y-2 text-slate-700 font-medium">
                    <div class="flex items-center gap-2 font-bold text-slate-900"><i class="fa-solid fa-database text-amber-600"></i> Prosedur Backup:</div>
                    <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-600">
                        <li>Cadangan data berbentuk format JSON terenkripsi.</li>
                        <li>Dapat diunduh kapan saja oleh Admin Dinas ESDM.</li>
                        <li>Integrasi otomatis dengan DTKS Kemensos RI.</li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- Download Handbook Action -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-amber-400 flex items-center justify-center text-xl font-bold shadow-md">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 text-base">Buku Saku Operator Desa {{ date('Y') }}</h4>
                    <p class="text-xs text-slate-500 font-medium">Panduan lengkap format digital PDF untuk pelatihan mandiri perangkat desa.</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('panduan.pdf') }}?print=true" target="_blank" class="px-5 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-file-pdf"></i> Cetak / Unduh Buku Saku PDF
                </a>
                <a href="{{ route('warga.index') }}" class="px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-arrow-left"></i> Beranda Utama
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
