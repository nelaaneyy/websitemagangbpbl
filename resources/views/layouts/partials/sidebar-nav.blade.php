<div class="space-y-6">
    <!-- Menu Utama -->
    <div>
        <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Navigasi Utama</p>

        @if(in_array(auth()->user()->role, ['instansi', 'super_admin', 'verifikator_esdm']))
            <div class="space-y-1">
                <a href="{{ route('dinasesdm.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('dinasesdm.index') || request()->routeIs('dinasesdm.show') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-list-check text-sm w-5 text-center"></i>
                    <span>Dasbor Verifikasi Warga</span>
                </a>

                <a href="{{ route('dinasesdm.lisdes.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('dinasesdm.lisdes.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-tower-cell text-sm w-5 text-center"></i>
                    <span>Usulan Listrik Desa</span>
                </a>

                <a href="{{ route('dinasesdm.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('dinasesdm.users.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear text-sm w-5 text-center"></i>
                    <span>Verifikasi Akun Desa</span>
                </a>

                <a href="{{ route('dinasesdm.desa.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('dinasesdm.desa.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-map-location-dot text-sm w-5 text-center"></i>
                    <span>Peta Rasio Elektrifikasi</span>
                </a>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800 space-y-1">
                <p class="px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Laporan & Ekspor</p>

                <button type="button" onclick="typeof openExportModal === 'function' ? openExportModal('excel') : window.location.href='{{ route('dinasesdm.export.excel') }}'" 
                        class="w-full text-left flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-emerald-400 hover:bg-emerald-500/10 transition cursor-pointer">
                    <i class="fa-solid fa-file-excel text-sm w-5 text-center"></i>
                    <span>Ekspor Format Excel</span>
                </button>

                <button type="button" onclick="typeof openExportModal === 'function' ? openExportModal('pdf') : window.open('{{ route('dinasesdm.export.pdf') }}', '_blank')" 
                        class="w-full text-left flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-500/10 transition cursor-pointer">
                    <i class="fa-solid fa-file-pdf text-sm w-5 text-center"></i>
                    <span>Cetak Laporan PDF</span>
                </button>
            </div>

        @elseif(auth()->user()->role === 'kepala_desa')
            <div class="space-y-1">
                <a href="{{ route('kepaladesa.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('kepaladesa.index') || request()->routeIs('kepaladesa.show') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-list-check text-sm w-5 text-center"></i>
                    <span>Verifikasi Warga Desa</span>
                </a>

                <a href="{{ route('kepaladesa.lisdes.create') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold transition-all {{ request()->routeIs('kepaladesa.lisdes.*') ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-paper-plane text-sm w-5 text-center"></i>
                    <span>Ajukan Jaringan Lisdes</span>
                </a>
            </div>
        @endif

        <div class="mt-6 pt-4 border-t border-slate-800 space-y-1">
            <a href="{{ route('panduan.index') }}" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-amber-400 hover:bg-amber-500/10 transition">
                <i class="fa-solid fa-graduation-cap text-sm w-5 text-center"></i>
                <span>Panduan Pelatihan Desa</span>
            </a>
        </div>
    </div>
</div>
