@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ rejectModalOpen: false, activeLisdes: null, activeLisdesDesa: '', activeActionUrl: '' }">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-800 text-[10px] font-extrabold rounded-md uppercase">Program Infrastruktur</span>
                <span class="text-xs text-slate-400 font-semibold">Dinas ESDM</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Verifikasi Pengajuan Listrik Desa (Lisdes)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Peninjauan dan penetapan kelayakan usulan perluasan jaringan listrik desa dari Kepala Desa.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-900 flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Total Usulan</span>
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-bolt"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-slate-400">Pengajuan Lisdes Masuk</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-amber-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-amber-700 uppercase tracking-wider">Menunggu Review</span>
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-amber-900 tracking-tight">{{ $stats['menunggu_verifikasi'] }}</p>
            <p class="text-[11px] text-amber-600 font-medium">Perlu Peninjauan ESDM</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-emerald-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider">Disetujui ESDM</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-emerald-900 tracking-tight">{{ $stats['disetujui'] }}</p>
            <p class="text-[11px] text-emerald-600 font-medium">Usulan Lolos Verifikasi</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-rose-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-rose-700 uppercase tracking-wider">Ditolak / Revisi</span>
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-rose-900 tracking-tight">{{ $stats['ditolak'] }}</p>
            <p class="text-[11px] text-rose-600 font-medium">Dikembalikan Ke Desa</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('dinasesdm.lisdes.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Dusun, Nama Desa, atau Nama Kades..."
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <div class="w-full md:w-56">
                <select name="status" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                    <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                    <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui ESDM</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak / Perbaikan</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                    Filter
                </button>
                @if(request('search') || (request('status') && request('status') !== 'all'))
                    <a href="{{ route('dinasesdm.lisdes.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Pengajuan Lisdes -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-semibold">
                <thead class="bg-slate-900 text-white uppercase font-extrabold tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Desa & Pengaju</th>
                        <th class="px-5 py-4">Wilayah Usulan (Dusun)</th>
                        <th class="px-5 py-4 text-center">Sasaran KK</th>
                        <th class="px-5 py-4 text-center">Jarak ke PLN</th>
                        <th class="px-5 py-4">Tanggal Pengajuan</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center">Aksi ESDM</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lisdesList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $item->desa }}</div>
                                <div class="text-[11px] text-slate-400 font-medium">Kades: {{ $item->user->name ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-slate-800">{{ $item->nama_dusun }}</div>
                                @if($item->latitude && $item->longitude)
                                    <div class="text-[11px] font-mono text-blue-600 mt-0.5">
                                        <i class="fa-solid fa-location-dot"></i> {{ $item->latitude }}, {{ $item->longitude }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center font-extrabold text-slate-900">
                                {{ number_format($item->jumlah_kk) }} KK
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-slate-700">
                                {{ number_format($item->estimasi_jarak) }} m
                            </td>
                            <td class="px-5 py-4 text-[11px] text-slate-500 whitespace-nowrap">
                                {{ $item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @if($item->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 text-[11px] font-extrabold rounded-full border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui
                                    </span>
                                @elseif($item->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-800 text-[11px] font-extrabold rounded-full border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-800 text-[11px] font-extrabold rounded-full border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu ESDM
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('dinasesdm.lisdes.show', $item->id) }}"
                                       class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>

                                    @if($item->status === 'menunggu_verifikasi' || $item->status === 'ditolak')
                                        <form action="{{ route('dinasesdm.lisdes.approve', $item->id) }}" method="POST" onsubmit="return confirm('Setujui pengajuan Lisdes ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition shadow-xs flex items-center gap-1">
                                                <i class="fa-solid fa-check"></i> Setujui
                                            </button>
                                        </form>
                                    @endif

                                    @if($item->status === 'menunggu_verifikasi' || $item->status === 'disetujui')
                                        <button type="button"
                                                @click.stop="rejectModalOpen = true; activeLisdesDesa = '{{ $item->desa }} ({{ $item->nama_dusun }})'; activeActionUrl = '{{ route('dinasesdm.lisdes.reject', $item->id) }}'"
                                                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition flex items-center gap-1">
                                            <i class="fa-solid fa-xmark"></i> Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-bolt text-3xl mb-2 text-slate-300 block"></i>
                                <p class="font-bold text-slate-700">Belum ada data pengajuan Lisdes yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lisdesList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $lisdesList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Tolak Pengajuan Lisdes -->
    <div x-cloak
         x-show="rejectModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @click.stop="rejectModalOpen = false">

        <div @click.stop class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-6 sm:p-8 space-y-4 border border-slate-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-circle-xmark text-rose-600"></i> Tolak Pengajuan Lisdes
                </h3>
                <button @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <p class="text-xs text-slate-600 font-medium">
                Menolak usulan Lisdes dari <strong x-text="activeLisdesDesa" class="text-slate-900"></strong>. Masukkan alasan penolakan:
            </p>

            <form :action="activeActionUrl" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="space-y-1">
                    <label for="catatan_esdm" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Catatan Penolakan / Perbaikan
                    </label>
                    <textarea id="catatan_esdm" name="catatan_esdm" rows="4" required
                              placeholder="Jelaskan alasan penolakan atau dokumen yang kurang..."
                              class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex justify-end gap-2.5 pt-2">
                    <button type="button" @click="rejectModalOpen = false"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md transition">
                        Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

