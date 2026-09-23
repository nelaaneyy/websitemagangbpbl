@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{
    selectAll: false,
    selected: [],
    toggleAll() {
        if (this.selectAll) {
            this.selected = Array.from(document.querySelectorAll('.warga-checkbox')).map(cb => cb.value);
        } else {
            this.selected = [];
        }
    }
}">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-900 text-[10px] font-extrabold uppercase tracking-wider rounded-md border border-amber-200">
                    <i class="fa-solid fa-shield-halved text-amber-600 mr-1"></i> Panel SuperAdmin & ESDM
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Validasi Realisasi Data Historis</h1>
            <p class="text-xs text-slate-500 font-medium">Verifikasi NIK hasil import data lama yang tidak memiliki tanda centang realisasi di berkas Excel/CSV.</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3.5 py-2 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs font-bold flex items-center gap-2 shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-rose-600 animate-pulse"></i>
                <span>{{ number_format($totalPendingCount) }} Data Butuh Validasi</span>
            </span>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200/90 rounded-2xl text-xs font-bold text-emerald-900 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xs font-black shadow-xs">
                    <i class="fa-solid fa-check"></i>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- Filter & Batch Action Header -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-4">
        <form method="GET" action="{{ route('dinasesdm.validasi_realisasi.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari NIK / Nama / Alamat</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik NIK atau Nama..."
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Kecamatan</label>
                <select name="kecamatan" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $kec)
                        <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Desa / Kelurahan</label>
                <select name="desa" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="">Semua Desa</option>
                    @foreach($desas as $des)
                        <option value="{{ $des }}" {{ request('desa') == $des ? 'selected' : '' }}>{{ $des }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'kecamatan', 'desa']))
                    <a href="{{ route('dinasesdm.validasi_realisasi.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>

        <!-- Batch Action Bar (Terpilih > 0) -->
        <div x-show="selected.length > 0" x-cloak x-transition
             class="p-3 bg-amber-50 border border-amber-200/90 rounded-2xl flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-amber-500 text-slate-950 font-black text-xs flex items-center justify-center shadow-xs" x-text="selected.length"></span>
                <span class="font-extrabold text-amber-950">Data Dipilih untuk Konfirmasi Massal</span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Batch Form 1: Sudah Direalisasi -->
                <form method="POST" action="{{ route('dinasesdm.validasi_realisasi.bulk_confirm') }}" onsubmit="return confirm('Apakah Anda yakin menandai ' + selected.length + ' data terpilih sebagai SUDAH DIREALISASI (Terpasang)?')">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="warga_ids[]" :value="id">
                    </template>
                    <input type="hidden" name="keputusan" value="sudah_realisasi">
                    <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl text-xs transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Konfirmasi Massal (Sudah Realisasi)</span>
                    </button>
                </form>

                <!-- Batch Form 2: Belum Direalisasi / Usulan -->
                <form method="POST" action="{{ route('dinasesdm.validasi_realisasi.bulk_confirm') }}" onsubmit="return confirm('Apakah Anda yakin menandai ' + selected.length + ' data terpilih sebagai BELUM DIREALISASI (Hanya Usulan Lolos)?')">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="warga_ids[]" :value="id">
                    </template>
                    <input type="hidden" name="keputusan" value="belum_realisasi">
                    <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl text-xs transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Tandai Belum Realisasi (Usulan)</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-500 tracking-wider">
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="py-3.5 px-4">Informasi Warga & NIK</th>
                        <th class="py-3.5 px-4">Wilayah & Alamat</th>
                        <th class="py-3.5 px-4 text-center">Tahun Usulan</th>
                        <th class="py-3.5 px-4">Status Realisasi Import</th>
                        <th class="py-3.5 px-4 text-right">Aksi Konfirmasi SuperAdmin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($wargas as $w)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-4 text-center">
                                <input type="checkbox" value="{{ $w->id }}" x-model="selected" class="warga-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            </td>

                            <td class="py-3.5 px-4 font-semibold">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $w->nama }}</div>
                                <div class="text-[11px] font-mono text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <i class="fa-regular fa-id-card text-blue-600"></i>
                                    <span>NIK: {{ $w->nik }}</span>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 font-medium">
                                <div class="text-slate-900 font-bold">{{ $w->desa }}, {{ $w->kecamatan }}</div>
                                <div class="text-slate-500 text-[11px] truncate max-w-xs mt-0.5">{{ $w->alamat }}</div>
                            </td>

                            <td class="py-3.5 px-4 text-center font-bold">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-[11px]">
                                    {{ $w->tahun_usulan ?: date('Y', strtotime($w->created_at)) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 border border-rose-200 text-rose-700 font-extrabold rounded-lg text-[11px]">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    <span>Tanpa Centang Realisasi</span>
                                </span>
                                @if($w->keterangan_import)
                                    <p class="text-[10px] text-slate-400 font-medium italic mt-1 max-w-xs">"{{ $w->keterangan_import }}"</p>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Single Form: Konfirmasi Sudah Realisasi -->
                                    <form method="POST" action="{{ route('dinasesdm.validasi_realisasi.confirm', $w->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="keputusan" value="sudah_realisasi">
                                        <button type="submit" onclick="return confirm('Konfirmasi NIK {{ $w->nik }} SUDAH DIREALISASI (Terpasang)?')"
                                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-[11px] transition shadow-2xs flex items-center gap-1 cursor-pointer"
                                                title="Setujui sebagai Terpasang / Sudah Realisasi">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span>Sudah Realisasi</span>
                                        </button>
                                    </form>

                                    <!-- Single Form: Tandai Belum Realisasi -->
                                    <form method="POST" action="{{ route('dinasesdm.validasi_realisasi.confirm', $w->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="keputusan" value="belum_realisasi">
                                        <button type="submit" onclick="return confirm('Tandai NIK {{ $w->nik }} BELUM DIREALISASI (Tersimpan sebagai Usulan Lolos)?')"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-xl text-[11px] transition flex items-center gap-1 cursor-pointer"
                                                title="Tandai Belum Realisasi / Usulan Baru">
                                            <i class="fa-solid fa-xmark text-[10px]"></i>
                                            <span>Belum Realisasi</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3 text-xl">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <p class="text-sm font-bold text-slate-700">Tidak ada antrean validasi realisasi!</p>
                                <p class="text-xs text-slate-400 mt-1">Semua data lama dari import Excel telah dikonfirmasi atau memiliki status realisasi yang jelas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wargas->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $wargas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
