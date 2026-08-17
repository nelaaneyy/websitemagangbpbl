@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header Navigation -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
        <a href="{{ route('dinasesdm.desa.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-700 transition mb-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke List Desa
        </a>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tambah Data Rasio Elektrifikasi Desa</h2>
        <p class="text-xs text-slate-500 mt-0.5">Input koordinat geospasial dan kondisi keterjangkauan listrik desa baru.</p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        <form method="POST" action="{{ route('dinasesdm.desa.store') }}" class="space-y-6">
            @csrf

            <div class="space-y-5">
                <!-- Nama Desa -->
                <div class="space-y-2">
                    <label for="nama_desa" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Desa / Kelurahan <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_desa" name="nama_desa" value="{{ old('nama_desa') }}" required placeholder="Contoh: Mendalo Darat"
                           class="w-full px-4 py-3 bg-slate-50 border @error('nama_desa') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                    @error('nama_desa') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kabupaten / Kota -->
                <div class="space-y-2">
                    <label for="kabupaten" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kabupaten / Kota <span class="text-rose-500">*</span></label>
                    <select id="kabupaten" name="kabupaten" required
                            class="w-full px-4 py-3 bg-slate-50 border @error('kabupaten') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                        <option value="">-- Pilih Kabupaten / Kota --</option>
                        <option value="KABUPATEN KERINCI" {{ old('kabupaten') == 'KABUPATEN KERINCI' ? 'selected' : '' }}>KABUPATEN KERINCI</option>
                        <option value="KABUPATEN MERANGIN" {{ old('kabupaten') == 'KABUPATEN MERANGIN' ? 'selected' : '' }}>KABUPATEN MERANGIN</option>
                        <option value="KABUPATEN SAROLANGUN" {{ old('kabupaten') == 'KABUPATEN SAROLANGUN' ? 'selected' : '' }}>KABUPATEN SAROLANGUN</option>
                        <option value="KABUPATEN BATANG HARI" {{ old('kabupaten') == 'KABUPATEN BATANG HARI' ? 'selected' : '' }}>KABUPATEN BATANG HARI</option>
                        <option value="KABUPATEN MUARO JAMBI" {{ old('kabupaten') == 'KABUPATEN MUARO JAMBI' ? 'selected' : '' }}>KABUPATEN MUARO JAMBI</option>
                        <option value="KABUPATEN TANJUNG JABUNG TIMUR" {{ old('kabupaten') == 'KABUPATEN TANJUNG JABUNG TIMUR' ? 'selected' : '' }}>KABUPATEN TANJUNG JABUNG TIMUR</option>
                        <option value="KABUPATEN TANJUNG JABUNG BARAT" {{ old('kabupaten') == 'KABUPATEN TANJUNG JABUNG BARAT' ? 'selected' : '' }}>KABUPATEN TANJUNG JABUNG BARAT</option>
                        <option value="KABUPATEN TEBO" {{ old('kabupaten') == 'KABUPATEN TEBO' ? 'selected' : '' }}>KABUPATEN TEBO</option>
                        <option value="KABUPATEN BUNGO" {{ old('kabupaten') == 'KABUPATEN BUNGO' ? 'selected' : '' }}>KABUPATEN BUNGO</option>
                        <option value="KOTA JAMBI" {{ old('kabupaten') == 'KOTA JAMBI' ? 'selected' : '' }}>KOTA JAMBI</option>
                        <option value="KOTA SUNGAI PENUH" {{ old('kabupaten') == 'KOTA SUNGAI PENUH' ? 'selected' : '' }}>KOTA SUNGAI PENUH</option>
                    </select>
                    @error('kabupaten') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Latitude & Longitude -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="latitude" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Latitude (GPS) <span class="text-rose-500">*</span></label>
                        <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude') }}" required placeholder="Contoh: -1.6042"
                               class="w-full px-4 py-3 bg-slate-50 border @error('latitude') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                        @error('latitude') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="longitude" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Longitude (GPS) <span class="text-rose-500">*</span></label>
                        <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude') }}" required placeholder="Contoh: 103.5298"
                               class="w-full px-4 py-3 bg-slate-50 border @error('longitude') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                        @error('longitude') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Total RT & RT Berlistrik -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="total_rt" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Total Rumah Tangga (RT) <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" id="total_rt" name="total_rt" value="{{ old('total_rt', 0) }}" required
                               class="w-full px-4 py-3 bg-slate-50 border @error('total_rt') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                        @error('total_rt') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="berlistrik_rt" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">RT Sudah Berlistrik <span class="text-rose-500">*</span></label>
                        <input type="number" min="0" id="berlistrik_rt" name="berlistrik_rt" value="{{ old('berlistrik_rt', 0) }}" required
                               class="w-full px-4 py-3 bg-slate-50 border @error('berlistrik_rt') border-rose-400 @else border-slate-300 @enderror rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                        @error('berlistrik_rt') <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('dinasesdm.desa.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-xl shadow-md shadow-blue-700/25 transition">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Data Desa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

