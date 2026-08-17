<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Admin SIPELITA ESDM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full text-slate-800 antialiased selection:bg-amber-500 selection:text-slate-950" x-data="{ sidebarOpen: false }">

    <div class="min-h-full flex overflow-hidden">

        <!-- Off-canvas Mobile Sidebar Overlay -->
        <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-50 flex md:hidden" role="dialog" aria-modal="true">
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click.stop="sidebarOpen = false"></div>

            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full bg-slate-900 text-white" @click.stop>
                <div class="absolute top-0 right-0 -mr-12 pt-4">
                    <button type="button" @click.stop="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none text-white hover:bg-slate-800">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Mobile Sidebar Content -->
                <div class="h-20 flex items-center px-6 border-b border-slate-800 bg-slate-950">
                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg text-slate-950 font-extrabold mr-3">
                        <i class="fa-solid fa-bolt-lightning text-lg"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-base tracking-tight text-white block">SIPELITA ESDM</span>
                        <span class="text-[10px] text-amber-400 font-semibold uppercase tracking-wider block">Panel Verifikasi</span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                    @include('layouts.partials.sidebar-nav')
                </div>
            </div>
        </div>

        <!-- Desktop Persistent Sidebar -->
        <aside class="hidden md:flex md:w-72 md:flex-col bg-slate-900 text-slate-300 border-r border-slate-800 shrink-0">
            <!-- Sidebar Header / Branding -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800/80 bg-slate-950/60">
                <a href="{{ route('warga.index') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-md shadow-amber-500/20 text-slate-950 font-bold group-hover:scale-105 transition">
                        <i class="fa-solid fa-bolt-lightning text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-base text-white tracking-tight">SIPELITA</span>
                            <span class="px-1.5 py-0.2 bg-amber-500 text-slate-950 text-[10px] font-black rounded uppercase">ESDM</span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">Panel Layanan e-Gov</span>
                    </div>
                </a>
            </div>

            <!-- Role Badge Banner -->
            <div class="px-5 py-3 border-b border-slate-800/60 bg-slate-900/90">
                <div class="flex items-center gap-2.5">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></div>
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Sistem Terhubung</span>
                        <span class="text-xs font-bold text-white block truncate">
                            @if(auth()->user()->role === 'instansi')
                                Dinas ESDM (Instansi Pusat)
                            @else
                                Pemdes {{ auth()->user()->desa }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-5 space-y-1">
                @include('layouts.partials.sidebar-nav')
            </div>

            <!-- Footer / System Info -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40 text-[11px] text-slate-400">
                <p class="font-semibold text-slate-300">SPBE e-Government v2.0</p>
                <p>&copy; {{ date('Y') }} Dinas ESDM RI</p>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50/70 overflow-hidden">

            <!-- TOPBAR -->
            <header class="h-20 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-10 shadow-xs">
                <div class="flex items-center gap-3">
                    <!-- Mobile Sidebar Toggle -->
                    <button type="button" @click="sidebarOpen = true" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-blue-700 hover:bg-slate-100 transition">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>

                    <!-- Page Breadcrumb & Title Indicator -->
                    <div>
                        <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                            <span>Portal e-Gov</span>
                            <i class="fa-solid fa-chevron-right text-[9px]"></i>
                            <span class="text-slate-700 font-semibold">
                                @if(auth()->user()->role === 'kepala_desa')
                                    Verifikasi Kepala Desa
                                @else
                                    Dinas ESDM Provinsi
                                @endif
                            </span>
                        </div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                            @if(auth()->user()->role === 'kepala_desa')
                                Wilayah Desa {{ auth()->user()->desa }}
                            @else
                                Pengelolaan Bantuan Pasang Baru Listrik (BPBL)
                            @endif
                        </h1>
                    </div>
                </div>

                <!-- Right Topbar Controls (User Dropdown) -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('warga.index') }}" target="_blank" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-blue-700 hover:bg-slate-100 rounded-lg transition border border-slate-200">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Lihat Portal Publik
                    </a>

                    <!-- Profile Dropdown (AlpineJS) -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click.stop="open = !open" @click.away="open = false" type="button" class="flex items-center gap-2.5 p-1.5 pl-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 rounded-xl transition border border-slate-200/80">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-700 to-indigo-700 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block text-left pr-1">
                                <span class="block text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</span>
                                <span class="block text-[10px] text-slate-500 font-medium capitalize">
                                    @if(in_array(auth()->user()->role, ['super_admin', 'instansi']))
                                        Super Admin ESDM
                                    @elseif(auth()->user()->role === 'verifikator_esdm')
                                        Verifikator ESDM
                                    @else
                                        Kepala Desa
                                    @endif
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 px-1"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 py-2 divide-y divide-slate-100" @click.stop>
                            <div class="px-4 py-2.5">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                <span class="mt-1.5 inline-block px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-md border border-blue-200">
                                    Role: {{ in_array(auth()->user()->role, ['super_admin', 'instansi']) ? 'Super Admin ESDM' : (auth()->user()->role === 'verifikator_esdm' ? 'Verifikator ESDM' : 'Kepala Desa') }}
                                </span>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition flex items-center gap-2.5">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        Keluar Dari Panel
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN SCROLLABLE CONTENT AREA -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    @yield('content')
                </div>

                <footer class="mt-16 text-center text-xs text-slate-400 py-6 border-t border-slate-200/60">
                    &copy; {{ date('Y') }} Sistem Pelayanan & Elektrifikasi Listrik Terpadu (SIPELITA) — Dinas Energi & Sumber Daya Mineral
                </footer>
            </main>

        </div>

    </div>

    @stack('scripts')
</body>
</html>

