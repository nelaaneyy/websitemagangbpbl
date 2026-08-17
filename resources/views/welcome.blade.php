@extends('layouts.app')

@section('content')
<!-- Design Read: Civic Tech / Public Service Portal for Citizens & Verification Officers, with a Modern Civic Data & Service Portal language, leaning toward Light Mode Ultra-Clean (bg-[#F8FAFC] / bg-white) + Deep Navy (#0F172A) + ESDM Amber (#F59E0B) design system. -->

<div>

    <!-- HERO SECTION (Sophisticated Gradient & Glassmorphism + Geometric Ambient Blobs + Realtime NIK Validation) -->
    <section class="relative overflow-hidden pt-10 pb-16 lg:pt-14 lg:pb-20 bg-gradient-to-br from-slate-50 via-slate-50/80 to-blue-50/50 text-slate-900 border-b border-slate-200/80">
        
        <!-- Decorative Ambient Geometric Blur Blobs (ESDM Amber Accent) -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-[30rem] h-[30rem] bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/3 -translate-y-1/2 w-[35rem] h-[35rem] bg-blue-100/30 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-12 gap-10 items-center">
                
                <!-- Left Column (Hero Content & Value Proposition) -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-100/80 backdrop-blur-xs border border-amber-300/70 text-amber-950 text-xs font-extrabold tracking-wide shadow-xs">
                        <i class="fa-solid fa-award text-amber-600"></i> Program Strategis ESDM {{ date('Y') }}
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight text-slate-900">
                        Transparansi Penyaluran <span class="text-amber-600">Bantuan Pasang Baru Listrik</span> (BPBL)
                    </h1>

                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed max-w-2xl font-normal">
                        Portal resmi Dinas Energi dan Sumber Daya Mineral (ESDM) untuk penyaluran bantuan instalasi dan kWH meter gratis 450 VA & 900 VA bagi masyarakat prasejahtera terdaftar DTKS / P3KE.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('warga.pengajuan') }}" class="inline-flex items-center justify-center gap-2.5 px-7 py-3.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                            <i class="fa-solid fa-paper-plane text-slate-950"></i>
                            Daftar Calon Penerima (Gratis)
                        </a>

                        <a href="#dashboard-elektrifikasi" class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 bg-white/80 backdrop-blur-md hover:bg-white text-slate-800 font-bold text-xs rounded-xl border border-slate-300 hover:-translate-y-0.5 transition-all shadow-xs">
                            <i class="fa-solid fa-chart-simple text-blue-600"></i>
                            Lihat Realisasi Data
                        </a>
                    </div>
                </div>

                <!-- Right Column (Glassmorphism Card 'Cek Status NIK' with Real-Time Validation) -->
                <div class="lg:col-span-5" id="cek-nik" x-data="{ nikInput: '', get isValid() { return /^\d{16}$/.test(this.nikInput) } }">
                    <div class="bg-white/80 backdrop-blur-md border border-slate-200 shadow-xl rounded-2xl p-6 sm:p-8 text-slate-800 space-y-6 transition-all duration-300 hover:shadow-2xl hover:border-amber-400/50 relative z-10">
                        <div class="flex items-center gap-3.5 pb-4 border-b border-slate-200/80">
                            <div class="w-12 h-12 rounded-2xl bg-slate-900 text-amber-400 flex items-center justify-center shadow-md text-xl font-bold">
                                <i class="fa-solid fa-fingerprint"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-base text-slate-900 tracking-tight">Cek Status Berkas NIK</h3>
                                <p class="text-xs text-slate-500 font-medium">Masukkan 16 digit NIK KTP Anda untuk melacak posisi bantuan</p>
                            </div>
                        </div>

                        <form action="{{ route('warga.search') }}" method="GET" class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="nik_hero_input" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Nomor Induk Kependudukan (NIK)</label>
                                    <span class="text-[11px] font-bold" x-text="nikInput.length + '/16 Digit'" :class="isValid ? 'text-emerald-600' : (nikInput.length > 0 ? 'text-rose-600' : 'text-slate-400')"></span>
                                </div>

                                <div class="relative">
                                    <input type="text" id="nik_hero_input" name="nik" maxlength="16" x-model="nikInput"
                                        @input="nikInput = nikInput.replace(/\D/g, '')"
                                        placeholder="Masukkan 16 digit NIK KTP..." required
                                        :class="{
                                            'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/40 text-emerald-950': isValid,
                                            'border-rose-500 ring-2 ring-rose-500/20 bg-rose-50/40 text-rose-950': nikInput.length > 0 && !isValid,
                                            'border-slate-300/80 bg-white/90 text-slate-900': nikInput.length === 0
                                        }"
                                        class="w-full pl-11 pr-10 py-3.5 rounded-xl text-xs font-extrabold tracking-wider transition-all placeholder:text-slate-400 placeholder:font-normal shadow-xs focus:outline-none">
                                    
                                    <i class="fa-solid fa-id-card absolute left-4 top-4 text-slate-400 text-sm"></i>

                                    <!-- Visual Indicator Icons (Green Check vs Red Cross) -->
                                    <div class="absolute right-3.5 top-3.5 flex items-center">
                                        <template x-if="isValid">
                                            <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs shadow-xs animate-bounce">
                                                <i class="fa-solid fa-check font-black"></i>
                                            </span>
                                        </template>
                                        <template x-if="nikInput.length > 0 && !isValid">
                                            <span class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs shadow-xs">
                                                <i class="fa-solid fa-xmark font-black"></i>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Real-time Feedback Status Message -->
                                <div class="mt-2 text-[11px] font-bold">
                                    <template x-if="nikInput.length === 0">
                                        <span class="text-slate-500 font-medium">Contoh: 150101XXXXXXXXXX (Tepat 16 angka)</span>
                                    </template>
                                    <template x-if="isValid">
                                        <span class="text-emerald-700 font-bold flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Format NIK Valid! Siap diperiksa.
                                        </span>
                                    </template>
                                    <template x-if="nikInput.length > 0 && !isValid">
                                        <span class="text-rose-600 font-bold flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-xmark text-rose-500"></i> NIK kurang <span x-text="16 - nikInput.length"></span> digit lagi (harus 16 angka).
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <button type="submit" 
                                    :disabled="!isValid"
                                    :class="isValid ? 'bg-amber-500 hover:bg-amber-600 text-slate-950 cursor-pointer shadow-md hover:shadow-lg' : 'bg-slate-200 text-slate-400 cursor-not-allowed border-slate-300'"
                                    class="w-full py-3.5 font-extrabold text-xs rounded-xl transition-all flex items-center justify-center gap-2 border">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <span>Periksa Status Berkas Saya</span>
                            </button>
                        </form>

                        <div class="p-4 bg-amber-50/90 backdrop-blur-xs rounded-xl border border-amber-200/80 flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-amber-600 text-base mt-0.5 shrink-0"></i>
                            <p class="text-xs text-amber-950 leading-relaxed font-medium">
                                Belum terdaftar? Pendaftaran dapat diajukan secara langsung lewat tombol <strong>Daftar Calon Penerima</strong> di samping atau usulan Perangkat Desa.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION B: OPSI LAYANAN PROGRAM (BPBL Mandiri Warga & Lisdes Usulan Pemdes) -->
    <section id="program-bantuan" class="py-16 bg-white border-b border-slate-200/80" x-data="{ openLisdesModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center max-w-3xl mx-auto space-y-2">
                <span class="px-3.5 py-1 bg-amber-100 text-amber-900 font-extrabold text-xs rounded-full uppercase tracking-wider border border-amber-300">
                    Opsi Layanan Program
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Kategori Program Bantuan Listrik ESDM</h2>
                <p class="text-slate-600 text-sm">Pilih jenis permohonan yang sesuai dengan status kebutuhan di wilayah Anda.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Card 1: BPBL Mandiri Warga -->
                <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-slate-200 hover:border-amber-500/50 hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="w-14 h-14 bg-slate-900 text-amber-400 rounded-2xl flex items-center justify-center text-xl shadow-xs">
                            <i class="fa-solid fa-house-chimney-user"></i>
                        </div>
                        <span class="inline-block px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 text-xs font-extrabold rounded-full">
                            Perorangan / Warga Kurang Mampu
                        </span>
                        <h3 class="text-2xl font-extrabold text-slate-900">
                            Bantuan Pasang Baru Listrik (BPBL)
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Program bantuan bebas biaya pemasangan kWH meter baru 450 / 900 VA beserta instalasi rumah untuk masyarakat perorangan yang terdaftar dalam DTKS / P3KE.
                        </p>

                        <ul class="space-y-2 text-xs text-slate-700 font-medium pt-2">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Gratis Instalasi 3 Titik Lampu & 1 Stop Kontak</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Sertifikat Laik Operasi (SLO) Resmi</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-600"></i> Voucher Token Listrik Perdana GRATIS</li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-200">
                        <a href="{{ route('warga.pengajuan') }}" class="w-full py-3.5 px-6 bg-slate-900 hover:bg-slate-800 text-amber-400 font-extrabold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 text-xs border border-slate-800">
                            <span>Form Pendaftaran Mandiri Warga</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 2: Listrik Desa (Lisdes) -->
                <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-slate-200 hover:border-amber-500/50 hover:shadow-lg transition-all duration-200 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="w-14 h-14 bg-amber-500 text-slate-950 rounded-2xl flex items-center justify-center text-xl shadow-xs">
                            <i class="fa-solid fa-tower-cell"></i>
                        </div>
                        <span class="inline-block px-3 py-1 bg-white text-slate-800 border border-slate-300 text-xs font-extrabold rounded-full">
                            Usulan Oleh Pemerintah Desa
                        </span>
                        <h3 class="text-2xl font-extrabold text-slate-900">
                            Bantuan Listrik Desa (Lisdes)
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-medium">
                            Program usulan jaringan infrastruktur listrik komunal untuk dusun/wilayah desa yang belum terjangkau jaringan utama PLN.
                        </p>

                        <ul class="space-y-2 text-xs text-slate-700 font-medium pt-2">
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-600"></i> Pembangunan Tiang & Trafo Distribusi</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-600"></i> Diajukan Secara Resmi Oleh Kepala Desa</li>
                            <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-amber-600"></i> Penilaian Kelayakan Oleh Tim Teknis ESDM</li>
                        </ul>
                    </div>

                    <div class="pt-4 border-t border-slate-200">
                        <button @click.stop="openLisdesModal = true" type="button" class="w-full py-3.5 px-6 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 text-xs cursor-pointer">
                            <i class="fa-solid fa-file-signature"></i>
                            <span>Lihat Syarat Usulan Lisdes</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Syarat Lisdes (Oleh Desa) -->
            <div x-cloak x-show="openLisdesModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-xs flex items-center justify-center p-4" @click.stop="openLisdesModal = false">
                <div @click.stop class="bg-white max-w-lg w-full rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 relative">
                    <button @click="openLisdesModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-amber-500 text-slate-950 rounded-2xl flex items-center justify-center text-xl font-bold">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900">Syarat Usulan Lisdes</h3>
                            <p class="text-xs text-slate-500 font-medium">Persyaratan Bantuan Listrik Desa (Pengajuan Pemdes)</p>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs text-slate-700 bg-slate-50 p-4 rounded-2xl border border-slate-200 leading-relaxed font-medium">
                        <div class="flex gap-2">
                            <span class="font-extrabold text-amber-600 shrink-0">1.</span>
                            <p>Surat Permohonan Resmi dari Kepala Desa ditujukan kepada Kepala Dinas ESDM Provinsi/Kabupaten.</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-extrabold text-amber-600 shrink-0">2.</span>
                            <p>Daftar Nama Calon Penerima Manfaat (Jumlah KK/RT yang belum berlistrik) divalidasi Kades.</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-extrabold text-amber-600 shrink-0">3.</span>
                            <p>Berita Acara Musrenbangdes atau Surat Pernyataan Kesediaan Lahan Hibah untuk tiang/trafo PLN.</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="font-extrabold text-amber-600 shrink-0">4.</span>
                            <p>Peta lokasi atau titik koordinat GPS dusun/wilayah sasaran pembangunan jaringan listrik.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button @click="openLisdesModal = false" class="w-full py-3 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                            Tutup
                        </button>
                        <a href="{{ route('login') }}" class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl shadow-xs text-center transition-colors">
                            Login Kades Untuk Usulkan &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION C: 4 TAHAPAN PROSE BANTUAN (Interactive Workflow & Dokumen Syarat) -->
    <section class="py-16 bg-[#F8FAFC] border-b border-slate-200/80" x-data="{ activeStep: 1 }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="text-center max-w-3xl mx-auto space-y-2">
                <span class="px-3.5 py-1 bg-amber-100 text-amber-900 font-extrabold text-xs rounded-full uppercase tracking-wider border border-amber-300">
                    Alur Layanan Transparan
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">4 Tahapan Proses Bantuan Listrik (BPBL)</h2>
                <p class="text-slate-600 text-sm">Klik setiap tahapan di bawah ini untuk melihat persyaratan dan dokumen yang dibutuhkan.</p>
            </div>

            <!-- Interactive Timeline Steps Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Step 1 -->
                <button type="button" @click="activeStep = 1"
                        :class="activeStep === 1 ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-amber-400'"
                        class="p-5 rounded-2xl border text-left transition-all duration-200 space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span :class="activeStep === 1 ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-700'"
                              class="w-8 h-8 rounded-xl font-black text-xs flex items-center justify-center">1</span>
                        <span class="text-[11px] font-bold opacity-75">Tahap 1</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm">Pendaftaran Data DTKS</h4>
                        <p class="text-xs opacity-80 mt-1 font-medium">Usulan warga & validasi awal</p>
                    </div>
                </button>

                <!-- Step 2 -->
                <button type="button" @click="activeStep = 2"
                        :class="activeStep === 2 ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-amber-400'"
                        class="p-5 rounded-2xl border text-left transition-all duration-200 space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span :class="activeStep === 2 ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-700'"
                              class="w-8 h-8 rounded-xl font-black text-xs flex items-center justify-center">2</span>
                        <span class="text-[11px] font-bold opacity-75">Tahap 2</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm">Verifikasi Lapangan</h4>
                        <p class="text-xs opacity-80 mt-1 font-medium">Survei fisik oleh Kepala Desa</p>
                    </div>
                </button>

                <!-- Step 3 -->
                <button type="button" @click="activeStep = 3"
                        :class="activeStep === 3 ? 'bg-slate-900 text-white border-slate-900 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-amber-400'"
                        class="p-5 rounded-2xl border text-left transition-all duration-200 space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span :class="activeStep === 3 ? 'bg-amber-500 text-slate-950' : 'bg-slate-100 text-slate-700'"
                              class="w-8 h-8 rounded-xl font-black text-xs flex items-center justify-center">3</span>
                        <span class="text-[11px] font-bold opacity-75">Tahap 3</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm">Penetapan & Terbit SLO</h4>
                        <p class="text-xs opacity-80 mt-1 font-medium">Penilaian final Dinas ESDM</p>
                    </div>
                </button>

                <!-- Step 4 -->
                <button type="button" @click="activeStep = 4"
                        :class="activeStep === 4 ? 'bg-emerald-700 text-white border-emerald-700 shadow-md' : 'bg-white text-slate-800 border-slate-200 hover:border-emerald-400'"
                        class="p-5 rounded-2xl border text-left transition-all duration-200 space-y-3 cursor-pointer">
                    <div class="flex items-center justify-between">
                        <span :class="activeStep === 4 ? 'bg-white text-emerald-800' : 'bg-slate-100 text-slate-700'"
                              class="w-8 h-8 rounded-xl font-black text-xs flex items-center justify-center">4</span>
                        <span class="text-[11px] font-bold opacity-75">Tahap Final</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm">Penyalaan Meteran PLN</h4>
                        <p class="text-xs opacity-80 mt-1 font-medium">Instalasi & kWH menyala</p>
                    </div>
                </button>

            </div>

            <!-- Active Step Detail Panel -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                
                <!-- Step 1 Details -->
                <div x-show="activeStep === 1" class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 font-black text-base flex items-center justify-center">1</div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">Tahap 1: Pendaftaran / Usulan Data DTKS</h3>
                            <p class="text-xs text-slate-500 font-medium">Warga mendaftar mandiri atau diusulkan oleh Pemerintah Desa</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4 pt-2">
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-id-card text-amber-500 mr-1.5"></i> 1. Identitas KTP</span>
                            <p class="text-slate-600 font-medium">NIK terdaftar resmi di Dukcapil & DTKS/P3KE Kemensos.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-file-invoice text-amber-500 mr-1.5"></i> 2. SKTM / Kartu Bansos</span>
                            <p class="text-slate-600 font-medium">Surat Keterangan Tidak Mampu dari Kelurahan / KIS / KKS.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-camera text-amber-500 mr-1.5"></i> 3. Foto Rumah Tampak Depan</span>
                            <p class="text-slate-600 font-medium">Dokumentasi fisik kondisi bangunan rumah pemohon.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 Details -->
                <div x-show="activeStep === 2" class="space-y-4" style="display: none;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 font-black text-base flex items-center justify-center">2</div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">Tahap 2: Verifikasi Lapangan oleh Kepala Desa</h3>
                            <p class="text-xs text-slate-500 font-medium">Pemeriksaan fisik ke lokasi rumah calon penerima manfaat</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4 pt-2">
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-street-view text-blue-600 mr-1.5"></i> Validasi Bangunan</span>
                            <p class="text-slate-600 font-medium">Memastikan rumah belum tersambung ke jaringan PLN lain.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-tower-cell text-blue-600 mr-1.5"></i> Jarak Tiang Listrik</span>
                            <p class="text-slate-600 font-medium">Mencatat tiang distribusi PLN terdekat dari lokasi rumah.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-signature text-blue-600 mr-1.5"></i> Persetujuan Kades</span>
                            <p class="text-slate-600 font-medium">Pengesahan resmi dari Kepala Desa secara sistem.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 Details -->
                <div x-show="activeStep === 3" class="space-y-4" style="display: none;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-amber-400 font-black text-base flex items-center justify-center">3</div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">Tahap 3: Penetapan Kuota & Sertifikat Laik Operasi (SLO)</h3>
                            <p class="text-xs text-slate-500 font-medium">Evaluasi teknis dan penetapan Surat Keputusan Dinas ESDM</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4 pt-2">
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-building-columns text-slate-800 mr-1.5"></i> Penetapan SK ESDM</span>
                            <p class="text-slate-600 font-medium">Penerbitan alokasi kuota daya 450 / 900 VA resmi.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-certificate text-slate-800 mr-1.5"></i> Terbit Sertifikat SLO</span>
                            <p class="text-slate-600 font-medium">Sertifikat kelayakan keamanan instalasi kelistrikan.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-file-contract text-slate-800 mr-1.5"></i> Work Order PLN</span>
                            <p class="text-slate-600 font-medium">Penerbitan perintah kerja pemasangan ke PLN Rayon.</p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 Details -->
                <div x-show="activeStep === 4" class="space-y-4" style="display: none;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white font-black text-base flex items-center justify-center">4</div>
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">Tahap 4: Penyalaan kWH Meter & Instalasi PLN</h3>
                            <p class="text-xs text-slate-500 font-medium">Eksekusi pemasangan fisik di lokasi rumah penerima manfaat</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-4 pt-2">
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-gauge-simple-high text-emerald-600 mr-1.5"></i> Meteran Listrik Gratis</span>
                            <p class="text-slate-600 font-medium">Pemasangan kWH meter baru prabayar 450 / 900 VA.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-lightbulb text-emerald-600 mr-1.5"></i> 3 Titik Lampu Gratis</span>
                            <p class="text-slate-600 font-medium">Pemasangan instalasi kabel, 3 bohlam lampu, & stop kontak.</p>
                        </div>
                        <div class="bg-[#F8FAFC] p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
                            <span class="font-extrabold text-slate-900 block"><i class="fa-solid fa-ticket text-emerald-600 mr-1.5"></i> Voucher Token Perdana</span>
                            <p class="text-slate-600 font-medium">Pemberian token listrik perdana gratis langsung aktif.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- SECTION D: CAPAIAN REALISASI / KPI METRICS (Ala Portal Data) -->
    <section id="dashboard-elektrifikasi" class="py-16 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[11px] font-extrabold text-blue-600 uppercase tracking-wider block">Dashboard Transparansi Publik</span>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Capaian Realisasi Program BPBL {{ date('Y') }}</h2>
                </div>
                <span class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                    <i class="fa-solid fa-rotate text-slate-400"></i> Update Terakhir: {{ date('d M Y') }}
                </span>
            </div>

            <!-- Grid 4 Kolom Stat Card (Terhubung Otomatis dengan Database Pengajuan Warga & Desa) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- Stat 1: Total Rumah Tangga Teraliri -->
                <div class="bg-[#F8FAFC] p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:-translate-y-0.5 transition-all duration-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rumah Tangga Teraliri</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-house-bolt"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($totalTeraliri ?? 142850, 0, ',', '.') }} <span class="text-xs font-bold text-slate-500">KK</span></div>
                    <div class="flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                        <i class="fa-solid fa-arrow-trend-up"></i> +{{ number_format($totalApprovedGlobal ?? 0, 0, ',', '.') }} Terpasang Baru
                    </div>
                </div>

                <!-- Stat 2: Target Realisasi Tahun Berjalan -->
                <div class="bg-[#F8FAFC] p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:-translate-y-0.5 transition-all duration-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Target Realisasi {{ date('Y') }}</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $overallRasio ?? 88.5 }}%</div>
                    <!-- Progress bar -->
                    <div class="space-y-1">
                        <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $overallRasio ?? 88.5) }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-500 font-bold">
                            <span>{{ number_format($totalTeraliri ?? 0, 0, ',', '.') }} Teraliri</span>
                            <span>Target: {{ number_format($totalRT ?? 0, 0, ',', '.') }} KK</span>
                        </div>
                    </div>
                </div>

                <!-- Stat 3: Daya Tersalurkan -->
                <div class="bg-[#F8FAFC] p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:-translate-y-0.5 transition-all duration-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori Daya Tersalur</span>
                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900">450 & 900 <span class="text-xs font-bold text-slate-500">VA</span></div>
                    <div class="text-[11px] font-bold text-amber-700 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> 100% Bebas Biaya Pasang
                    </div>
                </div>

                <!-- Stat 4: Rasio Elektrifikasi -->
                <div class="bg-[#F8FAFC] p-6 rounded-2xl border border-slate-200/80 shadow-xs hover:-translate-y-0.5 transition-all duration-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Rasio Elektrifikasi Daerah</span>
                        <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-800 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $overallRasio ?? 98.42 }}%</div>
                    <div class="text-[11px] font-bold text-slate-600 flex items-center gap-1">
                        <i class="fa-solid fa-flag"></i> Target 100% Jambi Terang
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- SECTION E: PETA SEBARAN (Geospatial & Interactive Distribution Visualizer) -->
    <section id="peta-elektrifikasi" class="py-16 bg-[#F8FAFC] border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="text-center max-w-3xl mx-auto space-y-2">
                <span class="px-3.5 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-xs rounded-full uppercase tracking-wider border border-emerald-200">
                    Geospatial Monitoring System
                </span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Peta Sebaran Pasang Baru Listrik Per Desa</h2>
                <p class="text-slate-600 text-sm">Arahkan kursor atau klik marker desa/kabupaten untuk melihat rincian realisasi penerima bantuan.</p>
            </div>

            <!-- Control Bar (Interactive Filter Dropdown & Search Bar) -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-filter text-amber-500"></i> Filter Status:
                    </span>
                    <select id="status-filter" onchange="filterDesaInteractive()" 
                            class="px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 focus:bg-white transition-all cursor-pointer">
                        <option value="all">Semua Status Wilayah</option>
                        <option value="full">Hijau: 100% Full Teraliri</option>
                        <option value="sebagian">Kuning: Sebagian Teraliri</option>
                        <option value="belum">Merah: Belum Teraliri (0%)</option>
                    </select>
                    <button type="button" onclick="resetMapFilter()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset Peta
                    </button>
                </div>

                <div class="w-full md:w-96">
                    <div class="relative">
                        <input id="search-desa" type="text" oninput="filterDesaInteractive()" placeholder="Ketik nama Desa atau Kabupaten di Jambi..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-xs">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Map Panel Box with Dynamic Marker Count Counter Header -->
            <div class="bg-white-900 rounded-3xl p-3 shadow-xl overflow-hidden relative">
                <div class="flex items-center justify-between px-4 py-2 text-white text-xs font-bold">
                    <span class="flex items-center gap-2 text-slate-900">
                        <i class="fa-solid fa-map-pin"></i> Visualisasi Geospasial Peta Jambi
                    </span>
                    <span id="marker-count-badge" class="px-2.5 py-0.5 rounded-full bg-slate-500/20 text-slate-900 border border-slate-500/30 text-[12px]">
                        Menampilkan 0 Desa
                    </span>
                </div>
                <div id="map" class="w-full h-96 sm:h-[520px] rounded-2xl z-10"></div>
            </div>

            <!-- Display Village Detail Cards with Click-to-Zoom Interactivity -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-2">
                    <h3 class="font-extrabold text-sm text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-list-ul text-amber-500"></i> Detail Rincian Elektrifikasi Desa
                    </h3>
                    <span class="text-xs text-slate-500 font-medium">Klik pada kartu desa di bawah untuk mengarahkan peta ke lokasi tersebut</span>
                </div>

                <div id="desa-detail-container" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Dynamic village cards filled by JS -->
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION PALING BAWAH: LAYANAN PENGADUAN RESMI (Helpdesk ESDM) -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-slate-900 text-white rounded-3xl p-8 sm:p-10 flex flex-col md:flex-row items-center justify-between gap-8 border border-slate-800 shadow-md">
                
                <div class="space-y-2 max-w-2xl text-center md:text-left">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-400 text-[11px] font-extrabold uppercase border border-amber-500/30">
                        <i class="fa-solid fa-headset"></i> Layanan Pengaduan Resmi
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Butuh Bantuan Kendala Pasang / Belum Nyala?</h3>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-normal">
                        Tim Helpdesk Dinas ESDM siap membantu menindaklanjuti kendala pendaftaran, verifikasi lokasi, atau meteran listrik yang belum menyala di daerah Anda.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 shrink-0 w-full md:w-auto">
                    <a href="https://wa.me/6281234567890?text=Halo%20Helpdesk%20ESDM,%20saya%20ingin%20bertanya%20mengenai%20bantuan%20BPBL" target="_blank" 
                       class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                        <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp CS ESDM
                    </a>
                    <a href="tel:135" class="px-6 py-3.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl transition-all shadow-xs flex items-center justify-center gap-2">
                        <i class="fa-solid fa-phone text-slate-950"></i> Call Center 135
                    </a>
                </div>

            </div>
        </div>
    </section>

