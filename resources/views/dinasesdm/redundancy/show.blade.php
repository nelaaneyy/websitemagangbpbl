@extends('layouts.app')

@section('title', 'Panel Perbandingan Redudansi Spasial Berdampingan - WebGIS SIPELITA')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 shadow-xl backdrop-blur-md flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-rose-500/20 text-rose-400 rounded-xl border border-rose-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                    <div>
                        <h1 class="text-2xl font-bold text-white tracking-wide">Panel Perbandingan Redudansi Spasial & Integritas</h1>
                    </div>
                </div>
            </div>
            
            <div>
                <span class="px-4 py-2 bg-rose-500/20 text-rose-300 border border-rose-500/40 rounded-xl font-mono text-xs font-bold uppercase tracking-wider">
                    FLAG: {{ $checkResult['redundancy_flag'] }}
                </span>
            </div>
        </div>

        <!-- Warning Alert -->
        <div class="bg-amber-500/10 border border-amber-500/30 p-4 rounded-xl text-amber-300 text-sm flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                <h4 class="font-bold text-amber-200">Indikasi Anomali Terdeteksi System:</h4>
                <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs text-amber-300/90">
                    @foreach($checkResult['details'] as $detail)
                        <li>{{ $detail }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- SIDE-BY-SIDE COMPARISON PANEL -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- LEFT COLUMN: Pengajuan Saat Ini -->
            <div class="bg-slate-800/90 rounded-2xl border border-indigo-500/40 shadow-xl p-6 space-y-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-indigo-600 text-white px-4 py-1 rounded-bl-xl text-xs font-bold uppercase">
                    Pengajuan Saat Ini (Aktif)
                </div>
                
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                    {{ $warga->nama }}
                </h3>

                <div class="aspect-video bg-slate-900 rounded-xl overflow-hidden border border-slate-700 relative">
                    @if($warga->berkas && $warga->berkas->foto_rumah_depan)
                        <img src="{{ asset('storage/' . $warga->berkas->foto_rumah_depan) }}" alt="Foto Rumah" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-500 text-sm">Tidak ada foto rumah</div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                        <span class="text-slate-500 uppercase font-semibold block">NIK Pemohon</span>
                        <span class="font-mono font-medium text-slate-200">{{ $warga->nik }}</span>
                    </div>
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                        <span class="text-slate-500 uppercase font-semibold block">Nomor KK</span>
                        <span class="font-mono font-medium text-slate-200">{{ $warga->no_kk ?? '-' }}</span>
                    </div>
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50 col-span-2">
                        <span class="text-slate-500 uppercase font-semibold block">Alamat & Desa</span>
                        <span class="text-slate-200">{{ $warga->alamat }}, Desa {{ $warga->desa }}</span>
                    </div>
                    <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50 col-span-2">
                        <span class="text-slate-500 uppercase font-semibold block">Koordinat Spasial & EXIF</span>
                        <span class="font-mono text-amber-400">Lat: {{ $warga->latitude }}, Lng: {{ $warga->longitude }}</span>
                        <div class="text-slate-400 mt-0.5">EXIF Deviasi: {{ number_format($warga->exif_deviation_meters ?? 0, 1) }}m</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Arsip Terdekat (Radius <= 15 Meter) -->
            <div class="bg-slate-800/90 rounded-2xl border border-rose-500/40 shadow-xl p-6 space-y-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-rose-600 text-white px-4 py-1 rounded-bl-xl text-xs font-bold uppercase">
                    Arsip Pembanding (Proksimitas <= 15m)
                </div>

                @if(!empty($suspectRecords))
                    @php $suspect = $suspectRecords[0]; @endphp
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-3 h-3 bg-rose-500 rounded-full"></span>
                        {{ $suspect->nama }}
                    </h3>

                    <div class="aspect-video bg-slate-900 rounded-xl overflow-hidden border border-slate-700 relative">
                        @if($suspect->berkas && $suspect->berkas->foto_rumah_depan)
                            <img src="{{ asset('storage/' . $suspect->berkas->foto_rumah_depan) }}" alt="Foto Rumah Arsip" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-500 text-sm">Arsip tanpa foto</div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                            <span class="text-slate-500 uppercase font-semibold block">NIK Arsip</span>
                            <span class="font-mono font-medium text-slate-200">{{ $suspect->nik_masked }}</span>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50">
                            <span class="text-slate-500 uppercase font-semibold block">Status Pengajuan</span>
                            <span class="font-bold text-amber-400">{{ strtoupper($suspect->status_verifikasi) }}</span>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50 col-span-2">
                            <span class="text-slate-500 uppercase font-semibold block">ID Pelanggan / Meteran</span>
                            <span class="font-mono text-emerald-400">{{ $suspect->id_pelanggan ?? 'Belum ada ID Pelanggan' }}</span>
                        </div>
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-slate-700/50 col-span-2">
                            <span class="text-slate-500 uppercase font-semibold block">Koordinat & Jarak Fisik</span>
                            <span class="font-mono text-rose-400">Lat: {{ $suspect->latitude }}, Lng: {{ $suspect->longitude }}</span>
                        </div>
                    </div>
                @else
                    <div class="h-full flex items-center justify-center text-slate-500 text-sm">
                        Tidak ditemukan arsip persil terdekat di lokasi ini.
                    </div>
                @endif
            </div>
        </div>

        <!-- FORM OPSI RESOLUSI REDUDANSI (3 OPSI) -->
        <div class="bg-slate-800/90 rounded-2xl border border-slate-700 p-6 space-y-6 shadow-xl">
            <h3 class="text-lg font-bold text-white border-b border-slate-700 pb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Form Keputusan Resolusi Verifikator ESDM
            </h3>

            <form action="{{ route('dinasesdm.redundancy.resolve', $warga->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Option A -->
                    <label class="relative flex flex-col p-4 bg-slate-900/80 border border-slate-700 rounded-xl cursor-pointer hover:border-rose-500/60 transition-all has-[:checked]:border-rose-500 has-[:checked]:bg-rose-500/10">
                        <input type="radio" name="resolution_type" value="reject_duplicate" required class="sr-only" onchange="toggleOptionFields('reject')">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-rose-400 text-sm">Option A: Tolak Duplikasi</span>
                            <span class="w-4 h-4 rounded-full border border-slate-500 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-slate-400">Tolak pengajuan karena terbukti bangunan fisik sudah teraliri listrik atau pendaftaran berkas ganda.</p>
                    </label>

                    <!-- Option B -->
                    <label class="relative flex flex-col p-4 bg-slate-900/80 border border-slate-700 rounded-xl cursor-pointer hover:border-amber-500/60 transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10">
                        <input type="radio" name="resolution_type" value="merge" required class="sr-only" onchange="toggleOptionFields('merge')">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-amber-400 text-sm">Option B: Gabung Berkas (Merge)</span>
                            <span class="w-4 h-4 rounded-full border border-slate-500 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-slate-400">Gabungkan berkas pengajuan ini ke dalam tiket pendaftaran utama yang sudah aktif.</p>
                    </label>

                    <!-- Option C -->
                    <label class="relative flex flex-col p-4 bg-slate-900/80 border border-slate-700 rounded-xl cursor-pointer hover:border-emerald-500/60 transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10">
                        <input type="radio" name="resolution_type" value="special_override" required class="sr-only" onchange="toggleOptionFields('override')">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-emerald-400 text-sm">Option C: Special Override</span>
                            <span class="w-4 h-4 rounded-full border border-slate-500 flex items-center justify-center"></span>
                        </div>
                        <p class="text-xs text-slate-400">Bangunan fisik terbukti 2 sekat independen (kontrakan/rumah petak). Wajib upload foto sekat fisik.</p>
                    </label>
                </div>

                <!-- Field Upload Foto Sekat Fisik (Khusus Option C) -->
                <div id="field-override-photo" class="hidden bg-emerald-500/10 border border-emerald-500/30 p-4 rounded-xl space-y-2">
                    <label class="block text-xs font-semibold text-emerald-300 uppercase">Upload Foto Bukti Sekat Fisik Independen (Wajib untuk Special Override)</label>
                    <input type="file" name="foto_sekat_fisik" accept="image/*" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-300">
                    <p class="text-xs text-slate-400">Unggah foto bukti fisik batas/sekat bangunan terpisah untuk memvalidasi bahwa objek adalah 2 rumah tangga independen.</p>
                </div>

                <!-- Catatan Justifikasi Verifikator -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Catatan Justifikasi / Hasil Survei Lapangan (Wajib)</label>
                    <textarea name="justification_note" rows="3" required placeholder="Tuliskan catatan hasil verifikasi teknis dan alasan resolusi redudansi..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('dinasesdm.datalist') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-xl text-sm transition-colors border border-slate-700">Kembali</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm transition-colors shadow-lg shadow-indigo-600/30">
                        Simpan Decision & Rekam Audit Trail
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleOptionFields(option) {
        const photoField = document.getElementById('field-override-photo');
        if (option === 'override') {
            photoField.classList.remove('hidden');
        } else {
            photoField.classList.add('hidden');
        }
    }
</script>
@endsection
