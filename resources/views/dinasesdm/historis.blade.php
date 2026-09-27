@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span
                    class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase tracking-wider rounded-md border border-blue-200">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600 mr-1"></i> Data Historis & Rekapitulasi
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Arsip & Data Historis BPBL</h1>
            <p class="text-xs text-slate-500 font-medium">Rekapitulasi penerima bantuan pasang baru listrik (BPBL) dari
                tahun-tahun sebelumnya.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openImportModal()"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer">
                <i class="fa-solid fa-file-import"></i>
                <span>Import Excel Data Lama</span>
            </button>

            <button type="button" onclick="openExportModal('excel', 'historis')"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer">
                <i class="fa-solid fa-file-excel"></i>
                <span>Ekspor Excel</span>
            </button>

            <button type="button" onclick="openExportModal('pdf', 'historis')"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Ekspor PDF</span>
            </button>
        </div>
    </div>

    @if (session('success'))
    <div
        class="p-4 bg-emerald-50 border border-emerald-200/90 rounded-2xl text-xs font-bold text-emerald-900 flex items-center justify-between shadow-2xs">
        <div class="flex items-center gap-2.5">
            <div
                class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xs font-black shadow-xs">
                <i class="fa-solid fa-check"></i>
            </div>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()"
            class="text-emerald-700 hover:text-emerald-950 font-bold">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Total
                        Data Historis</span>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalHistoris) }}
                    </h3>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-box-archive"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-emerald-200/80 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <span
                        class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-wider block mb-1">Teraliri
                        / Terpasang Real</span>
                    <h3 class="text-2xl font-black text-emerald-950 tracking-tight">{{ number_format($totalTerpasang) }}
                    </h3>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-purple-200/80 shadow-2xs">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-purple-700 uppercase tracking-wider block mb-1">Rentang
                        Tahun Terekam</span>
                    <h3 class="text-2xl font-black text-purple-950 tracking-tight">
                        {{ isset($allYears) && $allYears->isNotEmpty() ? $allYears->min() . ' - ' . $allYears->max() : 'Semua' }}                    </h3>
                </div>
                <div
                    class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs">
        <form method="GET" action="{{ route('dinasesdm.historis.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari NIK / Nama / Alamat</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="NIK atau Nama..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Tahun Usulan / Pengajuan</label>
                <select name="tahun" onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-blue-900 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="">Semua Tahun</option>
                    @foreach($allYears as $y)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Kecamatan</label>
                <select name="kecamatan" onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $kec)
                    <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Desa / Kelurahan</label>
                <select name="desa" onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="">Semua Desa</option>
                    @foreach($desas as $des)
                    <option value="{{ $des }}" {{ request('desa') == $des ? 'selected' : '' }}>{{ $des }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Kabupaten / Kota</label>
                <select name="kabupaten" onchange="this.form.submit()"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white transition">
                    <option value="">Semua Kabupaten</option>
                    @foreach($kabupatens as $kab)
                    <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>{{ $kab }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-filter"></i>
                    <span>Terapkan Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'tahun', 'kecamatan', 'desa']))
                <a href="{{ route('dinasesdm.historis.index') }}"
                    class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition"
                    title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Batch-Based Arsip Cards (Daftar Batch Pengajuan per Desa per Tahun) -->
    {{-- @if(isset($batches) && $batches->count() > 0)
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-folder-tree text-amber-600"></i>
                Arsip Batch Usulan per Desa per Tahun
                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-black rounded-lg border border-amber-200">{{ $batches->count() }} Batch</span>
            </h3>
        </div>

        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($batches as $batch)
                <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-200/80 rounded-2xl p-5 hover:shadow-md transition-all duration-200 group relative overflow-hidden">
                    <!-- Year Badge -->
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 bg-indigo-100 text-indigo-800 border border-indigo-200 font-black text-[11px] rounded-lg shadow-2xs">
                            <i class="fa-solid fa-calendar-day text-indigo-600 mr-0.5"></i> {{ $batch->tahun_anggaran }}
                        </span>
                    </div>

                    <!-- Batch Header -->
                    <div class="mb-3">
                        <h4 class="text-sm font-extrabold text-slate-900 leading-tight">
                            <i class="fa-solid fa-building-flag text-blue-600 mr-1"></i> Desa {{ $batch->desa }}
                        </h4>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                            Kec. {{ $batch->user?->kecamatan ?? ($batch->user?->desa ?? '-') }}, {{ $batch->user?->kabupaten ?? '-' }}
                        </p>                        
                        <p class="text-[10px] text-slate-400 font-mono mt-1">
                            {{ $batch->kode_batch }}
                        </p>
                    </div>

                    <!-- Warga Counter -->
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex-1 bg-blue-50 border border-blue-100 rounded-xl px-3 py-2">
                            <div class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Jumlah Warga</div>
                            <div class="text-lg font-black text-blue-900">{{ $batch->wargas_count ?? $batch->total_warga }}<span class="text-xs font-bold text-blue-400"> Warga</span></div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="flex-1">
                            <div class="w-full bg-slate-200 rounded-full h-2.5 mb-1.5">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full transition-all" style="width: {{ min(100, ($batch->wargas_count ?? $batch->total_warga)) }}%"></div>
                            </div>
                            <div class="text-[10px] text-slate-500 font-bold text-center">{{ min(100, ($batch->wargas_count ?? $batch->total_warga)) }}% Terisi</div>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-3">
                        @php
                            $statusColors = [
                                'draft_staff' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'dikirim_ke_kades' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'diverifikasi_kades' => 'bg-sky-50 text-sky-800 border-sky-200',
                                'diajukan_ke_esdm' => 'bg-blue-50 text-blue-800 border-blue-200',
                                'disetujui_esdm' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                            ];
                            $statusColor = $statusColors[$batch->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 {{ $statusColor }} border text-[10px] font-bold rounded-lg">
                            @if($batch->status === 'disetujui_esdm')
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            @elseif($batch->status === 'diajukan_ke_esdm')
                                <i class="fa-solid fa-paper-plane text-blue-600"></i>
                            @elseif($batch->status === 'ditolak')
                                <i class="fa-solid fa-circle-xmark text-rose-600"></i>
                            @else
                                <i class="fa-solid fa-clock text-slate-500"></i>
                            @endif
                            {{ $batch->status_label }}
                        </span>

                        @if($batch->sptjm_accepted_at)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-[9px] font-bold rounded-md ml-1">
                            <i class="fa-solid fa-stamp"></i> SPTJM Sah
                        </span>
                        @endif
                    </div>

                    <!-- SPTJM Info -->
                    @if($batch->sptjm_accepted_at)
                    <div class="text-[10px] text-slate-500 font-medium mb-3 flex items-center gap-1">
                        <i class="fa-solid fa-calendar-check text-emerald-600"></i>
                        Disahkan: {{ $batch->sptjm_accepted_at->translatedFormat('d M Y H:i') }} WIB
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                        <a href="{{ route('dinasesdm.historis.index', ['desa' => $batch->desa, 'tahun' => $batch->tahun_anggaran]) }}"
                            class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-[11px] transition">
                            <i class="fa-solid fa-users text-[10px]"></i> Lihat Warga
                        </a>
                        <a href="{{ route('dinasesdm.export.pdf', ['batch_id' => $batch->id, 'desa' => $batch->desa, 'kecamatan' => $batch->kecamatan, 'kabupaten' => $batch->kabupaten, 'scope' => 'historis']) }}"
                            target="_blank"
                            class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold rounded-xl text-[11px] transition">
                            <i class="fa-solid fa-file-pdf text-[10px]"></i> Ekspor PDF
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif --}}

    <!-- Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-list-ul text-blue-600"></i>
                Daftar Data Historis Penerima BPBL
            </h3>
            <span class="text-xs text-slate-500 font-semibold">Total {{ number_format($wargas->total()) }} Data</span>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full min-w-212.5 text-left border-collapse">
                <thead>
                    <tr
                        class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-500 tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6 min-w-45">Nama Pemohon & NIK</th>
                        <th class="py-3.5 px-4 min-w-40">Wilayah Domisili</th>
                        <th class="py-3.5 px-4 min-w-55">Alamat Lengkap</th>
                        <th class="py-3.5 px-4 text-center min-w-27.5">Tahun Realisasi</th>
                        <th class="py-3.5 px-4 text-center min-w-35">Status Realisasi</th>
                        <th class="py-3.5 px-4 min-w-30">Keterangan</th>
                        <th class="py-3.5 px-4 text-center min-w-25">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($wargas as $w)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4">
                            <span class="font-extrabold text-slate-900 block">{{ $w->nama }}</span>
                            <span class="text-[11px] font-mono text-slate-400">NIK: {{ $w->nik }}</span>
                        </td>

                        <td class="py-3.5 px-4 font-semibold text-slate-800">
                            <div>Desa {{ $w->desa }}</div>
                            <div class="text-[11px] text-slate-500 font-normal">Kec. {{ $w->kecamatan }},
                                {{ $w->kabupaten }}</div>
                        </td>

                        <td class="py-3.5 px-4 text-slate-600 font-medium">
                            <div class="max-w-xs truncate" title="{{ $w->alamat }}">{{ $w->alamat }}</div>
                            <span class="text-[10px] text-slate-400">RT/RW: {{ $w->rt_rw }}</span>
                        </td>

                        <td class="py-3.5 px-4 text-center">
                            <span
                                class="px-2.5 py-1 bg-indigo-50 text-indigo-900 border border-indigo-200 font-black rounded-lg text-xs shadow-2xs">
                                <i class="fa-solid fa-calendar-day text-indigo-600 mr-1"></i> Tahun
                                {{ $w->tahun_usulan ?: date('Y', strtotime($w->created_at)) }}
                            </span>
                        </td>

                        <td class="py-3.5 px-4">
                            @if($w->status_verifikasi === 'terpasang')
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Terpasang (Sudah
                                Realisasi)
                            </span>
                            @elseif($w->status_verifikasi === 'lolos_verifikasi_pusat')
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-800 border border-blue-200 text-[11px] font-bold rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Disetujui Pusat (Usulan)
                            </span>
                            @elseif($w->butuh_validasi_realisasi)
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold rounded-lg">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu Validasi Realisasi
                            </span>
                            @else
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 text-[11px] font-bold rounded-lg">
                                {{ str_replace('_', ' ', strtoupper($w->status_verifikasi)) }}
                            </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 text-slate-500 font-medium text-[11px]">
                            {{ $w->keterangan_import ?: ($w->catatan ?: '-') }}
                        </td>

                        <td class="py-3.5 px-4 text-right">
                            <a href="{{ route('dinasesdm.show', $w->id) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-extrabold rounded-xl text-[11px] transition">
                                <i class="fa-solid fa-eye text-[10px]"></i>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                            <div
                                class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700">Belum ada data historis yang sesuai!</p>
                            <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter tahun usulan atau kata kunci
                                pencarian Anda.</p>
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

<!-- Modal Upload File Import Data Lama -->
<div id="importModal"
    class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative space-y-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-file-import text-indigo-600"></i> Import Excel Data Lama BPBL
            </h3>
            <button type="button" onclick="closeImportModal()"
                class="text-slate-400 hover:text-slate-600 font-bold p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Download Template Button -->
        <div
            class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-between gap-3 text-xs">
            <div>
                <p class="font-extrabold text-indigo-950">Belum memiliki format CSV/Excel?</p>
                <p class="text-indigo-700 text-[11px] font-medium">Unduh template baku resmi untuk menyusun data lama.
                </p>
            </div>
            <a href="{{ route('dinasesdm.import.template') }}"
                class="px-3.5 py-2 bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-xs rounded-xl shadow-xs shrink-0 transition">
                Unduh Template
            </a>
        </div>

        <form action="{{ route('dinasesdm.import.excel') }}" method="POST" enctype="multipart/form-data"
            class="space-y-5">
            @csrf
            <div class="space-y-4 text-xs">
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px]">Pilih File Excel
                        / CSV (.csv, .xls, .xlsx, .txt):</label>
                    <input type="file" name="file" accept=".csv, .xls, .xlsx, .txt" required
                        class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-100 file:text-indigo-800 border border-slate-300 rounded-xl cursor-pointer">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px]">Status Verifikasi
                        Default Data Import:</label>
                    <select name="default_status"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                        <option value="terpasang">Terpasang (Sudah Realisasi)</option>
                        <option value="lolos_verifikasi_pusat">Lolos Verifikasi Pusat (Usulan Disetujui ESDM)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white font-extrabold rounded-xl text-xs shadow-md transition">
                    Unggah & Import Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Input Nomor Surat & Filter Ekspor ESDM -->
