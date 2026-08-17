@extends('layouts.admin')

@section('content')
<div class="space-y-5">

    <!-- Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/60">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Verifikasi Bantuan BPBL</h1>
            <p class="text-xs text-slate-500 font-medium">Validasi & kelayakan usulan pasang listrik warga.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openImportModal()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                <i class="fa-solid fa-file-import text-xs"></i>
                Import Excel
            </button>

            <button type="button" onclick="openExportModal('excel')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold rounded-xl transition cursor-pointer">
                <i class="fa-solid fa-file-excel text-xs"></i>
                Ekspor (.xls)
            </button>

            <button type="button" onclick="openExportModal('pdf')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 text-xs font-bold rounded-xl transition cursor-pointer">
                <i class="fa-solid fa-file-pdf text-xs"></i>
                Cetak PDF
            </button>

            <a href="{{ route('dinasesdm.backup') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold rounded-xl transition cursor-pointer">
                <i class="fa-solid fa-database text-amber-600 text-xs"></i>
                Backup Data (.json)
            </a>
        </div>
    </div>

    <!-- Stat Cards Ringkas (4 Metrics) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Permohonan</span>
                <p class="text-2xl font-extrabold text-slate-900 mt-0.5">{{ $stats['total'] }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-folder-open"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-amber-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block">Menunggu ESDM</span>
                <p class="text-2xl font-extrabold text-amber-900 mt-0.5">{{ $stats['menunggu'] }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-emerald-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block">Disetujui (Lolos)</span>
                <p class="text-2xl font-extrabold text-emerald-900 mt-0.5">{{ $stats['disetujui'] }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-rose-200/80 shadow-2xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block">Ditolak / Revisi</span>
                <p class="text-2xl font-extrabold text-rose-900 mt-0.5">{{ $stats['ditolak'] }}</p>
            </div>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
    <!-- SECTION GRAFIK ANALITIK & AUDIT TRAIL LOG -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Chart 1: Sebaran Pengajuan per Kabupaten -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-amber-500"></i> Sebaran BPBL per Kabupaten
                </h3>
                <span class="text-[10px] text-slate-400 font-bold">Realtime</span>
            </div>
            <div class="h-44 relative">
                <canvas id="chartKabupatenCanvas"></canvas>
            </div>
        </div>

        <!-- Chart 2: Komposisi Status Verifikasi -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-amber-500"></i> Komposisi Status Verifikasi
                </h3>
                <span class="text-[10px] text-slate-400 font-bold">Persentase</span>
            </div>
            <div class="h-44 relative flex items-center justify-center">
                <canvas id="chartStatusCanvas"></canvas>
            </div>
        </div>

        <!-- Widget Activity Log (Audit Trail) -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3 flex flex-col justify-between">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-amber-500"></i> Audit Trail Activity Log
                </h3>
                <span class="text-[10px] text-slate-400 font-bold">10 Terakhir</span>
            </div>
            <div class="space-y-2 max-h-44 overflow-y-auto pr-1 text-xs">
                @forelse($recentLogs as $log)
                    <div class="p-2 bg-slate-50 border border-slate-150 rounded-xl flex items-start gap-2">
                        <div class="w-6 h-6 rounded-lg bg-slate-900 text-amber-400 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5 shadow-2xs">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 leading-tight text-[11px] truncate">{{ $log->user_name }} <span class="text-[9px] text-slate-500 font-medium">({{ $log->user_role }})</span></p>
                            <p class="text-[10px] text-slate-600 font-medium leading-snug line-clamp-2 mt-0.5">{{ $log->description }}</p>
                            <span class="text-[9px] text-slate-400 block mt-0.5">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-xs italic text-center py-6">Belum ada catatan aktivitas sistem.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Minimal Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-2xs">
        <form method="GET" action="{{ route('dinasesdm.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5 items-center">
            <!-- Search Input -->
            <div class="relative lg:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK, Nama, Alamat..."
                       class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-600">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <!-- Filter Kabupaten -->
            <select name="kabupaten" id="filterKabupaten" onchange="onFilterKabupatenChange(this)" class="px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-600">
                <option value="">-- Kabupaten --</option>
                @foreach($kabupatens as $kab)
                    <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>{{ $kab }}</option>
                @endforeach
            </select>

            <!-- Filter Kecamatan -->
            <select name="kecamatan" id="filterKecamatan" onchange="onFilterKecamatanChange(this)" class="px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-600">
                <option value="">-- Kecamatan --</option>
                @foreach($kecamatans as $kec)
                    <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                @endforeach
            </select>

            <!-- Filter Status -->
            <select name="status" onchange="this.form.submit()" class="px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-600">
                <option value="">-- Semua Status --</option>
                <option value="menunggu_verifikasi_pusat" {{ request('status') == 'menunggu_verifikasi_pusat' ? 'selected' : '' }}>Menunggu ESDM</option>
                <option value="lolos_verifikasi_pusat" {{ request('status') == 'lolos_verifikasi_pusat' ? 'selected' : '' }}>Disetujui (Lolos)</option>
                <option value="ditolak/perlu_perbaikan" {{ request('status') == 'ditolak/perlu_perbaikan' ? 'selected' : '' }}>Ditolak / Perbaikan</option>
            </select>

            <!-- Action Filter Buttons -->
            <div class="flex items-center gap-1.5">
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                    Filter
                </button>
                @if(request('search') || request('kabupaten') || request('kecamatan') || request('desa') || request('status'))
                    <a href="{{ route('dinasesdm.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if (session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-900 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Data Table (Clean Minimalist Header & Border) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/80 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3">Nama Pemohon & NIK</th>
                        <th class="px-4 py-3">Alamat / Wilayah</th>
                        <th class="px-4 py-3">No. HP</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($wargas as $warga)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3">
                                <span class="block text-slate-900 font-bold text-xs">{{ $warga->nama }}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[11px] text-slate-400 font-mono">NIK: {{ $warga->nik }}</span>
                                    <span class="px-1.5 py-0.2 bg-blue-50 text-blue-800 text-[9px] font-bold rounded border border-blue-200">
                                        <i class="fa-solid fa-shield-check text-blue-600"></i> DTKS
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <span class="block text-slate-800 font-semibold text-xs">Desa {{ $warga->desa }}, Kec. {{ $warga->kecamatan }}</span>
                                <span class="text-[11px] text-slate-400">{{ $warga->kabupaten }} (RT {{ $warga->rt_rw }})</span>
                            </td>
                            <td class="px-4 py-3 text-slate-700 font-mono">
                                {{ $warga->no_hp ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($warga->status_verifikasi === 'lolos_verifikasi_pusat')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-50 text-emerald-800 text-[11px] font-bold rounded-md border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui (Lolos)
                                    </span>
                                @elseif($warga->status_verifikasi === 'menunggu_verifikasi_pusat')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-50 text-amber-800 text-[11px] font-bold rounded-md border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu ESDM
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-rose-50 text-rose-800 text-[11px] font-bold rounded-md border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak / Revisi
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dinasesdm.show', $warga) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400 font-medium text-xs">
                                <i class="fa-solid fa-inbox text-2xl mb-1 text-slate-300 block"></i>
                                Belum ada data pengajuan yang sesuai.
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


<!-- Modal Input Nomor Surat & Filter Ekspor ESDM -->
<div id="exportModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-blue-600"></i> Pengaturan Filter Ekspor Resmi ESDM
            </h3>
            <button type="button" onclick="closeExportModal()" class="text-slate-400 hover:text-slate-600 font-bold p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="exportForm" method="GET" action="" target="" onsubmit="handleExportSubmit(event)">
            <input type="hidden" name="search" value="{{ request('search') }}">

            <div class="space-y-4 text-xs mb-6">
                <!-- Section 1: Filter Wilayah & Status Ekspor -->
                <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3">
                    <p class="font-extrabold text-slate-900 uppercase text-[11px] tracking-wider text-blue-700 flex items-center gap-1.5"><i class="fa-solid fa-location-dot"></i> Cakupan Wilayah Ekspor:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kabupaten / Kota:</label>
                            <select name="kabupaten" id="modalKabupaten" onchange="onModalKabupatenChange(this.value)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                                <option value="">-- Semua Kabupaten/Kota --</option>
                                @foreach($kabupatens as $kab)
                                    <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>{{ $kab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kecamatan:</label>
                            <select name="kecamatan" id="modalKecamatan" onchange="onModalKecamatanChange(this.value)" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                                <option value="">-- Semua Kecamatan --</option>
                                @foreach($kecamatans as $kec)
                                    <option value="{{ $kec }}" {{ request('kecamatan') == $kec ? 'selected' : '' }}>{{ $kec }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-bold text-slate-700">Daftar Desa / Kelurahan:</label>
                                <button type="button" onclick="toggleSelectAllDesa()" class="text-[11px] text-blue-700 font-extrabold hover:underline">
                                    Pilih / Batalkan Semua
                                </button>
                            </div>
                            
                            <div id="modalDesaContainer" class="max-h-36 overflow-y-auto p-2.5 bg-white border border-slate-300 rounded-xl space-y-1 text-xs shadow-inner">
                                @forelse($desas as $d)
                                    <label class="flex items-center gap-2 p-1.5 hover:bg-blue-50 rounded-lg cursor-pointer transition">
                                        <input type="checkbox" name="desa[]" value="{{ $d }}" class="modal-desa-checkbox rounded text-blue-600 focus:ring-blue-500" {{ request('desa') == $d || !request('desa') ? 'checked' : '' }}>
                                        <span class="text-slate-800 font-semibold">{{ $d }}</span>
                                    </label>
                                @empty
                                    <p class="text-slate-400 text-xs italic p-1">Pilih Kabupaten & Kecamatan terlebih dahulu.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Verifikasi Data:</label>
                        <select name="status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                            <option value="">-- Semua Status Verifikasi --</option>
                            <option value="lolos_verifikasi_pusat" {{ request('status') == 'lolos_verifikasi_pusat' ? 'selected' : '' }}>Lolos Verifikasi Pusat (Disetujui)</option>
                            <option value="menunggu_verifikasi_pusat" {{ request('status') == 'menunggu_verifikasi_pusat' ? 'selected' : '' }}>Menunggu Verifikasi Pusat</option>
                            <option value="ditolak/perlu_perbaikan" {{ request('status') == 'ditolak/perlu_perbaikan' ? 'selected' : '' }}>Ditolak / Perlu Perbaikan</option>
                        </select>
                    </div>
                </div>

                <!-- Section 2: Informasi Pengesahan Surat -->
                <div class="space-y-3">
                    <p class="font-extrabold text-slate-900 uppercase text-[11px] tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-pen-to-square text-amber-500"></i> Header Surat & Pengesahan Dokumen:</p>
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
                <button type="button" onclick="closeExportModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" id="btnSubmitExport" class="px-6 py-2.5 text-white font-extrabold rounded-xl text-xs shadow-md transition">
                    Proses Ekspor
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Animasi Sukses Ekspor ESDM -->
<div id="successExportModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 sm:p-8 text-center shadow-2xl border border-emerald-100 space-y-4">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-2xl bg-emerald-100 text-emerald-600 text-2xl font-bold shadow-md">
            <i class="fa-solid fa-check text-2xl"></i>
        </div>

        <div class="space-y-1">
            <h3 class="text-lg font-extrabold text-slate-900">Ekspor Berhasil!</h3>
            <p id="successExportText" class="text-xs text-slate-500 leading-relaxed font-medium">
                File laporan data pengajuan BPBL ESDM telah berhasil diproses.
            </p>
        </div>

        <button type="button" onclick="closeSuccessExportModal()" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md transition">
            Siap, Mengerti
        </button>
    </div>
</div>

<!-- Modal Upload File Import Data Lama -->
<div id="importModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative space-y-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-file-import text-indigo-600"></i> Import Data Excel / CSV
            </h3>
            <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 font-bold p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Download Template Button -->
        <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-2xl flex items-center justify-between gap-3 text-xs">
            <div>
                <p class="font-extrabold text-indigo-950">Belum memiliki format CSV?</p>
                <p class="text-indigo-700 text-[11px] font-medium">Unduh template CSV resmi untuk menyusun data.</p>
            </div>
            <a href="{{ route('dinasesdm.import.template') }}" class="px-3.5 py-2 bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-xs rounded-xl shadow-xs shrink-0 transition">
                Unduh Template
            </a>
        </div>

        <form action="{{ route('dinasesdm.import.excel') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="space-y-4 text-xs">
                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px]">Pilih File Excel / CSV (.csv, .xlsx, .xls):</label>
                    <input type="file" name="file" accept=".csv, .xls, .xlsx, .txt" required
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-100 file:text-indigo-800 border border-slate-300 rounded-xl cursor-pointer">
                </div>

                <div class="space-y-1.5">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px]">Status Verifikasi Default Data Import:</label>
                    <select name="default_status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                        <option value="lolos_verifikasi_pusat">Lolos Verifikasi Pusat (Langsung Disetujui ESDM)</option>
                        <option value="menunggu_verifikasi_pusat">Menunggu Verifikasi Pusat (Perlu Review)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white font-extrabold rounded-xl text-xs shadow-md transition">
                    Unggah & Import Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const wilayahTree = @json($wilayahTree ?? []);
    let currentExportType = 'excel';

    function onFilterKabupatenChange(selectElement) {
        document.getElementById('filterKecamatan').value = '';
        document.getElementById('filterDesa').value = '';
        selectElement.form.submit();
    }

    function onFilterKecamatanChange(selectElement) {
        document.getElementById('filterDesa').value = '';
        selectElement.form.submit();
    }

    function onModalKabupatenChange(selectedKab) {
        const kecSelect = document.getElementById('modalKecamatan');
        const desaContainer = document.getElementById('modalDesaContainer');

        kecSelect.innerHTML = '<option value="">-- Semua Kecamatan --</option>';
        desaContainer.innerHTML = '<p class="text-slate-400 text-xs italic p-1">Pilih Kecamatan terlebih dahulu.</p>';

        if (selectedKab && wilayahTree[selectedKab]) {
            const kecs = Object.keys(wilayahTree[selectedKab]).sort();
            kecs.forEach(kec => {
                const opt = document.createElement('option');
                opt.value = kec;
                opt.textContent = kec;
                kecSelect.appendChild(opt);
            });
        }
    }

    function onModalKecamatanChange(selectedKec) {
        const selectedKab = document.getElementById('modalKabupaten').value;
        const desaContainer = document.getElementById('modalDesaContainer');

        desaContainer.innerHTML = '';

        if (selectedKab && selectedKec && wilayahTree[selectedKab] && wilayahTree[selectedKab][selectedKec]) {
            const desas = wilayahTree[selectedKab][selectedKec].sort();
            desas.forEach(desa => {
                const label = document.createElement('label');
                label.className = 'flex items-center gap-2 p-1.5 hover:bg-blue-50 rounded-lg cursor-pointer transition';
                label.innerHTML = `
                    <input type="checkbox" name="desa[]" value="${desa}" checked class="modal-desa-checkbox rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-slate-800 font-semibold">${desa}</span>
                `;
                desaContainer.appendChild(label);
            });
        } else {
            desaContainer.innerHTML = '<p class="text-slate-400 text-xs italic p-1">Pilih Kecamatan untuk melihat daftar desa.</p>';
        }
    }

    function toggleSelectAllDesa() {
        const checkboxes = document.querySelectorAll('.modal-desa-checkbox');
        if (checkboxes.length === 0) return;
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }

    function openExportModal(type) {
        currentExportType = type;
        const modal = document.getElementById('exportModal');
        const form = document.getElementById('exportForm');
        const btn = document.getElementById('btnSubmitExport');

        if (type === 'excel') {
            form.action = "{{ route('dinasesdm.export.excel') }}";
            form.removeAttribute('target');
            btn.className = "px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md transition cursor-pointer";
            btn.innerText = "Unduh Excel (.xls)";
        } else {
            form.action = "{{ route('dinasesdm.export.pdf') }}";
            form.setAttribute('target', '_blank');
            btn.className = "px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold rounded-xl text-xs shadow-md transition cursor-pointer";
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

        setTimeout(() => {
            showSuccessExportModal();
        }, 400);
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

<!-- Chart.js Library CDN & Initialization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Chart Kabupaten (Bar Chart)
    const kabData = @json($chartKabupaten);
    const kabLabels = Object.keys(kabData);
    const kabValues = Object.values(kabData);

    const ctxKab = document.getElementById('chartKabupatenCanvas');
    if (ctxKab) {
        new Chart(ctxKab, {
            type: 'bar',
            data: {
                labels: kabLabels.length > 0 ? kabLabels : ['Belum Ada Data'],
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: kabValues.length > 0 ? kabValues : [0],
                    backgroundColor: '#0f172a',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { font: { size: 9, weight: 'bold' } } },
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 9 } } }
                }
            }
        });
    }

    // 2. Chart Status Verifikasi (Doughnut Chart)
    const statusData = @json($chartStatus);
    const ctxStatus = document.getElementById('chartStatusCanvas');
    if (ctxStatus) {
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Pending Warga', 'Disetujui Desa', 'Terverifikasi ESDM', 'Terpasang PLN', 'Ditolak/Revisi'],
                datasets: [{
                    data: [
                        statusData.terkirim || 0,
                        statusData.disetujui_desa || 0,
                        statusData.lolos_verifikasi_pusat || 0,
                        statusData.terpasang || 0,
                        statusData.ditolak || 0
                    ],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#047857', '#f43f5e'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { font: { size: 9, weight: 'bold' }, boxWidth: 10 }
                    }
                }
            }
        });
    }
});
</script>
@endsection


