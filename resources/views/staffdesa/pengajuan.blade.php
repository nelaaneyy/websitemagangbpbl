@extends('layouts.app')

@section('title', 'Portal Staff Administrasi Desa - Fasilitasi Pendaftaran Warga')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 shadow-xl backdrop-blur-md">
            <div class="flex items-center gap-3">
                <span class="p-2.5 bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-wide">Portal Front Office Staff Desa (ACT-02)</h1>
                    <p class="text-slate-400 text-sm mt-0.5">Fasilitasi pendaftaran warga tanpa smartphone / area blank spot & penataan berkas kependudukan.</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-slate-800/90 rounded-2xl border border-slate-700 p-6 shadow-xl">
            <form action="{{ route('warga.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="created_by_user_id" value="{{ auth()->user()->id }}">

                <h3 class="text-lg font-bold text-white border-b border-slate-700 pb-3">Form Fasilitasi Pendaftaran Mandiri / Pendamping</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">NIK Pemohon (16 Digit)</label>
                        <input type="text" name="nik" required maxlength="16" value="{{ old('nik') }}" placeholder="351501..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Nomor KK Pemohon (16 Digit)</label>
                        <input type="text" name="no_kk" maxlength="16" value="{{ old('no_kk') }}" placeholder="351501..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Nama Lengkap Sesuai KTP</label>
                        <input type="text" name="nama" required value="{{ old('nama') }}" placeholder="Budi Santoso" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Nomor Telepon / WA Pemohon</label>
                        <input type="text" name="no_hp" required value="{{ old('no_hp') }}" placeholder="081234567890" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Kabupaten</label>
                        <input type="text" name="kabupaten" required value="{{ old('kabupaten', 'Muaro Jambi') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" required value="{{ old('kecamatan', 'Jambi Luar Kota') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Nama Desa</label>
                        <input type="text" name="desa" required value="{{ old('desa', auth()->user()->desa ?? 'Mendalo Darat') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">RT / RW</label>
                        <input type="text" name="rt_rw" required value="{{ old('rt_rw') }}" placeholder="01/02" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Alamat Lengkap Bangunan</label>
                    <textarea name="alamat" rows="2" required placeholder="Jl. Raya Mendalo No. 12..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-sm text-white focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Latitude GPS</label>
                        <input type="text" id="staff-lat" name="latitude" required value="{{ old('latitude', '-1.610100') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Longitude GPS</label>
                        <input type="text" id="staff-lng" name="longitude" required value="{{ old('longitude', '103.613100') }}" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono">
                    </div>
                </div>

                <div class="space-y-4 pt-2 border-t border-slate-700">
                    <h4 class="text-sm font-bold text-emerald-400 uppercase">Upload Berkas Foto Fisik (Diunggah oleh Staff Desa)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-slate-400 mb-1">Foto KTP / KK Pemohon</label>
                            <input type="file" name="foto_ktp" required accept="image/*" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2 text-slate-300">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-1">Foto Rumah Tampak Depan</label>
                            <input type="file" name="foto_rumah_depan" required accept="image/*" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2 text-slate-300">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-1">Foto KWH Meteran Terdekat</label>
                            <input type="file" name="foto_kwh_rumah_terdekat" required accept="image/*" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2 text-slate-300">
                        </div>
                        <div>
                            <label class="block text-slate-400 mb-1">Foto Tiang Listrik Terdekat</label>
                            <input type="file" name="foto_tiang_rumah_terdekat" required accept="image/*" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2 text-slate-300">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-slate-400 mb-1">Foto SKTM / Surat Keterangan Tidak Mampu</label>
                            <input type="file" name="foto_sktm" required accept="image/*" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2 text-slate-300">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="persetujuan" required id="chk-agree" class="rounded text-emerald-500 focus:ring-emerald-500">
                    <label for="chk-agree" class="text-xs text-slate-300">Saya mengonfirmasi bahwa data kependudukan dan berkas fisik warga ini telah diverifikasi secara sah di kantor desa.</label>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-emerald-600/30">
                    Kirim Pendaftaran Warga via Front Office Desa
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