<div id="exportModal"
    class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-blue-600"></i> Pengaturan Filter Ekspor Data Historis
            </h3>
            <button type="button" onclick="closeExportModal()"
                class="text-slate-400 hover:text-slate-600 font-bold p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="exportForm" method="GET" action="" target="" onsubmit="handleExportSubmit(event)">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="scope" value="historis">

            <div class="space-y-4 text-xs mb-6">
                <!-- Section 1: Filter Wilayah & Status Ekspor -->
                <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3">
                    <p
                        class="font-extrabold text-slate-900 uppercase text-[11px] tracking-wider text-blue-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot"></i> Cakupan Wilayah Ekspor:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kabupaten / Kota:</label>
                            <select name="kabupaten"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                                <option value="">-- Semua Kabupaten/Kota --</option>
                                @foreach($kabupatens as $kab)
                                <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>
                                    {{ $kab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kecamatan:</label>
                            <select name="kecamatan"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                                <option value="">-- Semua Kecamatan --</option>
                                @foreach($kecamatans as $kec)
                                <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>
                                    {{ $kec }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Desa / Kelurahan:</label>
                            <select name="desa"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                                <option value="">-- Semua Desa --</option>
                                @foreach($desas as $des)
                                <option value="{{ $des }}" {{ request('desa') == $des ? 'selected' : '' }}>{{ $des }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tahun Usulan:</label>
                            <select name="tahun"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                                <option value="">-- Semua Tahun --</option>
                                @foreach($allYears as $y)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>Tahun {{ $y }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Centang Kolom yang Dibutuhkan -->
                <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <p
                            class="font-extrabold text-slate-900 uppercase text-[11px] tracking-wider text-blue-700 flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check"></i> Centang Kolom Data yang Dibutuhkan:
                        </p>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleSelectAllColumns(true)"
                                class="text-[10px] text-blue-700 font-extrabold hover:underline">
                                Centang Semua
                            </button>
                            <span class="text-slate-300">|</span>
                            <button type="button" onclick="toggleSelectAllColumns(false)"
                                class="text-[10px] text-rose-600 font-extrabold hover:underline">
                                Hapus Semua
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="no" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">No</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="provinsi" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Provinsi</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="kabupaten" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Kabupaten/Kota</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="kecamatan" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Kecamatan</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="desa" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Desa/Kelurahan</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="nama" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Nama Pemohon</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="nik" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">NIK Warga</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="alamat" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Alamat & RT/RW</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="no_hp" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">No. Telepon/HP</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="jarak_tiang" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Jarak Tiang</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="status" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Status Verifikasi</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition">
                            <input type="checkbox" name="columns[]" value="tahun" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Tahun Usulan/Real</span>
                        </label>
                        <label
                            class="flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-xl hover:bg-blue-50/50 cursor-pointer transition sm:col-span-2">
                            <input type="checkbox" name="columns[]" value="keterangan" checked
                                class="modal-col-checkbox rounded text-blue-600 focus:ring-blue-500">
                            <span class="text-slate-800 font-semibold">Keterangan / Catatan Import</span>
                        </label>
                    </div>
                </div>

                <!-- Section 3: Informasi Pengesahan Surat -->
                <div class="space-y-3">
                    <p
                        class="font-extrabold text-slate-900 uppercase text-[11px] tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square text-amber-500"></i> Header Surat & Pengesahan Dokumen:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Surat Resmi:</label>
                            <input type="text" name="nomor_surat" placeholder="B-500.10.17.2/ 123 /DESDM/2026"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 text-xs font-semibold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Dokumen:</label>
                            <input type="date" name="tanggal_surat" value="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 text-xs font-semibold">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeExportModal()"
                    class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" id="btnSubmitExport"
                    class="px-6 py-2.5 text-white font-extrabold rounded-xl text-xs shadow-md transition">
                    Proses Ekspor
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Animasi Sukses Ekspor ESDM -->
<div id="successExportModal"
    class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div
        class="bg-white rounded-3xl max-w-sm w-full p-6 sm:p-8 text-center shadow-2xl border border-emerald-100 space-y-4">
        <div
            class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-emerald-100 text-emerald-600 text-2xl font-bold shadow-md">
            <i class="fa-solid fa-check text-2xl"></i>
        </div>

        <div class="space-y-1">
            <h3 class="text-lg font-extrabold text-slate-900">Ekspor Berhasil!</h3>
            <p id="successExportText" class="text-xs text-slate-500 leading-relaxed font-medium">
                File laporan data pengajuan BPBL ESDM telah berhasil diproses.
            </p>
        </div>

        <button type="button" onclick="closeSuccessExportModal()"
            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md transition">
            Siap, Mengerti
        </button>
    </div>
</div>

<script>
    let currentExportType = 'excel';

    function toggleSelectAllColumns(status) {
        const checkboxes = document.querySelectorAll('.modal-col-checkbox');
        checkboxes.forEach(cb => cb.checked = status);
    }

    function openExportModal(type, scope = 'historis') {
        currentExportType = type;
        const modal = document.getElementById('exportModal');
        const form = document.getElementById('exportForm');
        const btn = document.getElementById('btnSubmitExport');

        if (type === 'excel') {
            form.action = "{{ route('dinasesdm.export.excel') }}";
            form.removeAttribute('target');
            btn.className =
                "px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md transition cursor-pointer";
            btn.innerText = "Unduh Excel (.xls)";
        } else {
            form.action = "{{ route('dinasesdm.export.pdf') }}";
            form.setAttribute('target', '_blank');
            btn.className =
                "px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-xl text-xs shadow-md transition cursor-pointer";
            btn.innerText = "Cetak / Buka PDF";
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function handleExportSubmit(event) {
        const type = currentExportType;
        closeExportModal();

        const successText = document.getElementById('successExportText');
        if (type === 'excel') {
            successText.innerText = "File Laporan Excel (.xls) telah berhasil dibuat dan terunduh ke perangkat Anda.";
        } else {
            successText.innerText = "Dokumen PDF Cetak Resmi Dinas ESDM telah dibuka di tab baru.";
        }

        // Tampilkan modal sukses hanya untuk cetak PDF atau beri jeda aman bagi excel
        if (type === 'pdf') {
            setTimeout(() => {
                showSuccessExportModal();
            }, 400);
        }
    }

    function showSuccessExportModal() {
        const modal = document.getElementById('successExportModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeSuccessExportModal() {
        const modal = document.getElementById('successExportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function closeExportModal() {
        const modal = document.getElementById('exportModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
