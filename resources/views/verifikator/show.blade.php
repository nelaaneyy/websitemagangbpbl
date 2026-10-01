@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Link -->
    <div>
        @if($warga->status_verifikasi === 'terpasang' || !empty($warga->keterangan_import))
            <a href="{{ route('verifikator.historis.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-2xl border border-slate-200 shadow-xs transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Data Historis
            </a>
        @else
            <a href="{{ route('verifikator.datalist') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-2xl border border-slate-200 shadow-xs transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke List Verifikasi
            </a>
        @endif
    </div>

    <!-- Header & Status -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Calon Penerima BPBL</h2>
            <p class="text-xs text-slate-500 mt-0.5">NIK Calon Penerima Bantuan: <span class="font-mono font-bold text-slate-700">{{ $warga->nik }}</span></p>
        </div>

        <div>
            @if($warga->status_verifikasi === 'terpasang')
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-950 border border-emerald-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Terpasang (Sudah Realisasi)
                </span>
            @elseif($warga->status_verifikasi === 'lolos_verifikasi_pusat')
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Disetujui (Lolos Verifikasi ESDM)
                </span>
            @elseif($warga->status_verifikasi === 'disetujui_desa')
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-900 border border-blue-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-check text-blue-600"></i> Diverifikasi Kepala Desa
                </span>
            @elseif($warga->status_verifikasi === 'menunggu_verifikasi_pusat')
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-900 border border-amber-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-clock text-amber-600"></i> Menunggu Verifikasi ESDM Provinsi Jambi
                </span>
            @elseif(in_array($warga->status_verifikasi, ['ditolak/perlu_perbaikan', 'ditolak']))
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-rose-100 text-rose-900 border border-rose-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Data pengajuan dikembalikan / Perlu Revisi
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-800 border border-slate-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-info-circle text-slate-600"></i> {{ str_replace('_', ' ', strtoupper($warga->status_verifikasi)) }}
                </span>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-900 flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 1. Biodata -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3 font-extrabold text-slate-900">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3 class="text-base">Data Administrasi Calon Penerima Terdaftar</h3>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">NIK Pemohon</span>
                <p class="text-slate-900 text-base font-mono font-extrabold tracking-wide">{{ $warga->nik }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap</span>
                <p class="text-slate-900 text-sm font-extrabold">{{ $warga->nama }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">No. WhatsApp / HP</span>
                <p class="text-slate-900 font-mono text-sm font-bold">{{ $warga->no_hp ?: '-' }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kabupaten / Kota</span>
                <p class="text-slate-900 font-bold">{{ $warga->kabupaten }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kecamatan</span>
                <p class="text-slate-900 font-bold">{{ $warga->kecamatan }}</p>
            </div>
            <div class="space-y-1 sm:col-span-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Desa / Kelurahan</span>
                <p class="text-slate-900 font-bold">{{ $warga->desa }} (RT {{ $warga->rt_rw }})</p>
            </div>
            <div class="space-y-1 sm:col-span-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Alamat Rumah Lengkap</span>
                <p class="text-slate-900 bg-slate-50 p-3 rounded-xl border border-slate-200/80 leading-relaxed">{{ $warga->alamat }}</p>
            </div>
            @if($warga->tahun_usulan || $warga->keterangan_import)
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">Tahun Realisasi / Usulan</span>
                <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-900 border border-indigo-200 font-extrabold rounded-xl text-xs">
                    <i class="fa-solid fa-calendar-day text-indigo-600 mr-1"></i> Tahun {{ $warga->tahun_usulan ?: date('Y', strtotime($warga->created_at)) }}
                </span>
            </div>
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-indigo-500 uppercase tracking-wider block">Keterangan / Status Import Data</span>
                <p class="bg-indigo-50/60 p-2.5 rounded-xl border border-indigo-100 font-semibold text-xs text-indigo-950">
                    @if($warga->keterangan_import)
                        {{ $warga->keterangan_import }}
                    @elseif($warga->sumber_data == 'excel')
                        Data Historis Import Excel
                    @else
                        Data Pengajuan Baru
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

    <!-- 2. Berkas Foto -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4 font-extrabold text-slate-900">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-images"></i>
            </div>
            <h3 class="text-base">Lampiran Berkas Foto Fisik</h3>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @if($warga->berkas)
                @php
                    $fotos = [
                        'Foto KTP'            => $warga->berkas->foto_ktp,
                        'Rumah Tampak Depan'  => $warga->berkas->foto_rumah_depan,
                        'kWH Tetangga'        => $warga->berkas->foto_kwh_rumah_terdekat,
                        'Tiang Terdekat'      => $warga->berkas->foto_tiang_rumah_terdekat,
                        'SKTM'                => $warga->berkas->foto_sktm,
                    ];
                @endphp
                @foreach($fotos as $label => $path)
                    @if($path)
                    <a href="{{ asset('storage/'.$path) }}" target="_blank" class="group block relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-xs">
                        <img src="{{ asset('storage/'.$path) }}" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                        <div class="absolute inset-x-0 bottom-0 bg-slate-900/80 backdrop-blur-xs p-2 text-center text-xs font-bold text-white">{{ $label }}</div>
                    </a>
                    @endif
                @endforeach
            @else
                <div class="col-span-4 p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-slate-200 font-semibold text-xs">Belum ada lampiran foto berkas.</div>
            @endif
        </div>
    </div>

    <!-- 3. Riwayat Catatan -->
    @if($warga->catatan)
        <div class="bg-amber-50 p-6 rounded-3xl border border-amber-200/90 shadow-sm space-y-2">
            <h3 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-comment-dots text-amber-600"></i> Riwayat Catatan Verifikator Terakhir:
            </h3>
            <p class="text-xs text-amber-900 leading-relaxed font-medium bg-white/80 p-4 rounded-2xl border border-amber-200">
                "{{ $warga->catatan }}"
            </p>
        </div>
    @endif

    <!-- 4. Form Keputusan Verifikator ESDM -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 text-xs font-bold">
            <a href="{{ route('verifikator.datalist') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-blue-700 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali Tanpa Mengubah
            </a>

            <form method="POST" action="{{ route('verifikator.destroy', $warga) }}" onsubmit="return confirm('Yakin ingin menghapus data warga ini secara permanen?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-slate-400 hover:text-rose-600 transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-trash text-xs"></i> Hapus Permohonan Permanen
                </button>
            </form>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-gavel"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Keputusan Tim Verifikator ESDM</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Tentukan persetujuan atau berikan catatan setelah memeriksa biodata & berkas.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <!-- Loloskan & teruskan ke admin -->
            <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-4">
                <div>
                    <span class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">Persetujuan & Teruskan ke Admin</span>
                    <p class="text-xs text-slate-500 font-medium">Jika berkas lengkap & memenuhi kriteria. Catatan bersifat opsional.</p>
                </div>

                <form method="POST" action="{{ route('verifikator.approve', $warga) }}" onsubmit="return confirm('Loloskan verifikasi dan teruskan ke admin?')" class="space-y-3">
                    @csrf @method('PATCH')
                    <textarea name="catatan" rows="3" placeholder="Catatan untuk admin (opsional)..."
                              class="w-full p-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-emerald-500">{{ old('catatan') }}</textarea>
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white text-xs font-extrabold rounded-2xl flex items-center justify-center gap-2 transition shadow-md shadow-emerald-600/20">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                        Loloskan & Teruskan ke Admin
                    </button>
                </form>
            </div>

            <!-- Tolak / revisi -->
            <div class="p-6 bg-rose-50/50 border border-rose-200/80 rounded-2xl space-y-4">
                <div>
                    <span class="block text-xs font-extrabold text-rose-900 uppercase tracking-wider mb-1">Kembalikan untuk Perbaikan</span>
                    <p class="text-xs text-rose-700 font-medium">Tuliskan alasan jika berkas kurang jelas atau tidak sesuai.</p>
                </div>

                <form method="POST" action="{{ route('verifikator.reject', $warga) }}" onsubmit="return confirm('Kembalikan pengajuan dengan catatan?')" class="space-y-3">
                    @csrf @method('PATCH')
                    <textarea name="catatan" rows="3" required placeholder="Tuliskan catatan revisi spesifik..."
                              class="w-full p-3 bg-white border border-rose-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500">{{ old('catatan') }}</textarea>
                    <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold rounded-2xl transition shadow-md shadow-rose-600/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                        Kembalikan & Kirim Catatan Revisi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
