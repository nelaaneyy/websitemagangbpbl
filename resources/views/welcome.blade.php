@extends('layouts.app')

@section('content')

<div class="overflow-x-hidden">

    <!-- HERO SECTION -->
    <section
        class="relative overflow-hidden pt-8 pb-14 sm:pt-12 sm:pb-16 lg:pt-16 lg:pb-20 bg-linear-to-br from-slate-50 via-slate-50/80 to-blue-50/50 text-slate-900 border-b border-slate-200/80">
        <div
            class="absolute -bottom-24 -right-24 w-72 h-72 sm:w-120 sm:h-120 bg-amber-500/15 rounded-full blur-3xl pointer-events-none">
        </div>
        <div
            class="absolute top-1/2 left-1/3 -translate-y-1/2 w-80 h-80 sm:w-140 sm:h-140 bg-blue-100/30 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-center">

                <!-- Left Column (Hero Content & Value Proposition) -->
                <div class="lg:col-span-7 space-y-5 sm:space-y-6 text-center lg:text-left">
                    <h1
                        class="text-2xl sm:text-4xl lg:text-5xl xl:text-6xl font-black tracking-tight leading-tight text-slate-900">
                        Elektronik Layanan Informasi
                        <span class="text-amber-600 block sm:inline">Bantuan Listrik</span>
                        (E-LISTRIK)
                    </h1>

                    <p
                        class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed max-w-2xl mx-auto lg:mx-0 font-normal">
                        Portal resmi Dinas Energi dan Sumber Daya Mineral (ESDM) Provinsi Jambi untuk penyaluran program
                        Bantuan Pasang Listrik Baru dan Bantuan Listrik Desa (Lisdes) bagi masyarakat kurang mampu di
                        wilayah Provinsi Jambi.
                    </p>

                    <div
                        class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 sm:gap-4 w-full sm:w-auto">
                        @auth
                        <a href="{{ route('staffdesa.pengajuan') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                            <i class="fa-solid fa-house-user text-white"></i>
                            Input Pendaftaran BPBL Warga
                        </a>
                        @else
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold text-xs rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all border border-slate-800">
                            <i class="fa-solid fa-right-to-bracket text-amber-400"></i>
                            Portal Login Perangkat Desa & ESDM
                        </a>
                        @endauth

                        <a href="#peta-elektrifikasi"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-6 py-3.5 bg-white/80 backdrop-blur-md hover:bg-white text-slate-800 font-bold text-xs rounded-xl border border-slate-300 hover:-translate-y-0.5 transition-all shadow-xs">
                            <i class="fa-solid fa-map-location-dot text-amber-500"></i>
                            Peta Sebaran Listrik Desa
                        </a>
                    </div>
                </div>

                <!-- Right Column: Hero Image Placeholder Card -->
                <div class="lg:col-span-5 w-full mt-4 lg:mt-0">
                    <div
                        class="bg-white/80 backdrop-blur-md border-2 border-slate-300 shadow-lg rounded-3xl text-slate-700 space-y-4 flex flex-col items-center justify-center text-center hover:border-amber-400/80 transition-all duration-300 relative group overflow-hidden">
                        <img src="{{ asset('images/Listrik Hadir, Jambi Terang (2).png') }}" alt="poster program elektrikfikasi DESDM Provinsi Jambi"
                                class="w-full h-auto rounded-xl shadow-sm">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION B: OPSI LAYANAN PROGRAM -->
    <section id="program-bantuan" class="py-12 sm:py-16 bg-white border-b border-slate-200/80"
        x-data="{ openLisdesModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-10">
            <div class="text-center max-w-3xl mx-auto space-y-2">
                <span
                    class="px-3.5 py-1 bg-amber-100 text-amber-900 font-extrabold text-xs rounded-full uppercase tracking-wider border border-amber-300">
                    Opsi Layanan Program
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Kategori Program Bantuan
                    Listrik Dinas Energi dan Sumber Daya Mineral (ESDM) Provinsi Jambi</h2>
                <p class="text-slate-600 text-xs sm:text-sm">Pilih jenis permohonan yang sesuai dengan status kebutuhan
                    di wilayah desa Anda.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 sm:gap-8 max-w-5xl mx-auto">
                <!-- Card 1: BPBL Mandiri Warga -->
                <div
                    class="p-6 sm:p-8 rounded-3xl bg-[#F8FAFC] border border-slate-200 hover:border-amber-500/50 hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div
                            class="w-12 h-12 sm:w-14 sm:h-14 bg-slate-900 text-amber-400 rounded-2xl flex items-center justify-center text-lg sm:text-xl shadow-xs">
                            <i class="fa-solid fa-house-chimney-user"></i>
                        </div>
                        <span
                            class="inline-block px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] sm:text-xs font-extrabold rounded-full">
                            Pendataan Dilakukan Oleh Perangkat Desa
                        </span>
                        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                            Bantuan Pasang Baru Listrik (BPBL)
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Program bantuan bebas biaya berupa pemasangan kWH meter baru prabayar beserta instalasi rumah untuk
                            masyarakat perorangan yang memenuhi syarat. Pendataan diinput langsung oleh Staff Kantor Desa & Kepala Desa ke
                            rumah-rumah warga.
                        </p>

                        <ul class="space-y-2 text-xs text-slate-700 font-medium pt-2">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                                <span>Diisi Langsung Oleh Staff / Kepala Desa saat Kunjungan Rumah</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                                <span>Gratis Instalasi 3 Titik Lampu, 1 Stop Kontak & Token Perdana</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-200">
                        @auth
                        @if(in_array(auth()->user()->role, ['staff_desa', 'kepala_desa', 'verifikator_esdm',
                        'super_admin', 'instansi']))
                        <a href="{{ route('staffdesa.pengajuan') }}"
                            class="w-full py-3.5 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 text-xs">
                            <i class="fa-solid fa-house-chimney-user text-xs"></i>
                            <span>Input Pendaftaran BPBL Warga (Staff / Kades)</span>
                        </a>
                        @endif
                        @else
                        <a href="{{ route('login') }}"
                            class="w-full py-3.5 px-6 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 text-xs border border-slate-800">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Login Sebaagai Perangkat Desa Untuk Input Data Warga</span>
                        </a>
                        @endauth
                    </div>
                </div>

                <!-- Card 2: Listrik Desa (Lisdes) -->
                <div
                    class="p-6 sm:p-8 rounded-3xl bg-[#F8FAFC] border border-slate-200 hover:border-amber-500/50 hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div
                            class="w-12 h-12 sm:w-14 sm:h-14 bg-amber-500 text-slate-950 rounded-2xl flex items-center justify-center text-lg sm:text-xl shadow-xs">
                            <i class="fa-solid fa-tower-cell"></i>
                        </div>
                        <span
                            class="inline-block px-3 py-1 bg-white text-slate-800 border border-slate-300 text-[11px] sm:text-xs font-extrabold rounded-full">
                            Usulan Oleh Pemerintah Desa
                        </span>
                        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-900">
                            Bantuan Listrik Desa (Lisdes)
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Program usulan jaringan infrastruktur listrik komunal untuk dusun/wilayah desa yang belum
                            terjangkau jaringan listrik utama PLN.
                        </p>

                        <ul class="space-y-2 text-xs text-slate-700 font-medium pt-2">
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-amber-600 shrink-0"></i>
                                <span>Pembangunan Tiang & Trafo Distribusi</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-amber-600 shrink-0"></i>
                                <span>Diajukan Secara Resmi Oleh Kepala Desa</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-amber-600 shrink-0"></i>
                                <span>Penilaian Kelayakan Oleh Tim Teknis Dinas Energi dan Sumber Daya Mineral (ESDM) Provinsi Jambi</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-200">
                        <button @click.stop="openLisdesModal = true" type="button"
                            class="w-full py-3.5 px-6 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 text-xs cursor-pointer">
                            <i class="fa-solid fa-file-signature"></i>
                            <span>Lihat Syarat Usulan Lisdes</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Popup Syarat Lisdes -->
            <div x-cloak x-show="openLisdesModal" x-transition.opacity
                class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4"
                @click.stop="openLisdesModal = false">
                <div @click.stop
                    class="bg-white max-w-lg w-full rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 relative max-h-[90vh] overflow-y-auto">
                    <button @click="openLisdesModal = false"
                        class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>

                    <div class="flex items-center gap-3">
                        <div
                            class="w-11 h-11 sm:w-12 sm:h-12 bg-amber-500 text-slate-950 rounded-2xl flex items-center justify-center text-lg sm:text-xl font-bold shrink-0">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <div>
                            <h3 class="text-lg sm:text-xl font-extrabold text-slate-900 leading-tight">Syarat Usulan
                                Lisdes</h3>
                            <p class="text-[11px] sm:text-xs text-slate-500 font-medium">Persyaratan Pengajuan
                                Pemerintah Desa</p>
                        </div>
                    </div>

                    <div
                        class="space-y-3 text-xs text-slate-700 bg-slate-50 p-4 rounded-2xl border border-slate-200 leading-relaxed font-medium">
                        <div class="flex gap-2.5">
                            <span class="font-extrabold text-amber-600 shrink-0">1.</span>
                            <p>Surat Permohonan Resmi dari Kepala Desa ditujukan kepada Kepala Dinas ESDM Provinsi
                                Jambi.</p>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="font-extrabold text-amber-600 shrink-0">2.</span>
                            <p>Daftar Nama Calon Penerima Manfaat (Jumlah Rumah yang belum berlistrik) divalidasi Kades.
                            </p>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="font-extrabold text-amber-600 shrink-0">3.</span>
                            <p>Berita Acara Musrenbangdes atau Surat Pernyataan Kesediaan Lahan Hibah untuk tiang/trafo
                                PLN.</p>
                        </div>
                        <div class="flex gap-2.5">
                            <span class="font-extrabold text-amber-600 shrink-0">4.</span>
                            <p>Peta lokasi atau titik koordinat GPS dusun/wilayah sasaran pembangunan jaringan listrik.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2.5 pt-1">
                        <button @click="openLisdesModal = false"
                            class="w-full py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                            Tutup
                        </button>
                        <a href="{{ route('login') }}"
                            class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-xs text-center transition-colors">
                            Login Sebagai Kades &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION C: 4 TAHAPAN PROSES BANTUAN -->
    <section id="tahapan-proses" class="py-12 sm:py-16 bg-[#F8FAFC] border-b border-slate-200/80"
        x-data="{ activeStep: 1 }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-10">

            <div class="text-center max-w-3xl mx-auto space-y-2">
                <span
                    class="px-3.5 py-1 bg-amber-100 text-amber-900 font-extrabold text-xs rounded-full uppercase tracking-wider border border-amber-300">
                    Alur Layanan Transparan
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Tahapan Proses Bantuan
                    Listrik (BPBL)</h2>
                <p class="text-slate-600 text-xs sm:text-sm">Klik setiap tahapan di bawah ini untuk melihat persyaratan
                    dan dokumen yang dibutuhkan.</p>
            </div>

            <!-- Timeline Steps Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Step 1 -->
                <button type="button" @click="activeStep = 1"
                    :class="activeStep === 1 ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-amber-400'"
                    class="p-4 sm:p-5 rounded-2xl border text-left transition-all duration-200 space-y-2 sm:space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span :class="activeStep === 1 ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-700'"
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl font-black text-xs flex items-center justify-center">1</span>
                        <span class="text-[10px] sm:text-[11px] font-bold opacity-75">Tahap 1</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs sm:text-sm">Pendaftaran Data DTKS</h4>
                        <p class="text-[11px] opacity-80 mt-0.5 font-medium">Usulan warga & validasi awal</p>
                    </div>
                </button>

                <!-- Step 2 -->
                <button type="button" @click="activeStep = 2"
                    :class="activeStep === 2 ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-amber-400'"
                    class="p-4 sm:p-5 rounded-2xl border text-left transition-all duration-200 space-y-2 sm:space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span :class="activeStep === 2 ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-700'"
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl font-black text-xs flex items-center justify-center">2</span>
                        <span class="text-[10px] sm:text-[11px] font-bold opacity-75">Tahap 2</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs sm:text-sm">Verifikasi Lapangan</h4>
                        <p class="text-[11px] opacity-80 mt-0.5 font-medium">Survei fisik oleh Kepala Desa</p>
                    </div>
                </button>

                <!-- Step 3 -->
                <button type="button" @click="activeStep = 3"
                    :class="activeStep === 3 ? 'bg-emerald-700 text-white border-emerald-700 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-emerald-400'"
                    class="p-4 sm:p-5 rounded-2xl border text-left transition-all duration-200 space-y-2 sm:space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span :class="activeStep === 3 ? 'bg-white text-emerald-800' : 'bg-slate-100 text-slate-700'"
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl font-black text-xs flex items-center justify-center">3</span>
                        <span class="text-[10px] sm:text-[11px] font-bold opacity-75">Tahap Final</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs sm:text-sm">Penyalaan Meteran PLN</h4>
                        <p class="text-[11px] opacity-80 mt-0.5 font-medium">Instalasi & kWH menyala</p>
                    </div>
                </button>
            </div>

            <!-- Active Step Detail Panel -->
            <div class="bg-white p-5 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <!-- Step 1 Details -->
                <div x-show="activeStep === 1" class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-900 text-amber-400 font-black text-sm sm:text-base flex items-center justify-center shrink-0">
                            1</div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Tahap 1: Pendaftaran Bantuan
                                Pasang Baru Listrik (BPBL)</h3>
                            <p class="text-[11px] sm:text-xs text-slate-500 font-medium">Warga mendaftar mandiri atau
                                diusulkan oleh Pemerintah Desa setempat</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-3 sm:gap-4 pt-1">
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-id-card text-amber-500 mr-1.5"></i> 1. Identitas KTP</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">NIK terdaftar
                                resmi di Dukcapil & DTKS/P3KE Kemensos.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-file-invoice text-amber-500 mr-1.5"></i> 2. SKTM / Kartu
                                Bansos</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">Surat
                                Keterangan Tidak Mampu dari Kelurahan / KIS / KKS.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-camera text-amber-500 mr-1.5"></i> 3. Foto Rumah Depan</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">Dokumentasi
                                fisik kondisi bangunan rumah calon pemohon.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 Details -->
                <div x-show="activeStep === 2" class="space-y-4" style="display: none;">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-900 text-amber-400 font-black text-sm sm:text-base flex items-center justify-center shrink-0">
                            2</div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Tahap 2: Verifikasi Lapangan
                                oleh Kepala Desa</h3>
                            <p class="text-[11px] sm:text-xs text-slate-500 font-medium">Pemeriksaan fisik ke lokasi
                                rumah calon penerima manfaat</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-3 sm:gap-4 pt-1">
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-street-view text-blue-600 mr-1.5"></i> Validasi Bangunan</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">Memastikan
                                rumah belum tersambung ke jaringan listrik lain.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-tower-cell text-blue-600 mr-1.5"></i> Jarak Tiang Listrik</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">Mencatat tiang
                                distribusi PLN terdekat dari lokasi rumah.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-signature text-blue-600 mr-1.5"></i> Persetujuan Kades</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">Pengesahan
                                resmi dari Kepala Desa secara digital di sistem.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 Details -->
                <div x-show="activeStep === 3" class="space-y-4" style="display: none;">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-700 text-white font-black text-sm sm:text-base flex items-center justify-center shrink-0">
                            3</div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Tahap 3: Penyalaan kWH Meter
                                & Instalasi PLN</h3>
                            <p class="text-[11px] sm:text-xs text-slate-500 font-medium">Eksekusi pemasangan fisik di
                                lokasi rumah penerima manfaat</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-3 sm:gap-4 pt-1">
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-gauge-simple-high text-emerald-600 mr-1.5"></i> Meteran Listrik
                                Gratis</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">Pemasangan kWH
                                meter baru prabayar resmi dari PLN.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-lightbulb text-emerald-600 mr-1.5"></i> 3 Titik Lampu
                                Gratis</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">Pemasangan
                                instalasi kabel, 3 bohlam lampu, & stop kontak.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i
                                    class="fa-solid fa-ticket text-emerald-600 mr-1.5"></i> Voucher Token Perdana</span>
                            <p class="text-slate-600 font-medium text-[11px] sm:text-xs leading-relaxed">Pemberian token
                                listrik perdana gratis yang langsung aktif.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION D: CAPAIAN REALISASI / KPI METRICS -->
    <section id="dashboard-elektrifikasi" class="py-12 sm:py-16 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-10">

            <!-- Header Dashboard Transparansi (Ukuran Disamakan & Rata Tengah) -->
            <div class="text-center max-w-3xl mx-auto space-y-2 border-b border-slate-100 pb-5">
                <span
                    class="inline-block px-3.5 py-1 bg-blue-100 text-blue-900 font-extrabold text-xs rounded-full uppercase tracking-wider border border-blue-200">
                    Dashboard Transparansi Publik
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Capaian Realisasi BPBL {{ date('Y') }}
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm flex items-center justify-center gap-1.5 pt-1">
                    <i class="fa-solid fa-rotate text-slate-400"></i> Update Terakhir: {{ date('d M Y') }}
                </p>
            </div>

            <!-- Grid 4 Kolom Stat Card -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <!-- Stat 1: Total Rumah Tangga Teraliri -->
                <div
                    class="bg-[#F8FAFC] p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:-translate-y-0.5 transition-all duration-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Rumah
                            Tangga Teraliri</span>
                        <div
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs sm:text-sm">
                            <i class="fa-solid fa-house-bolt"></i>
                        </div>
                        <h3 id="kpi-teraliri-main" class="text-3xl font-black text-slate-900">{{ number_format($totalTerpasang ?? 0, 0, ',', '.') }}</h3>

                    </div>
                    <div id="kpi-total-approved" class="flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        +{{ number_format($totalApprovedGlobal, 0, ',', '.') }} Terpasang Baru
                    </div>
                </div>

                <!-- Stat 2: Target Realisasi Tahun Berjalan -->
                <div
                    class="bg-[#F8FAFC] p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:-translate-y-0.5 transition-all duration-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Target
                            Realisasi {{ date('Y') }}</span>
                        <div
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs sm:text-sm">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                    </div>
                    <div id="kpi-overall-rasio" class="text-2xl sm:text-3xl font-black text-slate-900">
                        {{ $overallRasio }}%</div>
                    <div class="space-y-1">
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                            <div id="kpi-overall-rasio-bar"
                                class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                                style="width: {{ min(100, $overallRasio) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Stat 3: Daya Tersalurkan -->
                <div
                    class="bg-[#F8FAFC] p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:-translate-y-0.5 transition-all duration-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori
                            Daya Tersalur</span>
                        <div
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xs sm:text-sm">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900">450 & 900 <span
                            class="text-xs font-bold text-slate-500">VA</span></div>
                    <div class="text-[11px] font-bold text-amber-700 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> 100% Bebas Biaya Pasang
                    </div>
                </div>

                <!-- Stat 4: Rasio Elektrifikasi -->
                <div
                    class="bg-[#F8FAFC] p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:-translate-y-0.5 transition-all duration-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider">Rasio
                            Elektrifikasi Jambi</span>
                        <div
                            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-200 text-slate-800 flex items-center justify-center font-bold text-xs sm:text-sm">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                    </div>
                    <div id="kpi-overall-rasio-2" class="text-2xl sm:text-3xl font-black text-slate-900">
                        {{ $overallRasio }}%</div>
                    <div class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                        <i class="fa-solid fa-flag text-amber-500"></i> Target 100% Terang
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION E: PETA SEBARAN -->
    <section id="peta-elektrifikasi" class="py-12 sm:py-16 bg-[#F8FAFC] border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

            <div class="text-center max-w-3xl mx-auto space-y-2">
                <span
                    class="px-3.5 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-xs rounded-full uppercase tracking-wider border border-emerald-200">
                    Geospatial Monitoring System
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Peta Sebaran BPBL Per Desa
                    di Jambi</h2>
                <p class="text-slate-600 text-xs sm:text-sm">Arahkan kursor atau klik marker desa/kabupaten untuk
                    melihat rincian realisasi penerima bantuan.</p>
            </div>

            <!-- Control Bar Filter Responsif -->
            <div
                class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 sm:gap-4">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full md:w-auto">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5 shrink-0">
                        <i class="fa-solid fa-filter text-amber-500"></i> Filter:
                    </span>
                    <select id="status-filter" onchange="filterDesaInteractive()"
                        class="w-full sm:w-auto px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all cursor-pointer">
                        <option value="all">Semua Status Wilayah</option>
                        <option value="full">Hijau: 100% Full Teraliri</option>
                        <option value="sebagian">Kuning: Sebagian Teraliri</option>
                        <option value="belum">Merah: Belum Teraliri (0%)</option>
                    </select>
                    <button type="button" onclick="resetMapFilter()"
                        class="w-full sm:w-auto px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center justify-center">
                        <i class="fa-solid fa-rotate-left mr-1.5"></i> Reset Peta
                    </button>
                </div>

                <div class="w-full md:w-80 lg:w-96">
                    <div class="relative">
                        <input id="search-desa" type="text" oninput="filterDesaInteractive()"
                            placeholder="Cari desa atau kabupaten..."
                            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-xs">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Map Container -->
            <div class="bg-white rounded-3xl p-2.5 sm:p-3 shadow-xl overflow-hidden relative border border-slate-200">
                <div class="flex items-center justify-between px-2 sm:px-4 py-2 text-slate-900 text-xs font-bold">
                    <span class="flex items-center gap-1.5 sm:gap-2 text-[12px] sm:text-sm">
                        <i class="fa-solid fa-map-pin text-amber-500"></i> Peta Sebaran Listrik Wilayah Provinsi Jambi
                    </span>
                    <div class="flex items-center gap-2">
                        <span id="marker-count-badge"
                            class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-800 border border-slate-200 text-[11px] font-bold">
                            Menampilkan 0 Desa
                        </span>
                    </div>
                </div>
                <div id="map" class="w-full h-72 sm:h-96 md:h-120 rounded-2xl z-10"></div>
            </div>

            <!-- Visual Dashboard Charts & Cards -->
            <div class="w-full max-w-7xl mx-auto bg-white p-4 sm:p-6 lg:p-8 rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xs space-y-6"
                x-data="{ viewTab: 'chart' }">

                <!-- Header Ringkasan & Switcher -->
                <div class="text-center max-w-3xl mx-auto space-y-3 border-b border-slate-100 pb-5 sm:pb-6">
                    <span
                        class="inline-block px-3 py-1 bg-blue-100 text-blue-900 font-extrabold text-[11px] sm:text-xs rounded-full uppercase tracking-wider border border-blue-200">
                        Ringkasan Data Publik
                    </span>

                    <h2
                        class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-snug">
                        Informasi Aliran Listrik Desa di Jambi
                    </h2>

                    <p class="text-slate-600 text-xs sm:text-sm max-w-xl mx-auto">
                        Grafik persentase wilayah dan status desa yang sudah teraliri listrik PLN.
                    </p>

                    <!-- View Switcher (Full-width di mobile, inline di screen besar) -->
                    <div class="pt-2">
                        <div
                            class="inline-flex w-full sm:w-auto p-1 bg-slate-100 rounded-xl sm:rounded-2xl border border-slate-200 text-xs font-bold">
                            <button type="button"
                                @click="viewTab = 'chart'; $nextTick(() => { if (typeof renderCharts === 'function') renderCharts(currentFilteredData); window.dispatchEvent(new Event('resize')); })"
                                :class="viewTab === 'chart' ? 'bg-white text-slate-900 shadow-xs font-extrabold' : 'text-slate-500 hover:text-slate-900'"
                                class="flex-1 sm:flex-none justify-center px-4 py-2 rounded-lg sm:rounded-xl transition flex items-center gap-1.5 cursor-pointer text-xs">
                                <i class="fa-solid fa-chart-bar text-blue-600"></i>
                                <span>Grafik</span>
                            </button>
                            <button type="button" @click="viewTab = 'cards'; if (typeof onSwitchToCards === 'function') onSwitchToCards();"
                                :class="viewTab === 'cards' ? 'bg-white text-slate-900 shadow-xs font-extrabold' : 'text-slate-500 hover:text-slate-900'"
                                class="flex-1 sm:flex-none justify-center px-4 py-2 rounded-lg sm:rounded-xl transition flex items-center gap-1.5 cursor-pointer text-xs">
                                <i class="fa-solid fa-list-ul text-amber-500"></i>
                                <span>Kartu Desa</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- View 1: ApexCharts -->
                <div x-show="viewTab === 'chart'" x-cloak class="space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Chart 1: Bar per Kabupaten -->
                        <div
                            class="bg-slate-50 p-4 sm:p-5 lg:p-6 rounded-xl sm:rounded-2xl border border-slate-200/80 space-y-3 overflow-hidden">
                            <div class="flex items-center justify-between gap-2">
                                <h4
                                    class="font-extrabold text-[11px] sm:text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5 truncate">
                                    <i class="fa-solid fa-chart-bar text-blue-600 shrink-0"></i>
                                    <span class="truncate">Tingkat Terang per Kab (%)</span>
                                </h4>
                                <span class="text-[10px] font-bold text-slate-400 shrink-0">Jambi</span>
                            </div>
                            <div id="chart-kabupaten-bar" class="w-full min-h-70 sm:min-h-"></div>
                        </div>

                        <!-- Chart 2: Donut Status Desa -->
                        <div
                            class="bg-slate-50 p-4 sm:p-5 lg:p-6 rounded-xl sm:rounded-2xl border border-slate-200/80 space-y-3 overflow-hidden">
                            <div class="flex items-center justify-between gap-2">
                                <h4
                                    class="font-extrabold text-[11px] sm:text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5 truncate">
                                    <i class="fa-solid fa-chart-pie text-emerald-600 shrink-0"></i>
                                    <span class="truncate">Status Kelistrikan Desa</span>
                                </h4>
                                <span class="text-[10px] font-bold text-slate-400 shrink-0">Desa</span>
                            </div>
                            <div id="chart-status-donut"
                                class="w-full min-h-70 sm:min-h- flex items-center justify-center"></div>
                        </div>
                    </div>
                </div>

                <!-- View 2: Cards Grid -->
                <div x-show="viewTab === 'cards'" x-cloak class="space-y-4" style="display: none;">
                    <!-- Card Search & Control Header -->
                    <div class="bg-slate-50/90 p-3.5 sm:p-4 rounded-2xl border border-slate-200/90 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shadow-2xs">
                        <div class="space-y-0.5">
                            <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-amber-500"></i>
                                <span>Daftar Kartu Desa</span>
                            </h3>
                            <p class="text-[11px] sm:text-xs text-slate-500 font-medium">
                                Menampilkan 10 desa per tampilan. Klik kartu desa untuk melihat posisinya di peta.
                            </p>
                        </div>

                        <!-- Fitur Pencarian Nama Desa -->
                        <div class="w-full sm:w-80 md:w-96 relative">
                            <div class="relative flex items-center">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 text-slate-400 text-xs pointer-events-none"></i>
                                <input
                                    id="card-search-desa"
                                    type="text"
                                    oninput="onCardSearchInput(this.value)"
                                    onkeydown="if(event.key === 'Escape') clearCardSearch();"
                                    placeholder="Cari nama desa..."
                                    class="w-full pl-9 pr-8 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-xs"
                                >
                                <button
                                    id="btn-clear-card-search"
                                    type="button"
                                    onclick="clearCardSearch()"
                                    class="hidden absolute right-2.5 w-5 h-5 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-600 items-center justify-center text-[10px] transition cursor-pointer"
                                    title="Hapus pencarian">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Info Bar / Counter -->
                    <div class="flex items-center justify-between text-xs px-1">
                        <div id="desa-cards-count" class="font-bold text-slate-600 flex items-center gap-1.5 text-xs">
                            <!-- Diisi oleh JS -->
                        </div>
                        <div id="card-search-indicator" class="hidden text-[11px] text-amber-600 font-bold items-center gap-1.5 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Pencarian aktif</span>
                        </div>
                    </div>

                    <!-- Cards Container -->
                    <div id="desa-detail-container"
                        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4 min-h-35"></div>

                    <!-- Pagination Container -->
                    <div id="desa-cards-pagination" class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100">
                        <!-- Diisi oleh JS -->
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION F: HELP DESK RESMI -->
    <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-slate-900 text-white rounded-3xl p-6 sm:p-8 lg:p-10 flex flex-col md:flex-row items-center justify-between gap-6 sm:gap-8 border border-slate-800 shadow-md">
                <div class="space-y-2 max-w-2xl text-center md:text-left">
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-400 text-[10px] sm:text-[11px] font-extrabold uppercase border border-amber-500/30">
                        <i class="fa-solid fa-headset"></i> Layanan Pengaduan Resmi
                    </span>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight">Butuh Bantuan
                        Kendala Pasang / Belum Nyala?</h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-normal">
                        Tim Helpdesk Dinas ESDM siap membantu menindaklanjuti kendala pendaftaran, verifikasi lokasi,
                        atau meteran listrik yang belum menyala di daerah Anda.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 shrink-0 w-full md:w-auto">
                    <a href="https://wa.me/6285369911990?text=Halo%20Helpdesk%20ESDM,%20saya%20ingin%20bertanya%20mengenai%20bantuan%20BPBL"
                        target="_blank"
                        class="w-full sm:w-auto px-5 py-3 sm:px-6 sm:py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp CS ESDM
                    </a>
                    <a href="tel:074165004"
                        class="w-full sm:w-auto px-5 py-3 sm:px-6 sm:py-3.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                        <i class="fa-solid fa-phone text-slate-950"></i> Call Center (0741) 65004
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-gesture-handling/dist/leaflet-gesture-handling.min.css"
    type="text/css" />
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 18px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    }

    .leaflet-tooltip {
        border-radius: 12px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        font-size: 11px;
        padding: 5px 10px;
        border: 1px solid #cbd5e1;
    }

    .leaflet-gesture-handling-touch-warning:after,
    .leaflet-gesture-handling-scroll-warning:after {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-weight: 700;
        font-size: 12px;
        border-radius: 12px;
    }