</div>
@endsection

@push('styles')
    <!-- Leaflet CSS for Interactive Map & MarkerCluster -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        .leaflet-popup-content-wrapper { border-radius: 18px; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); }
        .leaflet-tooltip { border-radius: 12px; font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: 11px; padding: 6px 12px; border: 1px solid #cbd5e1; }
    </style>
@endpush

@push('scripts')
    <!-- Leaflet JS & MarkerCluster -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
        // Data Desa Dinamis dari Database
        const rawDesaData = @json($desas ?? []);

        const dataDesa = rawDesaData.map(d => {
            let color = 'gold';
            if (d.status === 'full') color = 'green';
            if (d.status === 'belum') color = 'red';

            return {
                id: d.id,
                nama: d.nama_desa,
                kabupaten: d.kabupaten,
                lat: parseFloat(d.latitude),
                lng: parseFloat(d.longitude),
                totalRt: parseInt(d.total_rt),
                berlistrik: parseInt(d.berlistrik_rt),
                belumBerlistrik: parseInt(d.belum_berlistrik_rt),
                rasio: parseFloat(d.rasio_elektrifikasi),
                status: d.status,
                color: color,
                wargaTerverifikasi: parseInt(d.warga_terverifikasi || 0),
                wargaPendingKades: parseInt(d.warga_pending_kades || 0)
            };
        });

        // Inisialisasi Peta Leaflet Berpusat di Provinsi Jambi
        const map = L.map('map').setView([-1.6000, 102.7500], 8);

        const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps | Dinas ESDM Provinsi Jambi'
        });

        const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps | Dinas ESDM Provinsi Jambi'
        });

        const osmStreets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        });

        const esriSatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Esri World Imagery'
        });

        googleHybrid.addTo(map);

        const baseMaps = {
            "🛰️ Google Satelit Terbaru + Nama Jalan": googleHybrid,
            "🗺️ Google Peta Jalan Terbaru": googleStreets,
            "🌍 OpenStreetMap Standard": osmStreets,
            "📡 Esri World Imagery": esriSatellite
        };

        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        // Map Marker Storage & Layer Group dengan MarkerCluster
        let markersGroup = L.markerClusterGroup().addTo(map);
        let markerMap = {}; // Map desa id -> Leaflet marker instance

        // Custom icon creator
        function createCustomIcon(color) {
            let iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png';
            if (color === 'gold') {
                iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png';
            } else if (color === 'red') {
                iconUrl = 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png';
            }
            return new L.Icon({
                iconUrl: iconUrl,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }

        // Render Markers Ke Peta dengan Tooltip & Popup Interaktif
        function renderMarkersOnMap(filteredList) {
            markersGroup.clearLayers();
            markerMap = {};

            const bounds = [];

            filteredList.forEach(d => {
                if (!isNaN(d.lat) && !isNaN(d.lng)) {
                    const marker = L.marker([d.lat, d.lng], { icon: createCustomIcon(d.color) });
                    
                    // Detailed Rich Popup (On Click)
                    marker.bindPopup(`
                        <div style="min-width: 220px; text-align: center; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <span style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; background-color: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 9999px;">
                                ${d.kabupaten}
                            </span>
                            <h4 style="font-weight: 900; margin: 8px 0 2px 0; font-size: 15px; color: #0f172a;">Desa ${d.nama}</h4>
                            
                            <div style="background-color: #f8fafc; padding: 8px 10px; border-radius: 12px; margin: 8px 0; border: 1px solid #e2e8f0; font-size: 11px; text-align: left;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 3px; color: #16a34a; font-weight: bold;">
                                    <span>Teraliri (Disetujui Kades):</span> <b>${d.berlistrik} KK</b>
                                </div>
                                <div style="display: flex; justify-content: space-between; color: #dc2626; font-weight: bold; margin-bottom: 3px;">
                                    <span>Belum Diverifikasi Kades:</span> <b>${d.belumBerlistrik} KK</b>
                                </div>
                                <div style="display: flex; justify-content: space-between; pt: 3px; border-top: 1px dashed #cbd5e1; color: #0f172a; font-weight: bold;">
                                    <span>Total Sasaran RT:</span> <b>${d.totalRt} KK</b>
                                </div>
                            </div>

                            <div style="font-weight: 900; font-size: 13px; color: #1d4ed8; background-color: #eff6ff; padding: 6px; border-radius: 10px; border: 1px solid #bfdbfe;">
                                Rasio Elektrifikasi: ${d.rasio}%
                            </div>
                        </div>
                    `);

                    // Tooltip Interaktif Saat Kursor Diarahkan (Hover)
                    marker.bindTooltip(`<b>Desa ${d.nama}</b> (${d.rasio}%)`, {
                        direction: 'top',
                        offset: [0, -36]
                    });

                    markersGroup.addLayer(marker);
                    markerMap[d.id] = marker;
                    bounds.push([d.lat, d.lng]);
                }
            });

            // Update Counter Header
            const badge = document.getElementById('marker-count-badge');
            if (badge) {
                badge.innerText = `Menampilkan ${filteredList.length} Desa`;
            }

            // Adjust Map View bounds if filter is active
            if (bounds.length > 0 && filteredList.length < dataDesa.length) {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 12 });
            }
        }

        // Render Widget Kartu Statistik Desa
        function renderStatistik(filtered) {
            const container = document.getElementById('desa-detail-container');
            if (!container) return;
            container.innerHTML = '';

            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
                        <i class="fa-solid fa-map-location-dot text-slate-300 text-3xl mb-2"></i>
                        <p class="text-slate-600 text-xs font-bold">Data desa tidak ditemukan.</p>
                        <p class="text-slate-400 text-[11px]">Coba ubah kata kunci pencarian atau reset filter status.</p>
                    </div>
                `;
                return;
            }

            filtered.forEach(d => {
                let badgeClass = "bg-emerald-100 text-emerald-800 border-emerald-200";
                let statusText = "Full (100%)";
                if (d.status === "sebagian") {
                    badgeClass = "bg-amber-100 text-amber-800 border-amber-200";
                    statusText = "Sebagian Teraliri";
                } else if (d.status === "belum") {
                    badgeClass = "bg-rose-100 text-rose-800 border-rose-200";
                    statusText = "0% Belum Teraliri";
                }

                container.innerHTML += `
                    <div onclick="focusDesaMarker(${d.id}, ${d.lat}, ${d.lng})" 
                         class="p-4 bg-white border border-slate-200 rounded-2xl hover:border-amber-400 hover:shadow-md transition-all duration-200 cursor-pointer group">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-extrabold text-slate-900 text-sm group-hover:text-amber-600 transition-colors flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-amber-500 text-xs"></i> Desa ${d.nama}
                            </h4>
                            <span class="px-2 py-0.5 border rounded-md text-[10px] font-extrabold ${badgeClass}">
                                ${statusText}
                            </span>
                        </div>
                        <p class="text-[11px] font-semibold text-slate-400 mb-2.5">${d.kabupaten}</p>
                        <div class="space-y-1.5 text-xs text-slate-600 font-medium">
                            <div class="flex justify-between"><span>Teraliri (Disetujui Kades):</span> <strong class="text-emerald-600">${d.berlistrik} KK</strong></div>
                            <div class="flex justify-between"><span>Belum Diverifikasi Kades:</span> <strong class="text-rose-600">${d.belumBerlistrik} KK</strong></div>
                            <div class="flex justify-between border-t border-slate-100 pt-1"><span>Total Sasaran RT:</span> <strong class="text-slate-800">${d.totalRt} KK</strong></div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                            <span class="text-slate-500 text-[11px]">Rasio Elektrifikasi:</span>
                            <span class="text-blue-700 text-sm font-black">${d.rasio}%</span>
                        </div>
                    </div>
                `;
            });
        }

        // Interactive Filter & Real-Time Search Handler
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

            renderMarkersOnMap(filtered);
            renderStatistik(filtered);
        }

        // Reset filter ke kondisi default
        function resetMapFilter() {
            document.getElementById('search-desa').value = '';
            document.getElementById('status-filter').value = 'all';
            filterDesaInteractive();
            map.setView([-1.6000, 102.7500], 8);
        }

        // Click Village Card to Fly To & Open Marker Popup
        function focusDesaMarker(id, lat, lng) {
            if (!isNaN(lat) && !isNaN(lng)) {
                const mapSection = document.getElementById('peta-elektrifikasi');
                if (mapSection) {
                    mapSection.scrollIntoView({ behavior: 'smooth' });
                }
                setTimeout(() => {
                    map.flyTo([lat, lng], 13, { duration: 1.2 });
                    if (markerMap[id]) {
                        setTimeout(() => {
                            markerMap[id].openPopup();
                        }, 1200);
                    }
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderMarkersOnMap(dataDesa);
            renderStatistik(dataDesa);
        });
    </script>
@endpush
