<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-LISTRIK ESDM - Portal Transparansi Bantuan Pasang Baru Listrik dan Listrik Perdesaan</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0F172A">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-jambi.png') }}">
    @stack('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scrollbar-gutter: stable;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-linear-to-br from-slate-50 via-slate-50 to-blue-50/50 text-slate-900 min-h-screen flex flex-col justify-between antialiased selection:bg-amber-500 selection:text-slate-950">

    {{-- NAVBAR CONTAINER --}}
    <header class="fixed top-0 left-0 right-0 z-50 pt-3 px-3 sm:px-6 lg:px-8 pointer-events-none"
            x-data="{
                scrolled: false,
                mobileMenu: false,
                activeTab: window.location.hash ? window.location.hash.replace('#', '') : ('{{ request()->routeIs('staffdesa.cek') ? 'cek-status' : 'beranda' }}'),
                isAutoScrolling: false
            }"
            x-init="
                scrolled = window.pageYOffset > 20;

                @if(request()->routeIs('warga.index'))
                    if (window.location.hash) {
                        isAutoScrolling = true;
                        setTimeout(() => { isAutoScrolling = false; }, 800);
                    }

                    const checkScroll = () => {
                        if (isAutoScrolling) return;
                        const scrollPos = window.scrollY + 200;
                        const tahapan = document.getElementById('tahapan-proses');
                        const realisasi = document.getElementById('dashboard-elektrifikasi');

                        if (realisasi && scrollPos >= realisasi.offsetTop) {
                            activeTab = 'realisasi';
                        } else if (tahapan && scrollPos >= tahapan.offsetTop) {
                            activeTab = 'tahapan-proses';
                        } else {
                            activeTab = 'beranda';
                        }
                    };

                    window.addEventListener('scroll', () => {
                        scrolled = window.pageYOffset > 20;
                        checkScroll();
                    }, { passive: true });
                @else
                    window.addEventListener('scroll', () => {
                        scrolled = window.pageYOffset > 20;
                    }, { passive: true });
                @endif
            ">

        <!-- Outer Pill Wrapper -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2 rounded-full flex items-center justify-between pointer-events-auto relative border border-slate-200/80 bg-white/50 backdrop-blur-md shadow-xs transition-shadow duration-300"
             :class="scrolled ? 'shadow-md shadow-slate-900/5' : ''">

            <!-- Sisi Kiri: Branding & Logo -->
            <a href="{{ route('warga.index') }}" class="flex items-center gap-2.5 sm:gap-3 select-none group">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <img src="{{ asset('images/logo-jambi.png') }}" alt="Logo Jambi" class="h-8 sm:h-9 w-auto object-contain">
                    <div class="h-4 sm:h-5 w-px bg-slate-300"></div>
                    <img src="{{ asset('images/logo-esdm.png') }}" alt="Logo ESDM" class="h-7 sm:h-8 w-auto object-contain">
                </div>
                <div class="flex flex-col justify-center">
                    <span class="font-extrabold text-sm sm:text-base tracking-tight leading-tight text-slate-900">
                        Dinas ESDM
                    </span>
                    <span class="text-[8px] sm:text-[9px] font-bold text-amber-500 uppercase tracking-widest leading-tight">
                        Provinsi Jambi
                    </span>
                </div>
            </a>

            <!-- Sisi Tengah: Menu Desktop & Laptop (Hidden di bawah lg) -->
            <nav class="hidden lg:flex items-center gap-1.5 text-xs font-bold">
                <!-- 1. Beranda -->
                <a href="{{ route('warga.index') }}"
                   @click="{{ request()->routeIs('warga.index') ? "event.preventDefault(); isAutoScrolling = true; activeTab = 'beranda'; window.scrollTo({top: 0, behavior: 'smooth'}); setTimeout(() => isAutoScrolling = false, 700);" : "" }}"
                   class="relative px-4 py-2 rounded-full inline-flex items-center justify-center text-center select-none cursor-pointer transition-colors duration-200"
                   :class="activeTab === 'beranda' && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                    <span class="absolute inset-0 rounded-full transition-opacity duration-200 -z-10 pointer-events-none"
                          :class="activeTab === 'beranda' && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'bg-slate-100 ring-1 ring-slate-200/80 opacity-100' : 'bg-transparent opacity-0'"></span>
                    <span>Beranda</span>
                </a>

                <!-- 2. Tahapan Proses -->
                <a href="{{ route('warga.index') }}#tahapan-proses"
                   @click="{{ request()->routeIs('warga.index') ? "event.preventDefault(); isAutoScrolling = true; activeTab = 'tahapan-proses'; document.getElementById('tahapan-proses')?.scrollIntoView({behavior: 'smooth'}); setTimeout(() => isAutoScrolling = false, 700);" : "" }}"
                   class="relative px-4 py-2 rounded-full inline-flex items-center justify-center text-center select-none cursor-pointer transition-colors duration-200"
                   :class="(activeTab === 'tahapan-proses' || activeTab === 'tahapan') && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                    <span class="absolute inset-0 rounded-full transition-opacity duration-200 -z-10 pointer-events-none"
                          :class="(activeTab === 'tahapan-proses' || activeTab === 'tahapan') && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'bg-slate-100 ring-1 ring-slate-200/80 opacity-100' : 'bg-transparent opacity-0'"></span>
                    <span>Tahapan Proses</span>
                </a>

                <!-- 3. Realisasi -->
                <a href="{{ route('warga.index') }}#dashboard-elektrifikasi"
                   @click="{{ request()->routeIs('warga.index') ? "event.preventDefault(); isAutoScrolling = true; activeTab = 'dashboard-elektrifikasi'; document.getElementById('dashboard-elektrifikasi')?.scrollIntoView({behavior: 'smooth'}); setTimeout(() => isAutoScrolling = false, 700);" : "" }}"
                   class="relative px-4 py-2 rounded-full inline-flex items-center justify-center text-center select-none cursor-pointer transition-colors duration-200"
                   :class="(activeTab === 'dashboard-elektrifikasi' || activeTab === 'realisasi') && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'text-slate-900' : 'text-slate-600 hover:text-slate-900'">
                    <span class="absolute inset-0 rounded-full transition-opacity duration-200 -z-10 pointer-events-none"
                          :class="(activeTab === 'dashboard-elektrifikasi' || activeTab === 'realisasi') && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'bg-slate-100 ring-1 ring-slate-200/80 opacity-100' : 'bg-transparent opacity-0'"></span>
                    <span>Realisasi</span>
                </a>
            </nav>

            <!-- Sisi Kanan: Login Desktop & Hamburger Mobile -->
            <div class="flex items-center gap-2">
                <!-- Tombol Login Petugas Desktop -->
                <a href="{{ url('/login') }}"
                   class="hidden sm:inline-flex items-center gap-2 px-4 sm:px-5 py-2 rounded-full bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs shadow-xs transition-colors duration-150">
                    <span>Login Petugas</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>

                <!-- Tombol Hamburger Mobile -->
                <button type="button"
                        @click="mobileMenu = !mobileMenu"
                        class="lg:hidden h-9 w-9 rounded-full inline-flex items-center justify-center text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none"
                        aria-label="Menu Utama">
                    <i class="fa-solid text-base transition-transform duration-200"
                       :class="mobileMenu ? 'fa-xmark scale-110 text-slate-900' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <!-- Menu Dropdown Mobile Floating (Tampil di Layar HP & Tablet) -->
        <div x-show="mobileMenu"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-3 scale-95"
             @click.outside="mobileMenu = false"
             class="lg:hidden max-w-7xl mx-auto mt-2 p-3.5 rounded-3xl border border-slate-200/80 bg-white/95 backdrop-blur-xl shadow-xl pointer-events-auto flex flex-col gap-1 text-sm font-bold">

            <!-- Mobile Nav Item: Beranda -->
            <a href="{{ route('warga.index') }}"
               @click="{{ request()->routeIs('warga.index') ? "event.preventDefault(); isAutoScrolling = true; activeTab = 'beranda'; mobileMenu = false; window.scrollTo({top: 0, behavior: 'smooth'}); setTimeout(() => isAutoScrolling = false, 700);" : "mobileMenu = false;" }}"
               class="px-4 py-2.5 rounded-2xl flex items-center justify-between transition-colors"
               :class="activeTab === 'beranda' && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'bg-slate-100 text-slate-950 ring-1 ring-slate-200/70' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-house text-xs text-amber-500 w-4 text-center"></i>
                    <span>Beranda</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </a>

            <!-- Mobile Nav Item: Tahapan Proses -->
            <a href="{{ route('warga.index') }}#tahapan-proses"
               @click="{{ request()->routeIs('warga.index') ? "event.preventDefault(); isAutoScrolling = true; activeTab = 'tahapan-proses'; mobileMenu = false; document.getElementById('tahapan-proses')?.scrollIntoView({behavior: 'smooth'}); setTimeout(() => isAutoScrolling = false, 700);" : "mobileMenu = false;" }}"
               class="px-4 py-2.5 rounded-2xl flex items-center justify-between transition-colors"
               :class="(activeTab === 'tahapan-proses' || activeTab === 'tahapan') && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'bg-slate-100 text-slate-950 ring-1 ring-slate-200/70' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-list-check text-xs text-amber-500 w-4 text-center"></i>
                    <span>Tahapan Proses</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </a>

            <!-- Mobile Nav Item: Realisasi -->
            <a href="{{ route('warga.index') }}#dashboard-elektrifikasi"
               @click="{{ request()->routeIs('warga.index') ? "event.preventDefault(); isAutoScrolling = true; activeTab = 'dashboard-elektrifikasi'; mobileMenu = false; document.getElementById('dashboard-elektrifikasi')?.scrollIntoView({behavior: 'smooth'}); setTimeout(() => isAutoScrolling = false, 700);" : "mobileMenu = false;" }}"
               class="px-4 py-2.5 rounded-2xl flex items-center justify-between transition-colors"
               :class="(activeTab === 'dashboard-elektrifikasi' || activeTab === 'realisasi') && {{ request()->routeIs('warga.index') ? 'true' : 'false' }} ? 'bg-slate-100 text-slate-950 ring-1 ring-slate-200/70' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-chart-pie text-xs text-amber-500 w-4 text-center"></i>
                    <span>Realisasi</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </a>

            <!-- Mobile Nav Item: Cek Status -->
            <a href="{{ route('staffdesa.cek') }}"
               @click="mobileMenu = false"
               class="px-4 py-2.5 rounded-2xl flex items-center justify-between transition-colors"
               :class="{{ request()->routeIs('staffdesa.cek') ? 'true' : 'false' }} ? 'bg-slate-100 text-slate-950 ring-1 ring-slate-200/70' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-magnifying-glass text-xs text-amber-500 w-4 text-center"></i>
                    <span>Cek Status</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400"></i>
            </a>

            <!-- Tombol Login Khusus Layar Kecil (< sm) -->
            <div class="pt-2 mt-1 border-t border-slate-100 sm:hidden">
                <a href="{{ url('/login') }}"
                   class="w-full py-2.5 px-4 rounded-xl bg-slate-900 text-white flex items-center justify-center gap-2 text-xs font-bold shadow-sm">
                    <span>Login Petugas</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </header>

    {{-- KONTEN UTAMA --}}
    <main class="mb-auto flex-1">
        <section class="pt-24 pb-16">
            @yield('content')
        </section>
    </main>

    {{-- FOOTER E-GOVERNMENT --}}
    <footer class="bg-slate-900 text-white border-t-4 border-amber-500 mt-16 pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Grid 4 Kolom -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pb-10 border-b border-slate-800">

                <!-- Kolom 1: Branding -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/logo-jambi.png') }}" alt="Logo Jambi" class="h-9 w-auto object-contain">
                            <div class="h-6 w-px bg-slate-600"></div>
                            <img src="{{ asset('images/logo-esdm.png') }}" alt="Logo ESDM" class="h-8 w-auto object-contain">
                        </div>
                        <div class="flex flex-col justify-center">
                            <span class="font-extrabold text-lg text-white tracking-tight leading-tight">Dinas ESDM</span>
                            <span class="text-[10px] font-bold text-amber-500 uppercase tracking-wider leading-tight">Provinsi Jambi</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed font-medium">
                        Elektronik Layanan Informasi Bantuan Listrik (E-LISTRIK) untuk pemerataan energi listrik dan bantuan pasang baru gratis bagi masyarakat kurang mampu di Provinsi Jambi.
                    </p>
                </div>

                <!-- Kolom 2: Portal Layanan -->
                <div class="space-y-3">
                    <h5 class="font-bold text-sm text-amber-400 uppercase tracking-wider">Portal Layanan</h5>
                    <ul class="space-y-2 text-xs text-slate-300 font-medium">
                        <li><a href="{{ route('login') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-amber-400"></i> Portal Login Perangkat Desa & ESDM</a></li>
                        <li><a href="{{ route('panduan.index') }}" class="hover:text-amber-400 transition flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[9px] text-amber-400"></i> Panduan Pelatihan Perangkat Desa</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Kontak Resmi -->
                <div class="space-y-3">
                    <h5 class="font-bold text-sm text-amber-400 uppercase tracking-wider">Kontak Instansi</h5>
                    <ul class="space-y-2 text-xs text-slate-300 font-medium">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-amber-400 mt-0.5"></i>
                            <span>Dinas Energi & Sumber Daya Mineral Provinsi Jambi (ESDM)</span>
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

            </div>

            <!-- Bagian Copyright -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4 font-medium">
                <p>&copy; {{ date('Y') }} Dinas Energi dan Sumber Daya Mineral Provinsi Jambi. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center gap-4">
                    <span class="hover:text-white transition cursor-pointer">Privasi & Ketentuan</span>
                    <span>•</span>
                    <span class="hover:text-white transition cursor-pointer">Bantuan Teknis</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
