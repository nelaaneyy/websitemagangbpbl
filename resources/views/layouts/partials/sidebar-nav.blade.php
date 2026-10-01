<div class="space-y-6 text-slate-300">

    {{-- ========================================== --}}
    {{-- 1. AKSES UNTUK INSTANSI / SUPER ADMIN      --}}
    {{-- ========================================== --}}
    @if(in_array(auth()->user()->role, ['instansi', 'super_admin']))
        <div class="space-y-4">
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center justify-between">
                    <span>Executive Analytics</span>
                    <i class="fa-solid fa-chart-line text-[10px]"></i>
                </p>
                <div class="space-y-1">
                    <a href="{{ route('admin.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.index') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('admin.index'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-chart-pie text-sm w-4 text-center {{ request()->routeIs('admin.index') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Dasbor Analisis & KPI</span>
                    </a>

                    <a href="{{ route('admin.datalist') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.datalist') || request()->routeIs('admin.show') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('admin.datalist') || request()->routeIs('admin.show'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-table-list text-sm w-4 text-center {{ request()->routeIs('admin.datalist') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Permohonan BPBL</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center justify-between">
                    <span>Infrastruktur & Akun</span>
                    <i class="fa-solid fa-sliders text-[10px]"></i>
                </p>
                <div class="space-y-1">
                    <a href="{{ route('admin.lisdes.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.lisdes.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('admin.lisdes.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-tower-cell text-sm w-4 text-center {{ request()->routeIs('admin.lisdes.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Usulan Listrik Desa</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.users.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('admin.users.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-users-gear text-sm w-4 text-center {{ request()->routeIs('admin.users.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Verifikasi Akun Desa</span>
                    </a>

                    @php
                        $pendingValidasiCount = \App\Models\Warga::butuhValidasiRealisasi()->count();
                    @endphp
                    <a href="{{ route('admin.validasi_realisasi.index') }}"
                       class="group relative flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.validasi_realisasi.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('admin.validasi_realisasi.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clipboard-check text-sm w-4 text-center {{ request()->routeIs('admin.validasi_realisasi.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                            <span>Validasi Realisasi</span>
                        </div>
                        @if($pendingValidasiCount > 0)
                            <span class="px-2 py-0.5 bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-bold rounded-md">
                                {{ $pendingValidasiCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.historis.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.historis.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('admin.historis.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-clock-rotate-left text-sm w-4 text-center {{ request()->routeIs('admin.historis.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Arsip Historis</span>
                    </a>

                    <a href="{{ route('admin.audit_logs.index') }}"
                        class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('admin.audit_logs.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('admin.audit_logs.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-shield-halved text-sm w-4 text-center {{ request()->routeIs('admin.audit_logs.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Log Aktivitas & Audit</span>
                    </a>

                </div>
            </div>


            <div class="pt-3 border-t border-slate-800/80 space-y-1">
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pelaporan</p>

                <button type="button" onclick="typeof openExportModal === 'function' ? openExportModal('excel') : window.location.href='{{ route('admin.export.excel') }}'"
                        class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-emerald-500/10 hover:text-emerald-400 transition cursor-pointer">
                    <i class="fa-solid fa-file-excel text-sm w-4 text-center text-emerald-500/80"></i>
                    <span>Ekspor Excel</span>
                </button>

                <button type="button" onclick="typeof openExportModal === 'function' ? openExportModal('pdf') : window.open('{{ route('admin.export.pdf') }}', '_blank')"
                        class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-rose-500/10 hover:text-rose-400 transition cursor-pointer">
                    <i class="fa-solid fa-file-pdf text-sm w-4 text-center text-rose-500/80"></i>
                    <span>Ekspor PDF</span>
                </button>
            </div>
        </div>

    {{-- ========================================== --}}
    {{-- 2. AKSES KHUSUS VERIFIKATOR ESDM           --}}
    {{-- ========================================== --}}
    @elseif(auth()->user()->role === 'verifikator_esdm')
        <div class="space-y-4">
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center justify-between">
                    <span>Executive Analytics</span>
                    <i class="fa-solid fa-chart-line text-[10px]"></i>
                </p>
                <div class="space-y-1">
                    <a href="{{ route('verifikator.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('verifikator.index') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('verifikator.index'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-chart-pie text-sm w-4 text-center {{ request()->routeIs('verifikator.index') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Dasbor Analisis & KPI</span>
                    </a>

                    <a href="{{ route(auth()->user()->role === 'verifikator_esdm' ? 'verifikator.datalist' : 'admin.datalist') }}"                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('verifikator.datalist', 'admin.datalist') || request()->routeIs('verifikator.show', 'admin.show') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('verifikator.datalist', 'admin.datalist') || request()->routeIs('verifikator.show', 'admin.show'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-table-list text-sm w-4 text-center {{ request()->routeIs('verifikator.datalist', 'admin.datalist') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Verifikasi Permohonan</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center justify-between">
                    <span>Infrastruktur & Akun</span>
                    <i class="fa-solid fa-sliders text-[10px]"></i>
                </p>
                <div class="space-y-1">
                    <a href="{{ route('verifikator.lisdes.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('verifikator.lisdes.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('verifikator.lisdes.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-tower-cell text-sm w-4 text-center {{ request()->routeIs('verifikator.lisdes.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Usulan Listrik Desa</span>
                    </a>

                    <a href="{{ route('verifikator.users.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('verifikator.users.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('verifikator.users.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-users-gear text-sm w-4 text-center {{ request()->routeIs('verifikator.users.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Verifikasi Akun Desa</span>
                    </a>

                    @php
                        $pendingValidasiCount = \App\Models\Warga::butuhValidasiRealisasi()->count();
                    @endphp
                    <a href="{{ route('verifikator.validasi_realisasi.index') }}"
                       class="group relative flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('verifikator.validasi_realisasi.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('verifikator.validasi_realisasi.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clipboard-check text-sm w-4 text-center {{ request()->routeIs('verifikator.validasi_realisasi.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                            <span>Validasi Realisasi</span>
                        </div>
                        @if($pendingValidasiCount > 0)
                            <span class="px-2 py-0.5 bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-bold rounded-md">
                                {{ $pendingValidasiCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('verifikator.historis.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('verifikator.historis.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('verifikator.historis.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-clock-rotate-left text-sm w-4 text-center {{ request()->routeIs('verifikator.historis.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Arsip Historis</span>
                    </a>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800/80 space-y-1">
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pelaporan</p>

                <button type="button" onclick="typeof openExportModal === 'function' ? openExportModal('excel') : window.location.href='{{ route('verifikator.export.excel') }}'"
                        class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-emerald-500/10 hover:text-emerald-400 transition cursor-pointer">
                    <i class="fa-solid fa-file-excel text-sm w-4 text-center text-emerald-500/80"></i>
                    <span>Ekspor Excel</span>
                </button>

                <button type="button" onclick="typeof openExportModal === 'function' ? openExportModal('pdf') : window.open('{{ route('verifikator.export.pdf') }}', '_blank')"
                        class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-rose-500/10 hover:text-rose-400 transition cursor-pointer">
                    <i class="fa-solid fa-file-pdf text-sm w-4 text-center text-rose-500/80"></i>
                    <span>Ekspor PDF</span>
                </button>
            </div>
        </div>

    {{-- ========================================== --}}
    {{-- 3. AKSES KHUSUS KEPALA DESA               --}}
    {{-- ========================================== --}}
    @elseif(auth()->user()->role === 'kepala_desa')
        <div class="space-y-4">
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center justify-between">
                    <span>Otorisasi Desa</span>
                    <i class="fa-solid fa-stamp text-[10px]"></i>
                </p>

                <div class="space-y-1">
                    <a href="{{ route('kepaladesa.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('kepaladesa.index') || request()->routeIs('kepaladesa.show') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('kepaladesa.index') || request()->routeIs('kepaladesa.show'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-file-signature text-sm w-4 text-center {{ request()->routeIs('kepaladesa.index') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Persetujuan Batch</span>
                    </a>

                    <a href="{{ route('kepaladesa.lisdes.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('kepaladesa.lisdes.*') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('kepaladesa.lisdes.*'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-tower-cell text-sm w-4 text-center {{ request()->routeIs('kepaladesa.lisdes.*') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Usulan Listrik Desa</span>
                    </a>
                </div>
            </div>
        </div>

    {{-- ========================================== --}}
    {{-- 4. AKSES KHUSUS STAFF DESA                --}}
    {{-- ========================================== --}}
    @elseif(in_array(auth()->user()->role, ['staff_desa', 'staf_desa']))
        <div class="space-y-4">
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center justify-between">
                    <span>Operasional Usulan</span>
                    <i class="fa-solid fa-list-check text-[10px]"></i>
                </p>

                <div class="space-y-1">
                    <a href="{{ route('staffdesa.index') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('staffdesa.index') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('staffdesa.index'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-layer-group text-sm w-4 text-center {{ request()->routeIs('staffdesa.index') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Manajemen Usulan BPBL</span>
                    </a>

                    <a href="{{ route('staffdesa.pengajuan') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('warga.pengajuan') || request()->routeIs('staffdesa.pengajuan') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('warga.pengajuan') || request()->routeIs('staffdesa.pengajuan'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-user-plus text-sm w-4 text-center {{ request()->routeIs('staffdesa.pengajuan') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Input Pendaftaran Warga</span>
                    </a>

                    <a href="{{ route('staffdesa.cek') }}"
                       class="group relative flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-150 {{ request()->routeIs('warga.search') || request()->routeIs('staffdesa.cek') ? 'bg-slate-800/80 text-white font-semibold border border-slate-700/60 shadow-xs' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
                        @if(request()->routeIs('warga.search') || request()->routeIs('staffdesa.cek'))
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-4 bg-amber-400 rounded-r-full"></span>
                        @endif
                        <i class="fa-solid fa-magnifying-glass text-sm w-4 text-center {{ request()->routeIs('staffdesa.cek') ? 'text-amber-400' : 'text-slate-400 group-hover:text-slate-300' }}"></i>
                        <span>Cek Status NIK</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- PANDUAN PELATIHAN (FOOTER LINK)            --}}
    {{-- ========================================== --}}
    <div class="pt-3 border-t border-slate-800/80">
        <a href="{{ route('panduan.index') }}" target="_blank"
           class="group flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-slate-800/40 hover:text-amber-300 transition-all duration-150">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-graduation-cap text-sm w-4 text-center text-slate-400 group-hover:text-amber-400"></i>
                <span>Panduan & SOP</span>
            </div>
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-500 group-hover:text-amber-300"></i>
        </a>
    </div>

</div>