</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="https://unpkg.com/leaflet-gesture-handling/dist/leaflet-gesture-handling.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    const toTitle = s => (s || '').toLowerCase().replace(/(^|\s)\S/g, c => c.toUpperCase());
    const rawPhpDesas = @json($desas ?? []);
    let dataDesa = Array.isArray(rawPhpDesas) ? rawPhpDesas.map(d => {
        let color = 'gold';
        if (d.status === 'full') color = 'green';
        if (d.status === 'belum') color = 'red';

        const parsedLat = parseFloat(d.latitude);
        const parsedLng = parseFloat(d.longitude);
        const validLat = (!isNaN(parsedLat) && parsedLat !== 0) ? parsedLat : -1.6101;
        const validLng = (!isNaN(parsedLng) && parsedLng !== 0) ? parsedLng : 103.6131;

        return {
            id: d.id || Math.random(),
            nama: toTitle(d.nama_desa || d.nama || 'Desa Tanpa Nama'),
            kabupaten: d.kabupaten || 'Jambi',
            lat: validLat,
            lng: validLng,
            totalRt: parseInt(d.total_rt) || 0,
            berlistrik: parseInt(d.berlistrik_rt) || 0,
            belumBerlistrik: parseInt(d.belum_berlistrik_rt) || 0,
            rasio: parseFloat(d.rasio_elektrifikasi) || 0,
            status: d.status || 'sebagian',
            color: color,
            wargaTerverifikasi: parseInt(d.warga_terverifikasi || 0),
            wargaPendingKades: parseInt(d.warga_pending_kades || 0)
        };
    }) : [];
    let currentFilteredData = [...dataDesa];
    let chartKabupatenInstance = null;
    let chartDonutInstance = null;
    let markerMap = {};

    // 1. Inisialisasi Peta & Base Layers
    const map = L.map('map', {
        gestureHandling: true,
        gestureHandlingOptions: {
            text: {
                touch: "Gunakan 2 jari untuk menggeser peta",
                scroll: "Gunakan Ctrl + scroll untuk zoom",
                scrollMac: "Gunakan \u2318 + scroll untuk zoom"
            }
        }
    }).setView([-1.6101229, 103.6131203], 8);

    // 1. Satelit Hybrid Google
    const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
    });

    // 2. Satelit Polos (Tanpa Teks)
    const googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
    });

    // 3. OpenStreetMap (Peta Vektor Jalan & Wilayah)
    const openStreetMap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    });

    // Default Layer
    googleHybrid.addTo(map);

    L.control.layers({
        "Satelit Hybrid": googleHybrid,
        "Satelit Polos (Tanpa Teks)": googleSat,
        "Peta Jalan (OpenStreetMap)": openStreetMap
    }, null, { position: 'topright' }).addTo(map);

    let markersGroup = L.markerClusterGroup().addTo(map);

    // 2. Load Data dari API Laravel
    function loadDesaDataFromApi() {
        const badge = document.getElementById('marker-count-badge');
        if (badge) badge.innerText = "Memuat data desa...";

        fetch('/api/desas-map')
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat API desas-map');
                return response.json();
            })
            .then(res => {
                // Evaluasi fleksibel untuk berbagai format wrapper JSON Laravel
                const rawDesaData = res.data?.data || res.data || res || [];

                dataDesa = Array.isArray(rawDesaData) ? rawDesaData.map(d => {
                    let color = 'gold';
                    if (d.status === 'full') color = 'green';
                    if (d.status === 'belum') color = 'red';

                    // Parsing koordinat dengan validasi angka & fallback pusat Jambi
                    const parsedLat = parseFloat(d.latitude);
                    const parsedLng = parseFloat(d.longitude);

                    const validLat = (!isNaN(parsedLat) && parsedLat !== 0) ? parsedLat : -1.6101;
                    const validLng = (!isNaN(parsedLng) && parsedLng !== 0) ? parsedLng : 103.6131;

                    return {
                        id: d.id || Math.random(),
                        nama: toTitle(d.nama_desa || d.nama || 'Desa Tanpa Nama'),
                        kabupaten: d.kabupaten || 'Jambi',
                        lat: validLat,
                        lng: validLng,
                        totalRt: parseInt(d.total_rt) || 0,
                        berlistrik: parseInt(d.berlistrik_rt) || 0,
                        belumBerlistrik: parseInt(d.belum_berlistrik_rt) || 0,
                        rasio: parseFloat(d.rasio_elektrifikasi) || 0,
                        status: d.status || 'sebagian',
                        color: color,
                        wargaTerverifikasi: parseInt(d.warga_terverifikasi || 0),
                        wargaPendingKades: parseInt(d.warga_pending_kades || 0)
                    };
                }) : [];

                currentFilteredData = [...dataDesa];

                // Render Peta & Statistik & Charts
                renderMarkersOnMap(currentFilteredData);
                renderStatistik(currentFilteredData);
                renderCharts(currentFilteredData);
            })
            .catch(error => {
                console.error("Error Fetch API:", error);
                if (badge) badge.innerText = `Menampilkan ${dataDesa.length} Desa`;
            });
    }

    function createCustomIcon(color) {
        let iconUrl =
            'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png';
        if (color === 'gold') iconUrl =
            'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png';
        if (color === 'red') iconUrl =
            'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png';

        return new L.Icon({
            iconUrl: iconUrl,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
    }

    function renderMarkersOnMap(filteredList) {
        markersGroup.clearLayers();
        markerMap = {};
        const bounds = [];

        filteredList.forEach(d => {
            if (!isNaN(d.lat) && !isNaN(d.lng) && d.lat !== 0 && d.lng !== 0) {
                const marker = L.marker([d.lat, d.lng], {
                    icon: createCustomIcon(d.color)
                });

                marker.bindPopup(`
                    <div style="min-width: 190px; text-align: center; font-family: 'Plus Jakarta Sans', sans-serif;">
                        <span style="font-size: 9px; font-weight: 800; text-transform: uppercase; background-color: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 9999px;">
                            ${d.kabupaten}
                        </span>
                        <h4 style="font-weight: 900; margin: 6px 0 2px 0; font-size: 14px; color: #0f172a;">Desa ${d.nama}</h4>
                        <div style="background-color: #f8fafc; padding: 6px 8px; border-radius: 10px; margin: 6px 0; border: 1px solid #e2e8f0; font-size: 11px; text-align: left;">
                            <div style="display: flex; justify-content: space-between; color: #16a34a; font-weight: bold;">
                                <span>Teraliri:</span> <b>${d.berlistrik} Rumah</b>
                            </div>
                            <div style="display: flex; justify-content: space-between; color: #dc2626; font-weight: bold;">
                                <span>Belum:</span> <b>${d.belumBerlistrik} Rumah</b>
                            </div>
                        </div>
                        <div style="font-weight: 900; font-size: 12px; color: #1d4ed8; background-color: #eff6ff; padding: 4px; border-radius: 8px;">
                            Rasio: ${d.rasio}%
                        </div>
                    </div>
                `);

                marker.bindTooltip(`<b>Desa ${d.nama}</b> (${d.rasio}%)`, {
                    direction: 'top',
                    offset: [0, -36]
                });

                markersGroup.addLayer(marker);
                markerMap[d.id] = marker;
                bounds.push([d.lat, d.lng]);
            }
        });

        const badge = document.getElementById('marker-count-badge');
        if (badge) badge.innerText = `Menampilkan ${filteredList.length} Desa`;

        if (bounds.length > 0 && filteredList.length < dataDesa.length) {
            map.fitBounds(bounds, {
                padding: [30, 30],
                maxZoom: 12
            });
        }
    }

    // State Manajemen Kartu Desa & Pagination
    let baseCardData = [];
    let cardCurrentPage = 1;
    const CARD_PAGE_SIZE = 10;
    let cardSearchQuery = '';

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m];
        });
    }

    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const escapedText = escapeHtml(text);
        const escapedQuery = escapeHtml(query).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp(`(${escapedQuery})`, 'gi');
        return escapedText.replace(regex, '<mark class="bg-amber-200 text-amber-950 rounded-xs px-0.5 font-bold">$1</mark>');
    }

    function onCardSearchInput(value) {
        cardSearchQuery = value || '';
        cardCurrentPage = 1;
        renderCardsFromState();
    }

    function clearCardSearch() {
        const input = document.getElementById('card-search-desa');
        if (input) input.value = '';
        cardSearchQuery = '';
        cardCurrentPage = 1;
        renderCardsFromState();
        if (input) input.focus();
    }

    function onSwitchToCards() {
        cardCurrentPage = 1;
        renderCardsFromState();
    }

    function goToCardPage(page) {
        cardCurrentPage = page;
        renderCardsFromState();
        const cardHeader = document.getElementById('card-search-desa');
        if (cardHeader) {
            cardHeader.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function renderStatistik(filtered) {
        baseCardData = filtered || [];
        cardCurrentPage = 1;
        renderCardsFromState();
    }

    function renderCardsFromState() {
        const container = document.getElementById('desa-detail-container');
        if (!container) return;
        container.innerHTML = '';

        const query = (cardSearchQuery || '').toLowerCase().trim();
        const matchingDesa = query
            ? baseCardData.filter(d => (d.nama || '').toLowerCase().includes(query))
            : baseCardData;

        const countBadge = document.getElementById('desa-cards-count');
        const clearBtn = document.getElementById('btn-clear-card-search');
        const indicator = document.getElementById('card-search-indicator');

        if (clearBtn) {
            if (query.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        if (indicator) {
            if (query.length > 0) {
                indicator.classList.remove('hidden');
            } else {
                indicator.classList.add('hidden');
            }
        }

        if (!matchingDesa || matchingDesa.length === 0) {
            if (countBadge) countBadge.innerHTML = `<span class="text-rose-600 font-extrabold text-xs">0 desa ditemukan</span>`;
            container.innerHTML = `
                <div class="col-span-full text-center py-10 px-4 bg-slate-50/70 rounded-2xl border border-dashed border-slate-300 space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-lg border border-amber-200">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <div>
                        <h4 class="text-xs sm:text-sm font-extrabold text-slate-800">Desa Tidak Ditemukan</h4>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            ${query ? `Tidak ada desa dengan kata kunci "<strong>${escapeHtml(query)}</strong>".` : 'Data desa tidak tersedia.'}
                        </p>
                    </div>
                    ${query ? `
                    <button type="button" onclick="clearCardSearch()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset Pencarian
                    </button>` : ''}
                </div>`;
            renderCardPagination(0, 1, CARD_PAGE_SIZE);
            return;
        }

        const totalPages = Math.ceil(matchingDesa.length / CARD_PAGE_SIZE);
        if (cardCurrentPage > totalPages) cardCurrentPage = totalPages;
        if (cardCurrentPage < 1) cardCurrentPage = 1;

        const startIdx = (cardCurrentPage - 1) * CARD_PAGE_SIZE;
        const paginatedItems = matchingDesa.slice(startIdx, startIdx + CARD_PAGE_SIZE);

        const startNum = startIdx + 1;
        const endNum = Math.min(startIdx + CARD_PAGE_SIZE, matchingDesa.length);

        if (countBadge) {
            countBadge.innerHTML = `
                <span>Menampilkan <strong class="text-slate-900">${startNum}-${endNum}</strong> dari <strong class="text-slate-900">${matchingDesa.length}</strong> desa</span>
                ${query ? `<span class="text-amber-600 font-semibold">(pencarian "${escapeHtml(query)}")</span>` : ''}
            `;
        }

        let cardsHtml = '';
        paginatedItems.forEach(d => {
            let badgeClass = "bg-emerald-100 text-emerald-800 border-emerald-200";
            let statusText = "Full (100%)";
            if (d.status === "sebagian") {
                badgeClass = "bg-amber-100 text-amber-800 border-amber-200";
                statusText = "Sebagian";
            } else if (d.status === "belum") {
                badgeClass = "bg-rose-100 text-rose-800 border-rose-200";
                statusText = "0% Belum";
            }

            const highlightedNama = highlightMatch(d.nama || 'Desa Tanpa Nama', query);

            cardsHtml += `
                <div onclick="focusDesaMarker(${d.id}, ${d.lat}, ${d.lng})"
                     class="group p-4 bg-white border border-slate-200/90 rounded-2xl hover:border-amber-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm leading-snug group-hover:text-amber-600 transition truncate">
                                Desa ${highlightedNama}
                            </h4>
                            <span class="px-2 py-0.5 border rounded-md text-[9px] font-extrabold ${badgeClass} shrink-0">${statusText}</span>
                        </div>
                        <p class="text-[10px] font-semibold text-slate-400 mb-2 flex items-center gap-1">
                            <i class="fa-solid fa-location-dot text-[9px] text-slate-400"></i> ${escapeHtml(d.kabupaten || 'Jambi')}
                        </p>
                        <div class="space-y-1 text-[11px] text-slate-600 font-medium bg-slate-50/70 p-2 rounded-xl border border-slate-100">
                            <div class="flex justify-between"><span>Teraliri:</span> <strong class="text-emerald-600">${d.berlistrik} Rumah</strong></div>
                            <div class="flex justify-between"><span>Belum:</span> <strong class="text-rose-600">${d.belumBerlistrik} Rumah</strong></div>
                        </div>
                    </div>
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                        <div class="flex items-center gap-1">
                            <span class="text-slate-400 text-[10px]">Rasio:</span>
                            <span class="text-blue-700 font-black">${d.rasio}%</span>
                        </div>
                        <span class="text-[10px] text-amber-600 font-bold opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                            Peta <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                        </span>
                    </div>
                </div>`;
        });

        container.innerHTML = cardsHtml;
        renderCardPagination(matchingDesa.length, cardCurrentPage, CARD_PAGE_SIZE);
    }

    function renderCardPagination(totalItems, currentPage, pageSize) {
        const paginationContainer = document.getElementById('desa-cards-pagination');
        if (!paginationContainer) return;

        const totalPages = Math.ceil(totalItems / pageSize);
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            paginationContainer.classList.add('hidden');
            return;
        }

        paginationContainer.classList.remove('hidden');

        const startItem = (currentPage - 1) * pageSize + 1;
        const endItem = Math.min(currentPage * pageSize, totalItems);

        let pages = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) pages.push(i);
        } else {
            pages.push(1);
            if (currentPage > 3) pages.push('...');

            let start = Math.max(2, currentPage - 1);
            let end = Math.min(totalPages - 1, currentPage + 1);
            for (let i = start; i <= end; i++) pages.push(i);

            if (currentPage < totalPages - 2) pages.push('...');
            pages.push(totalPages);
        }

        let pageButtonsHtml = pages.map(p => {
            if (p === '...') {
                return `<span class="px-2 py-1 text-slate-400 text-xs font-bold">...</span>`;
            }
            const isActive = p === currentPage;
            return `
                <button type="button" onclick="goToCardPage(${p})"
                    class="min-w-[32px] h-8 px-2 rounded-lg text-xs font-bold transition flex items-center justify-center cursor-pointer ${
                        isActive
                            ? 'bg-amber-500 text-slate-950 font-black shadow-xs'
                            : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'
                    }">
                    ${p}
                </button>
            `;
        }).join('');

        paginationContainer.innerHTML = `
            <div class="text-xs font-bold text-slate-500">
                Halaman <span class="text-slate-900 font-extrabold">${currentPage}</span> dari <span class="text-slate-900 font-extrabold">${totalPages}</span>
                <span class="text-slate-400 font-normal">(${startItem}-${endItem} dari ${totalItems} desa)</span>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap justify-center">
                <button type="button" onclick="goToCardPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}
                    class="px-2.5 h-8 rounded-lg text-xs font-bold flex items-center gap-1 transition ${
                        currentPage === 1
                            ? 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200/60'
                            : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 cursor-pointer shadow-xs'
                    }">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </button>
                ${pageButtonsHtml}
                <button type="button" onclick="goToCardPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}
                    class="px-2.5 h-8 rounded-lg text-xs font-bold flex items-center gap-1 transition ${
                        currentPage === totalPages
                            ? 'bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200/60'
                            : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 cursor-pointer shadow-xs'
                    }">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </button>
            </div>
        `;
    }

    function renderCharts(filteredList) {
        if (typeof ApexCharts === 'undefined') return;

        const elBar = document.getElementById('chart-kabupaten-bar');
        const elDonut = document.getElementById('chart-status-donut');

        if (!elBar || !elDonut) return;

        // 1. Chart Bar Kabupaten
        const kabGroup = {};
        filteredList.forEach(d => {
            let rawKab = (d.kabupaten || 'Jambi').trim();
            let cleanKab = rawKab.replace(/^KABUPATEN\s+/i, '').replace(/^KOTA\s+/i, 'Kota ');
            cleanKab = cleanKab.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase()).join(
                ' ');

            if (!kabGroup[cleanKab]) {
                kabGroup[cleanKab] = {
                    totalRasio: 0,
                    count: 0
                };
            }
            kabGroup[cleanKab].totalRasio += d.rasio;
            kabGroup[cleanKab].count += 1;
        });

        let kabCategories = Object.keys(kabGroup);
        let kabData = kabCategories.map(k => Math.round(kabGroup[k].totalRasio / kabGroup[k].count));

        if (kabCategories.length === 0) {
            kabCategories = ['Belum Ada Data'];
            kabData = [0];
        }

        const optionsKab = {
            series: [{
                name: 'Persentase Teraliri',
                data: kabData
            }],
            chart: {
                type: 'bar',
                height: Math.max(260, kabCategories.length * 32),
                toolbar: {
                    show: false
                },
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            colors: ['#2563eb', '#0284c7', '#0d9488', '#16a34a', '#d97706', '#dc2626', '#8b5cf6', '#ec4899'],
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '60%',
                    borderRadius: 5,
                    distributed: true,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#1e293b'],
                    fontSize: '10px',
                    fontWeight: 800
                },
                formatter: val => val + '%',
                offsetX: 6
            },
            xaxis: {
                max: 100,
                labels: {
                    formatter: val => val + '%',
                    style: {
                        fontSize: '10px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '10px',
                        fontWeight: 700
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9'
            },
            legend: {
                show: false
            }
        };

        if (chartKabupatenInstance) {
            chartKabupatenInstance.destroy();
        }
        chartKabupatenInstance = new ApexCharts(elBar, optionsKab);
        chartKabupatenInstance.render();

        // 2. Chart Donut Status Desa
        const fullCount = filteredList.filter(d => d.status === 'full').length;
        const sebCount = filteredList.filter(d => d.status === 'sebagian').length;
        const belumCount = filteredList.filter(d => d.status === 'belum').length;

        const seriesDonut = [fullCount, sebCount, belumCount];

        const optionsDonut = {
            series: seriesDonut,
            labels: ['100% Terang', 'Sebagian Teraliri', 'Belum Berlistrik'],
            chart: {
                type: 'donut',
                height: 270,
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            colors: ['#10b981', '#f59e0b', '#ef4444'],
            legend: {
                position: 'bottom',
                fontSize: '11px',
                fontWeight: 700
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Desa',
                                fontSize: '11px',
                                fontWeight: 800,
                                color: '#64748b'
                            }
                        }
                    }
                }
            }
        };

        if (chartDonutInstance) {
            chartDonutInstance.destroy();
        }
        chartDonutInstance = new ApexCharts(elDonut, optionsDonut);
        chartDonutInstance.render();
    }

    function filterDesaInteractive() {
        const query = document.getElementById('search-desa').value.toLowerCase().trim();
        const status = document.getElementById('status-filter').value;

        let filtered = dataDesa.filter(d =>
            d.nama.toLowerCase().includes(query) ||
            d.kabupaten.toLowerCase().includes(query)
        );

        if (status !== 'all') {
            filtered = filtered.filter(d => d.status === status);
        }

        currentFilteredData = filtered;
        renderMarkersOnMap(filtered);
        renderStatistik(filtered);
        renderCharts(filtered);
    }

    function resetMapFilter() {
        document.getElementById('search-desa').value = '';
        document.getElementById('status-filter').value = 'all';
        clearCardSearch();
        filterDesaInteractive();
        map.setView([-1.6000, 102.7500], 8);
    }

    function focusDesaMarker(id, lat, lng) {
        if (!isNaN(lat) && !isNaN(lng)) {
            const mapSection = document.getElementById('peta-elektrifikasi');
            if (mapSection) {
                mapSection.scrollIntoView({
                    behavior: 'smooth'
                });
            }
            setTimeout(() => {
                map.flyTo([lat, lng], 13, {
                    duration: 1.2
                });
                if (markerMap[id]) {
                    setTimeout(() => {
                        markerMap[id].openPopup();
                    }, 1200);
                }
            }, 300);
        }
    }

    function fetchRealtimeKpiStats() {
        fetch("{{ route('api.kpi.stats') }}")
            .then(res => res.json())
            .then(data => {
                const elTotalApproved = document.getElementById('kpi-total-approved');
                if (elTotalApproved) {
                    elTotalApproved.innerHTML =
                        `<i class="fa-solid fa-arrow-trend-up"></i> +${new Intl.NumberFormat('id-ID').format(data.total_approved_global)} Terpasang Baru`;
                }
                const elTeraliriMain = document.getElementById('kpi-teraliri-main');
                if (elTeraliriMain) {
                    elTeraliriMain.innerText = new Intl.NumberFormat('id-ID').format(data.total_teraliri);
                }
                const elOverallRasio = document.getElementById('kpi-overall-rasio');
                if (elOverallRasio) {
                    elOverallRasio.innerText = `${data.overall_rasio}%`;
                }
                const elOverallRasio2 = document.getElementById('kpi-overall-rasio-2');
                if (elOverallRasio2) {
                    elOverallRasio2.innerText = `${data.overall_rasio}%`;
                }
                const elBar = document.getElementById('kpi-overall-rasio-bar');
                if (elBar) {
                    elBar.style.width = `${Math.min(100, data.overall_rasio)}%`;
                }
                const elSub = document.getElementById('kpi-teraliri-sub');
                if (elSub) {
                    elSub.innerText = `${new Intl.NumberFormat('id-ID').format(data.total_teraliri)} Teraliri`;
                }
                const elTargetRt = document.getElementById('kpi-target-rt');
                if (elTargetRt) {
                    elTargetRt.innerText = `Target: ${new Intl.NumberFormat('id-ID').format(data.total_rt)} Rumah`;
                }
            })
            .catch(err => console.log('KPI sync log:', err));
    }

    window.addEventListener('load', () => {
        renderMarkersOnMap(dataDesa);
        renderStatistik(dataDesa);
        setTimeout(() => {
            renderCharts(dataDesa);
        }, 150);

        // Fetch fresh desa data from API
        loadDesaDataFromApi();

        setInterval(fetchRealtimeKpiStats, 10000);
        window.addEventListener('focus', fetchRealtimeKpiStats);

        window.addEventListener('resize', () => {
            map.invalidateSize();
        });
    });

</script>
@endpush
