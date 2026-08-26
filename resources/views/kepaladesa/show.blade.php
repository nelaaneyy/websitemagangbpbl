@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Standalone Back Link (di luar container) -->
    <div>
        <a href="{{ route('kepaladesa.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-2xl border border-slate-200 shadow-xs transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke List Verifikasi Desa
        </a>
    </div>

    <!-- Header Navigation & Status -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Calon Penerima BPBL</h2>
            <p class="text-xs text-slate-500 mt-0.5">NIK calon penerima bantuan: <span class="font-mono font-bold text-slate-700">{{ $warga->nik }}</span> </p>
        </div>

        <div>
            @if($warga->status_verifikasi === 'terkirim')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-100 text-amber-900 border border-amber-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-clock text-amber-600 animate-pulse"></i> Menunggu Verifikasi Kades
                </span>
            @elseif($warga->status_verifikasi === 'menunggu_verifikasi_pusat')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-100 text-blue-900 border border-blue-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-building text-blue-600"></i> Menunggu Verifikasi ESDM Provinsi Jambi
                </span>
            @elseif($warga->status_verifikasi === 'lolos_verifikasi_pusat')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Disetujui Dinas ESDM Provinsi Jambi
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-100 text-rose-900 border border-rose-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Data pengajuan dikembalikan / Perlu Revisi
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

    <!-- 1. Informasi Warga Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 font-extrabold text-slate-900">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <h3 class="text-base">Identitas Calon Penerima Bantuan</h3>
            </div>

            <!-- Tombol Edit Modal (jika perbaikan/terkirim) -->
            <div x-data="{ openEdit: false }">
                <button @click="openEdit = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square text-xs"></i> Edit Biodata
                </button>

                <!-- Modal Edit -->
                <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div @click.away="openEdit = false" class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
                        <h3 class="text-base font-extrabold text-slate-900 border-b pb-2">Edit Biodata Pemohon</h3>
                        <form method="POST" action="{{ route('kepaladesa.update', $warga) }}" class="space-y-3 text-xs">
                            @csrf @method('PUT')
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Nama Lengkap</label>
                                <input type="text" name="nama" value="{{ old('nama', $warga->nama) }}" class="w-full p-2.5 bg-slate-50 border rounded-xl font-semibold">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">NIK</label>
                                <input type="text" name="nik" value="{{ old('nik', $warga->nik) }}" class="w-full p-2.5 bg-slate-50 border rounded-xl font-semibold">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">No. WhatsApp</label>
                                <input type="text" name="no_hp" value="{{ old('no_hp', $warga->no_hp) }}" class="w-full p-2.5 bg-slate-50 border rounded-xl font-semibold">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">RT / RW</label>
                                <input type="text" name="rt_rw" value="{{ old('rt_rw', $warga->rt_rw) }}" class="w-full p-2.5 bg-slate-50 border rounded-xl font-semibold">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Alamat Lengkap</label>
                                <textarea name="alamat" rows="2" class="w-full p-2.5 bg-slate-50 border rounded-xl font-semibold">{{ old('alamat', $warga->alamat) }}</textarea>
                            </div>
                            <div class="pt-3 flex justify-end gap-2">
                                <button type="button" @click="openEdit = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Batal</button>
                                <button type="submit" class="px-5 py-2 bg-blue-700 text-white font-extrabold rounded-xl shadow-xs">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Highlight Bar NIK -->
        <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">NIK Pemohon</span>
                <p class="text-slate-900 text-base font-mono font-extrabold tracking-wide">{{ $warga->nik }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold">
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nama Lengkap</span>
                <p class="font-extrabold text-slate-900 text-sm">{{ $warga->nama }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Nomor WhatsApp / HP</span>
                <p class="font-mono text-slate-800 font-bold text-sm">{{ $warga->no_hp ?: '-' }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kabupaten / Kota</span>
                <p class="text-slate-800 font-bold">{{ $warga->kabupaten }}</p>
            </div>
            <div class="space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Kecamatan</span>
                <p class="text-slate-800 font-bold">{{ $warga->kecamatan }}</p>
            </div>
            <div class="space-y-1 sm:col-span-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Desa / Kelurahan</span>
                <p class="text-slate-800 font-bold">{{ $warga->desa }} (RT {{ $warga->rt_rw }})</p>
            </div>
            <div class="space-y-1 sm:col-span-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Alamat Rumah Lengkap</span>
                <p class="text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-200/80 leading-relaxed">{{ $warga->alamat }}</p>
            </div>
        </div>
    </div>

    <!-- 2. Berkas Foto Persyaratan Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4 font-extrabold text-slate-900">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-images"></i>
            </div>
            <h3 class="text-base">Lampiran Berkas Foto Fisik</h3>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @if($warga->berkas)
                <a href="{{ asset('storage/'.$warga->berkas->foto_ktp) }}" target="_blank" class="group block relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-xs">
                    <img src="{{ asset('storage/'.$warga->berkas->foto_ktp) }}" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-x-0 bottom-0 bg-slate-900/80 backdrop-blur-xs p-2 text-center text-xs font-bold text-white">Foto KTP</div>
                </a>
                <a href="{{ asset('storage/'.$warga->berkas->foto_rumah_depan) }}" target="_blank" class="group block relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-xs">
                    <img src="{{ asset('storage/'.$warga->berkas->foto_rumah_depan) }}" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-x-0 bottom-0 bg-slate-900/80 backdrop-blur-xs p-2 text-center text-xs font-bold text-white">Rumah Tampak Depan</div>
                </a>
                <a href="{{ asset('storage/'.$warga->berkas->foto_kwh_rumah_terdekat) }}" target="_blank" class="group block relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-xs">
                    <img src="{{ asset('storage/'.$warga->berkas->foto_kwh_rumah_terdekat) }}" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-x-0 bottom-0 bg-slate-900/80 backdrop-blur-xs p-2 text-center text-xs font-bold text-white">kWH Tetangga</div>
                </a>
                <a href="{{ asset('storage/'.$warga->berkas->foto_tiang_rumah_terdekat) }}" target="_blank" class="group block relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-xs">
                    <img src="{{ asset('storage/'.$warga->berkas->foto_tiang_rumah_terdekat) }}" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-x-0 bottom-0 bg-slate-900/80 backdrop-blur-xs p-2 text-center text-xs font-bold text-white">Tiang Terdekat</div>
                </a>
                @if($warga->berkas->foto_sktm)
                <a href="{{ asset('storage/'.$warga->berkas->foto_sktm) }}" target="_blank" class="group block relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-xs">
                    <img src="{{ asset('storage/'.$warga->berkas->foto_sktm) }}" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                    <div class="absolute inset-x-0 bottom-0 bg-slate-900/80 backdrop-blur-xs p-2 text-center text-xs font-bold text-white">SKTM</div>
                </a>
                @endif
            @else
                <div class="col-span-4 p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-slate-200 font-semibold text-xs">Belum ada lampiran foto berkas.</div>
            @endif
        </div>
    </div>

    <!-- 3. Riwayat Catatan Revisi (Jika ada) -->
    @if($warga->catatan)
        <div class="bg-amber-50 p-6 rounded-3xl border border-amber-200/90 shadow-sm space-y-2">
            <h3 class="text-xs font-extrabold text-amber-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-comment-dots text-amber-600"></i> Riwayat Catatan Verifikator:
            </h3>
            <p class="text-xs text-amber-900 leading-relaxed font-medium bg-white/80 p-4 rounded-2xl border border-amber-200">
                "{{ $warga->catatan }}"
            </p>
        </div>
    @endif

    <!-- 4. Form Keputusan Verifikasi Kepala Desa -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
        <!-- Action bar di ATAS Keputusan -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 text-xs font-bold">
            <a href="{{ route('kepaladesa.index') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-blue-700 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali Tanpa Mengubah
            </a>

            <form method="POST" action="{{ route('kepaladesa.destroy', $warga) }}" onsubmit="return confirm('Yakin ingin menghapus data warga ini secara permanen?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-slate-400 hover:text-rose-600 transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-trash text-xs"></i> Hapus Permohonan Permanen
                </button>
            </form>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-stamp"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Keputusan Kepala Desa</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Tentukan rekomendasi persetujuan awal sebelum diteruskan ke Dinas ESDM Provinsi.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <!-- Form Rekomendasikan ke ESDM -->
            <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-4">
                <div>
                    <span class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">Rekomendasikan ke Dinas ESDM</span>
                    <p class="text-xs text-slate-500 font-medium">Jika identitas warga valid & fisik rumah terverifikasi belum memiliki meteran PLN.</p>
                </div>

                <form method="POST" action="{{ route('kepaladesa.approve', $warga) }}" onsubmit="return confirm('Rekomendasikan warga ini ke Dinas ESDM Provinsi?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white text-xs font-extrabold rounded-2xl flex items-center justify-center gap-2 transition shadow-md shadow-blue-600/20">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                        Setujui & Teruskan ke Dinas ESDM
                    </button>
                </form>
            </div>

            <!-- Form Tolak / Revisi -->
            <div class="p-6 bg-rose-50/50 border border-rose-200/80 rounded-2xl space-y-4">
                <div>
                    <span class="block text-xs font-extrabold text-rose-900 uppercase tracking-wider mb-1">Kembalikan ke Warga (Perbaikan)</span>
                    <p class="text-xs text-rose-700 font-medium">Berikan alasan jika foto tidak jelas atau KK/KTP perlu direvisi.</p>
                </div>

                <form method="POST" action="{{ route('kepaladesa.reject', $warga) }}" onsubmit="return confirm('Kembalikan pengajuan ke warga untuk perbaikan?')" class="space-y-3">
                    @csrf @method('PATCH')
                    <textarea name="catatan" rows="3" placeholder="Tuliskan catatan perbaikan spesifik..."
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



<script>

</script>
@endsection
