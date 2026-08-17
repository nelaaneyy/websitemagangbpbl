<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPELITA ESDM - Portal Transparansi Bantuan Pasang Baru Listrik</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0F172A">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-slate-50 to-blue-50/50 text-slate-900 min-h-screen flex flex-col justify-between antialiased selection:bg-amber-500 selection:text-slate-950" x-data="{ mobileMenu: false }">

    <!-- Top Utility Announcement & Call Center Bar -->
    <div class="bg-slate-900 text-white text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-400 font-extrabold text-[11px] border border-amber-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    Portal Data & Layanan Publik ESDM
                </span>
                <span class="text-slate-300 text-[11px] font-semibold hidden md:inline">Sistem Pelayanan & Elektrifikasi Listrik Terpadu (SIPELITA)</span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-slate-300 font-medium">
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-phone text-amber-400"></i> Call Center: 135 / (021) 3804242</span>
                <span class="hidden sm:inline-flex items-center gap-1.5"><i class="fa-solid fa-clock text-amber-400"></i> Jam Kerja: 08.00 - 16.00 WIB</span>
            </div>
        </div>
    </div>

    {{-- NAVBAR UTAMA (Clean Minimalist White & Backdrop Blur) --}}
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo & Instansi Header -->
                <a href="{{ route('warga.index') }}" class="flex items-center gap-3.5 group">
                    <div class="w-11 h-11 bg-slate-900 text-amber-400 rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-bolt-lightning text-xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-xl text-slate-900 tracking-tight group-hover:text-amber-600 transition-colors">SIPELITA</span>
                            <span class="px-2 py-0.5 bg-amber-500 text-slate-950 text-[10px] font-black rounded uppercase tracking-wider">ESDM</span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-500 tracking-wide">Portal Transparansi BPBL</span>
                    </div>
                </a>

                <!-- Nav Links (Desktop) -->
                <div class="hidden md:flex items-center gap-1.5">
                    <a href="{{ route('warga.index') }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3.5 py-2 rounded-xl hover:bg-slate-100 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-house text-amber-500 text-xs"></i> Beranda
                    </a>
                    <a href="{{ route('warga.index') }}#dashboard-elektrifikasi" class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3.5 py-2 rounded-xl hover:bg-slate-100 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-chart-simple text-blue-600 text-xs"></i> Realisasi
                    </a>
                    <a href="{{ route('warga.search') }}" class="text-xs font-bold text-slate-700 hover:text-slate-900 px-3.5 py-2 rounded-xl hover:bg-slate-100 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-amber-500 text-xs"></i> Cek Status
                    </a>

                    <!-- CTAs -->
                    <div class="ml-3 flex items-center gap-2.5">
                        <a href="{{ route('warga.pengajuan') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold text-xs rounded-xl transition-all shadow-xs hover:-translate-y-0.5">
                            <i class="fa-solid fa-paper-plane text-slate-950"></i> Daftar Calon Penerima
                        </a>

                        @auth
                            <a href="{{ auth()->user()->role === 'kepala_desa' ? route('kepaladesa.index') : route('dinasesdm.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition-all shadow-xs">
                                <i class="fa-solid fa-gauge-high text-amber-400"></i> Portal Petugas ({{ auth()->user()->role === 'instansi' ? 'ESDM' : 'Kades' }})
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 font-bold text-xs rounded-xl transition-all">
                                <i class="fa-solid fa-user-lock text-slate-500"></i> Portal Petugas
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile Hamburger Button -->
                <button @click.stop="mobileMenu = !mobileMenu" type="button" class="md:hidden inline-flex items-center justify-center p-2.5 rounded-xl text-slate-700 hover:bg-slate-100 focus:outline-none transition">
                    <i class="fa-solid" :class="mobileMenu ? 'fa-xmark text-xl' : 'fa-bars text-xl'"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Navigation -->
        <div x-cloak x-show="mobileMenu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 shadow-xl space-y-2" @click.stop>
            <a href="{{ route('warga.index') }}" class="flex items-center gap-3 px-4 py-3 text-slate-800 font-bold rounded-xl hover:bg-slate-50 transition">
                <i class="fa-solid fa-house text-amber-500 w-5 text-center"></i> Beranda
            </a>
            <a href="{{ route('warga.index') }}#dashboard-elektrifikasi" class="flex items-center gap-3 px-4 py-3 text-slate-800 font-bold rounded-xl hover:bg-slate-50 transition">
                <i class="fa-solid fa-chart-simple text-blue-600 w-5 text-center"></i> Realisasi
            </a>
            <a href="{{ route('warga.search') }}" class="flex items-center gap-3 px-4 py-3 text-slate-800 font-bold rounded-xl hover:bg-slate-50 transition">
                <i class="fa-solid fa-magnifying-glass text-amber-500 w-5 text-center"></i> Cek Status
            </a>
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <a href="{{ route('warga.pengajuan') }}" class="flex items-center justify-center gap-2 px-4 py-3 bg-amber-500 text-slate-950 font-extrabold rounded-xl shadow-xs transition">
                    <i class="fa-solid fa-paper-plane text-slate-950"></i> Daftar Calon Penerima
                </a>
                @auth
                    <a href="{{ auth()->user()->role === 'kepala_desa' ? route('kepaladesa.index') : route('dinasesdm.index') }}" class="flex items-center justify-center gap-2 px-4 py-3 text-white bg-slate-900 rounded-xl font-bold transition">
                        <i class="fa-solid fa-gauge-high text-amber-400"></i> Portal Petugas ({{ auth()->user()->name }})
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 px-4 py-3 text-slate-700 border border-slate-300 rounded-xl font-bold hover:bg-slate-50 transition">
                        <i class="fa-solid fa-user-lock text-slate-500"></i> Portal Petugas
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- KONTEN UTAMA --}}
    <main class="mb-auto flex-1">
        @yield('content')
    </main>

    {{-- FOOTER E-GOVERNMENT --}}
    <footer class="bg-slate-900 text-white border-t-4 border-amber-500 mt-16 pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-800">
                <!-- Branding -->
                <div class="space-y-4 md:col-span-1">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-slate-950 font-bold">
                            <i class="fa-solid fa-bolt-lightning text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-base">SIPELITA ESDM</h4>
                            <p class="text-xs text-amber-400 font-semibold">Pemerintah Provinsi & Daerah</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">
                        Sistem Pelayanan & Elektrifikasi Listrik Terpadu untuk pemerataan energi listrik dan bantuan pasang baru gratis bagi masyarakat kurang mampu.
                    </p>
                </div>

                <!-- Navigasi Cepat -->
                <div class="space-y-3">
                    <h5 class="font-bold text-sm text-amber-400 uppercase tracking-wider">Layanan Publik</h5>
                    <ul class="space-y-2 text-xs text-slate-300 font-medium">
                        <li><a href="{{ route('warga.pengajuan') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-amber-400"></i> Pendaftaran Pasang Baru</a></li>
                        <li><a href="{{ route('warga.search') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-amber-400"></i> Lacak Status NIK Permohonan</a></li>
                        <li><a href="{{ route('panduan.index') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-amber-400"></i> Panduan & Syarat DTKS</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-amber-400"></i> Portal Verifikator ESDM & Desa</a></li>
                    </ul>
                </div>

                <!-- Kontak Resmi -->
                <div class="space-y-3">
                    <h5 class="font-bold text-sm text-amber-400 uppercase tracking-wider">Kontak Instansi</h5>
                    <ul class="space-y-2 text-xs text-slate-300 font-medium">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-amber-400 mt-0.5"></i>
                            <span>Dinas Energi & Sumber Daya Mineral (ESDM)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-amber-400"></i>
                            <span>Call Center: 135 / PLN Terpadu</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-amber-400"></i>
                            <span>esdm@jambiprov.go.id</span>
                        </li>
                    </ul>
                </div>

                <!-- Maklumat Pelayanan -->
                <div class="space-y-3">
                    <h5 class="font-bold text-sm text-amber-400 uppercase tracking-wider">Komitmen Layanan</h5>
                    <div class="bg-slate-800/90 p-3.5 rounded-2xl border border-slate-700 text-xs text-slate-300 space-y-2">
                        <div class="flex items-center gap-2 text-amber-400 font-bold">
                            <i class="fa-solid fa-shield-halved"></i> 100% Bebas Biaya (Gratis)
                        </div>
                        <p class="text-[11px] text-slate-400 leading-normal font-medium">
                            Seluruh verifikasi dan pemasangan Bantuan Pasang Baru Listrik (BPBL) tidak dipungut biaya apapun dari warga penerima manfaat.
                        </p>
                    </div>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4 font-medium">
                <p>&copy; {{ date('Y') }} Dinas Energi dan Sumber Daya Mineral . Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center gap-4">
                    <span class="hover:text-white transition cursor-pointer">Privasi & Ketentuan</span>
                    <span>•</span>
                    <span class="hover:text-white transition cursor-pointer">Standar SPBE e-Gov</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

