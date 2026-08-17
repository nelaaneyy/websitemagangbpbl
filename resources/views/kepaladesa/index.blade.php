@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    <!-- Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/60">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Dashboard Kades {{ auth()->user()->desa }}</h1>
            <p class="text-xs text-slate-500 font-medium">Verifikasi awal & rekomendasi usulan BPBL warga desa.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('kepaladesa.lisdes.create') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-extrabold rounded-xl transition shadow-xs flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-xs"></i> Usulkan Lisdes Dusun
            </a>
        </div>
    </div>

    <!-- Stat Cards Ringkas (4 Metrics) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Warga</span>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block">Menunggu Kades</span>
                <p class="text-2xl font-extrabold text-amber-900 mt-0.5">{{ number_format($stats['menunggu']) }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Disetujui Kades</span>
                <p class="text-2xl font-extrabold text-emerald-900 mt-0.5">{{ number_format($stats['disetujui']) }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-rose-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Ditolak / Revisi</span>
                <p class="text-2xl font-extrabold text-rose-900 mt-0.5">{{ number_format($stats['ditolak']) }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>
    </div>

    <!-- Minimal Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-2xs">
        <form method="GET" action="{{ route('kepaladesa.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-center">
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK atau Nama warga..." 
                       class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-600">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-600">
                <option value="">Semua Tahapan Status</option>
                <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim (Menunggu Kades)</option>
                <option value="menunggu_verifikasi_pusat" {{ request('status') == 'menunggu_verifikasi_pusat' ? 'selected' : '' }}>Menunggu ESDM</option>
                <option value="lolos_verifikasi_pusat" {{ request('status') == 'lolos_verifikasi_pusat' ? 'selected' : '' }}>Lolos Verifikasi (Approved)</option>
                <option value="ditolak/perlu_perbaikan" {{ request('status') == 'ditolak/perlu_perbaikan' ? 'selected' : '' }}>Ditolak / Perbaikan</option>
            </select>

            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                Cari & Filter
            </button>

            @if(request('search') || request('status'))
                <a href="{{ route('kepaladesa.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    @if (session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-900 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Data Table (Clean Minimalist) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/80 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3">Nama Warga & NIK</th>
                        <th class="px-4 py-3">RT / RW</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($wargas as $warga)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3">
                                <span class="block text-slate-900 font-bold text-xs">{{ $warga->nama }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">NIK: {{ $warga->nik }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded-md font-bold text-xs">
                                    RT {{ $warga->rt_rw ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $warga->created_at ? $warga->created_at->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($warga->status_verifikasi === 'terkirim')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-clock text-amber-600 animate-pulse text-[10px]"></i> Menunggu Kades
                                    </span>
                                @elseif($warga->status_verifikasi === 'menunggu_verifikasi_pusat')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-building text-blue-600 text-[10px]"></i> Menunggu ESDM
                                    </span>
                                @elseif($warga->status_verifikasi === 'lolos_verifikasi_pusat')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i> Disetujui ESDM
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-rose-50 text-rose-800 border border-rose-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-circle-xmark text-rose-600 text-[10px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('kepaladesa.show', $warga) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-clipboard-check text-xs"></i>
                                    Verifikasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400 font-medium text-xs">
                                <i class="fa-solid fa-inbox text-2xl mb-1 text-slate-300 block"></i>
                                Belum ada permohonan warga terdaftar untuk desa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
            {{ $wargas->links() }}
        </div>
    </div>
</div>
@endsection


