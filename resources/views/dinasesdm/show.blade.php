@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Standalone Back Link (di luar container) -->
    <div>
        <a href="{{ route('dinasesdm.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-2xl border border-slate-200 shadow-xs transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke List Verifikasi
        </a>
    </div>

    <!-- Header Navigation & Status -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Verifikasi Berkas Pemohon</h2>
            <p class="text-xs text-slate-500 mt-0.5">NIK Pemohon: <span class="font-mono font-bold text-slate-700">{{ $warga->nik }}</span></p>
        </div>

        <div>
            @if($warga->status_verifikasi === 'lolos_verifikasi_pusat')
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Disetujui (Lolos)
                </span>
            @elseif($warga->status_verifikasi === 'menunggu_verifikasi_pusat')
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-900 border border-amber-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-clock text-amber-600"></i> Menunggu Verifikasi ESDM
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-rose-100 text-rose-900 border border-rose-300 rounded-2xl text-xs font-extrabold shadow-xs">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Ditolak / Revisi
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

    <!-- 1. Biodata & Alamat Warga Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3 font-extrabold text-slate-900">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-user"></i>
                </div>
                <h3 class="text-base">Biodata & Alamat KTP Warga</h3>
            </div>
        </div>

        <!-- Highlight Bar NIK & Status DTKS -->
        <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">NIK Pemohon</span>
                <p class="text-slate-900 text-base font-mono font-extrabold tracking-wide">{{ $warga->nik }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-1 bg-blue-100 text-blue-900 text-xs font-extrabold rounded-xl border border-blue-300 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-check text-blue-600"></i> Terdaftar DTKS Kemensos
                </span>
                <button type="button" onclick="checkLiveDtksApi('{{ $warga->nik }}')"
                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition cursor-pointer flex items-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-cloud-arrow-down"></i> Cek Live API SIKS-NG
                </button>
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

    <!-- 3. Riwayat Catatan Verifikator (Jika ada) -->
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

    <!-- 4. Form Keputusan Tim Verifikator ESDM -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80 space-y-6">
        <!-- Action bar di ATAS Keputusan -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 text-xs font-bold">
            <a href="{{ route('dinasesdm.index') }}" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-blue-700 transition">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali Tanpa Mengubah
            </a>

            <form method="POST" action="{{ route('dinasesdm.destroy', $warga) }}" onsubmit="return confirm('Yakin ingin menghapus data warga ini secara permanen?')">
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
                <p class="text-xs text-slate-500 font-medium mt-0.5">Tentukan persetujuan setelah memeriksa biodata & kelengkapan foto berkas di atas.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <!-- Form Loloskan / Setujui -->
            <div class="p-6 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-4">
                <div>
                    <span class="block text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-1">Persetujuan Kelayakan</span>
                    <p class="text-xs text-slate-500 font-medium">Jika berkas lengkap & memenuhi kriteria penerima bantuan BPBL.</p>
                </div>

                <form method="POST" action="{{ route('dinasesdm.approve', $warga) }}" onsubmit="return confirm('Loloskan verifikasi pusat? Data warga ini akan diteruskan ke WO PLN.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white text-xs font-extrabold rounded-2xl flex items-center justify-center gap-2 transition shadow-md shadow-emerald-600/20">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                        Loloskan Verifikasi Pusat
                    </button>
                </form>
            </div>

            <!-- Form Tolak / Perbaikan -->
            <div class="p-6 bg-rose-50/50 border border-rose-200/80 rounded-2xl space-y-4">
                <div>
                    <span class="block text-xs font-extrabold text-rose-900 uppercase tracking-wider mb-1">Catatan Penolakan / Revisi</span>
                    <p class="text-xs text-rose-700 font-medium">Tuliskan alasan jika berkas kurang jelas atau tidak sesuai kriteria.</p>
                </div>

                <form method="POST" action="{{ route('dinasesdm.reject', $warga) }}" onsubmit="return confirm('Tolak pengajuan ini dan berikan catatan perbaikan kepada kades/warga?')" class="space-y-3">
                    @csrf @method('PATCH')
                    <textarea name="catatan" rows="3" placeholder="Tuliskan catatan revisi spesifik di sini..."
                              class="w-full p-3 bg-white border border-rose-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-rose-500">{{ old('catatan') }}</textarea>
                    <button type="submit" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold rounded-2xl transition shadow-md shadow-rose-600/20 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-circle-xmark text-sm"></i>
                        Tolak & Kirim Catatan Revisi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Response Live API DTKS Kemensos -->
<div id="dtksApiModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-blue-100 space-y-5 relative">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-down text-blue-600"></i> Hasil Integrasi API SIKS-NG Kemensos RI
            </h3>
            <button type="button" onclick="closeDtksApiModal()" class="text-slate-400 hover:text-slate-600 font-bold p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div id="dtksApiLoading" class="py-8 text-center space-y-3">
            <i class="fa-solid fa-circle-notch text-blue-600 text-3xl animate-spin"></i>
            <p class="text-xs text-slate-500 font-semibold">Menghubungi Server Web Service API SIKS-NG Kemensos...</p>
        </div>

        <div id="dtksApiContent" class="hidden space-y-4 text-xs font-semibold">
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                <div>
                    <h4 class="font-extrabold text-emerald-950 text-sm">NIK Terverifikasi Resmi di DTKS!</h4>
                    <p id="dtksKeterangan" class="text-[11px] text-emerald-800 mt-0.5"></p>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2 font-mono">
                <div class="flex justify-between"><span>Nomor NIK:</span> <strong id="dtksNik" class="text-slate-900"></strong></div>
                <div class="flex justify-between"><span>ID Register DTKS:</span> <strong id="dtksId" class="text-blue-700"></strong></div>
                <div class="flex justify-between"><span>Desil P3KE:</span> <strong id="dtksDesil" class="text-emerald-700"></strong></div>
                <div class="flex justify-between"><span>Bantuan Aktif:</span> <strong id="dtksBansos" class="text-slate-900"></strong></div>
                <div class="flex justify-between"><span>Waktu Respon:</span> <strong id="dtksVerifiedAt" class="text-slate-600"></strong></div>
                <div class="flex justify-between border-t border-slate-200 pt-1.5 mt-1.5 text-[10px]">
                    <span>Sumber Server:</span> <strong id="dtksSumber" class="text-slate-500 font-sans"></strong>
                </div>
            </div>
        </div>

        <button type="button" onclick="closeDtksApiModal()" class="w-full py-3 bg-blue-700 hover:bg-blue-800 text-white font-extrabold rounded-xl text-xs shadow-md transition">
            Tutup & Lanjutkan Verifikasi
        </button>
    </div>
</div>

<script>
function checkLiveDtksApi(nik) {
    const modal = document.getElementById('dtksApiModal');
    const loading = document.getElementById('dtksApiLoading');
    const content = document.getElementById('dtksApiContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    fetch(`/api/dtks/check/${nik}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('dtksNik').innerText = data.nik;
            document.getElementById('dtksId').innerText = data.id_dtks;
            document.getElementById('dtksDesil').innerText = data.desil_p3ke;
            document.getElementById('dtksBansos').innerText = data.bantuan_aktif ? data.bantuan_aktif.join(', ') : '-';
            document.getElementById('dtksKeterangan').innerText = data.keterangan;
            document.getElementById('dtksVerifiedAt').innerText = data.verified_at;
            document.getElementById('dtksSumber').innerText = data.sumber_data;

            loading.classList.add('hidden');
            content.classList.remove('hidden');
        })
        .catch(err => {
            alert('Gagal terhubung ke server API DTKS. Menampilkan data lokal.');
            closeDtksApiModal();
        });
}

function closeDtksApiModal() {
    const modal = document.getElementById('dtksApiModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
