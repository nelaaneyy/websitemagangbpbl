@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Standalone Back Link (di luar container) -->
    <div>
        <a href="{{ route('dinasesdm.lisdes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-2xl border border-slate-200 shadow-xs transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke List Pengajuan Lisdes
        </a>
    </div>

    <!-- Breadcrumb & Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Usulan Lisdes: {{ $lisdes->desa }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Program perluasan jaringan listrik di Dusun {{ $lisdes->nama_dusun }}.</p>
        </div>

        <div>
            @if($lisdes->status === 'disetujui')
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Disetujui ESDM
                </span>
            @elseif($lisdes->status === 'ditolak')
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-rose-100 text-rose-900 border border-rose-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Ditolak / Perbaikan
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-900 border border-amber-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-clock text-amber-600"></i> Menunggu Verifikasi ESDM
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-900 flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Card 1: Data Wilayah & Pengaju -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 font-extrabold text-slate-900">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-house-laptop"></i>
            </div>
            <h3 class="text-base">Informasi Usulan Wilayah</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Desa / Kelurahan</span>
                <p class="font-extrabold text-slate-900 text-sm">{{ $lisdes->desa }}</p>
            </div>

            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kepala Desa / Pengaju</span>
                <p class="font-extrabold text-slate-900 text-sm">{{ $lisdes->user->name ?? '-' }}</p>
                @if(isset($lisdes->user->email))
                    <p class="text-xs text-slate-500 font-normal">{{ $lisdes->user->email }}</p>
                @endif
            </div>

            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nama Dusun / RT / RW</span>
                <p class="font-bold text-slate-800">{{ $lisdes->nama_dusun }}</p>
            </div>

            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Jumlah KK Sasaran</span>
                <p class="font-extrabold text-blue-700 text-base">{{ number_format($lisdes->jumlah_kk) }} KK</p>
            </div>

            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Estimasi Jarak ke Jaringan PLN</span>
                <p class="font-bold text-slate-800">{{ number_format($lisdes->estimasi_jarak) }} Meter</p>
            </div>

            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal Pengajuan</span>
                <p class="text-slate-700">{{ $lisdes->created_at ? $lisdes->created_at->translatedFormat('d F Y, H:i') : '-' }}</p>
            </div>
        </div>

        @if($lisdes->keterangan_wilayah)
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Catatan Kondisi Wilayah</span>
                <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 leading-relaxed font-medium">
                    {{ $lisdes->keterangan_wilayah }}
                </div>
            </div>
        @endif
    </div>

    <!-- Card 2: Dokumen Pendukung PDF & Foto Wilayah -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 font-extrabold text-slate-900">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
            <h3 class="text-base">Dokumen Pendukung & Foto</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Surat Permohonan Kades -->
            <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200/80 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 text-xs">Surat Kades</p>
                        <p class="text-[11px] text-slate-400">PDF Permohonan</p>
                    </div>
                </div>
                <a href="{{ asset('storage/' . $lisdes->surat_permohonan) }}" target="_blank"
                   class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-download"></i> Unduh
                </a>
            </div>

            <!-- Proposal Lisdes -->
            <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200/80 rounded-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-book-bookmark"></i>
                    </div>
                    <div>
                        <p class="font-extrabold text-slate-900 text-xs">Proposal Lisdes</p>
                        <p class="text-[11px] text-slate-400">PDF Rincian Teknis</p>
                    </div>
                </div>
                <a href="{{ asset('storage/' . $lisdes->proposal_lisdes) }}" target="_blank"
                   class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 shrink-0">
                    <i class="fa-solid fa-download"></i> Unduh
                </a>
            </div>
        </div>

        <!-- Foto Wilayah -->
        <div class="pt-2 space-y-2">
            <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Kondisi Dusun / Akses Jalan</span>
            <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm max-h-96 bg-slate-100 flex items-center justify-center">
                <img src="{{ asset('storage/' . $lisdes->foto_wilayah) }}" alt="Foto Kondisi Wilayah {{ $lisdes->nama_dusun }}" class="w-full object-cover max-h-96">
            </div>
        </div>
    </div>

    <!-- Card 3: Peta Koordinat GPS -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 font-extrabold text-slate-900">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h3 class="text-base">Peta Lokasi Geospasial</h3>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="font-mono text-slate-500"><strong class="text-slate-700">Lat:</strong> {{ $lisdes->latitude ?? '-' }}</span>
                <span class="font-mono text-slate-500"><strong class="text-slate-700">Lng:</strong> {{ $lisdes->longitude ?? '-' }}</span>
            </div>
        </div>

        <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
            <div id="detailMap" class="w-full h-80 z-10"></div>
        </div>
    </div>

    <!-- Card 4: Keputusan Verifikasi Dinas ESDM -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        <!-- Top Action Bar -->
        <div class="pb-4 border-b border-slate-100 text-xs font-bold">
            <a href="{{ route('dinasesdm.lisdes.index') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-blue-700 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali Tanpa Mengubah
            </a>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-gavel"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Keputusan Dinas ESDM</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Tentukan persetujuan usulan Lisdes di bawah ini.</p>
            </div>
        </div>

        @if($lisdes->catatan_esdm)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl space-y-1">
                <span class="block text-[11px] font-extrabold text-amber-900 uppercase tracking-wider">Catatan ESDM Terakhir</span>
                <p class="text-xs text-amber-900 leading-relaxed font-medium bg-white/80 p-3 rounded-xl border border-amber-200">
                    "{{ $lisdes->catatan_esdm }}"
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <!-- Setujui Form -->
            <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-4">
                <div>
                    <span class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">Setujui Usulan Lisdes</span>
                    <p class="text-xs text-slate-500 font-medium">Usulan akan disahkan dan diprioritaskan dalam program Lisdes PLN.</p>
                </div>
                <form action="{{ route('dinasesdm.lisdes.approve', $lisdes->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan Lisdes ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white font-extrabold text-xs rounded-2xl shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-check text-sm"></i> Setujui Pengajuan Lisdes
                    </button>
                </form>
            </div>

            <!-- Tolak Form dengan Catatan -->
            <div class="p-6 bg-rose-50/50 border border-rose-200/80 rounded-2xl space-y-4">
                <div>
                    <span class="block text-xs font-extrabold text-rose-900 uppercase tracking-wider mb-1">Tolak / Catatan Evaluasi</span>
                    <p class="text-xs text-rose-700 font-medium">Berikan catatan khusus perbaikan dokumen atau survei ulang.</p>
                </div>
                <form action="{{ route('dinasesdm.lisdes.reject', $lisdes->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <textarea id="catatan_esdm_form" name="catatan_esdm" rows="3" placeholder="Tuliskan alasan penolakan..."
                              class="w-full p-3 bg-white border border-rose-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500">{{ old('catatan_esdm', $lisdes->catatan_esdm) }}</textarea>

                    <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-2xl transition shadow-md shadow-rose-600/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-sm"></i> Tolak & Kirim Catatan Evaluasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const lat = {{ $lisdes->latitude ?? -1.6101 }};
            const lng = {{ $lisdes->longitude ?? 103.6131 }};

            const map = L.map('detailMap').setView([lat, lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors | Dinas ESDM'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>{{ $lisdes->nama_dusun }}</b><br>Desa {{ $lisdes->desa }}<br>Target: {{ $lisdes->jumlah_kk }} KK")
                .openPopup();
        });
    </script>
@endpush

