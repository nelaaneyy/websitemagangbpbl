@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{ selectedRole: '{{ old('role', $user->role) }}' }">
    <!-- Header Navigation -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
        <a href="{{ route('dinasesdm.users.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-700 transition mb-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke List Pengelolaan Akun
        </a>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Informasi Akun Pengguna</h2>
        <p class="text-xs text-slate-500 mt-0.5">Perbarui profil kedinasan, role, NIP, dan kontak {{ $user->name }}.</p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-bold text-rose-900 space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>{{ $error }}</span>
                    </p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('dinasesdm.users.update', $user) }}" class="space-y-5">
            @csrf @method('PUT')

            <!-- Role Selector Card Options -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Otorisasi Hak Akses (Role) <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                    <!-- Option 1: Verifikator ESDM -->
                    <label :class="selectedRole === 'verifikator_esdm' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-950 ring-2 ring-indigo-600/20' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-white'"
                           class="p-3.5 border-2 rounded-2xl cursor-pointer transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-extrabold text-xs text-indigo-900">Verifikator ESDM</span>
                            <input type="radio" name="role" value="verifikator_esdm" x-model="selectedRole" class="text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <span class="text-[11px] text-slate-500 leading-snug">Verifikasi permohonan warga, lisdes, & cetak laporan. (Tanpa akses kelola akun).</span>
                    </label>

                    <!-- Option 2: Kepala Desa -->
                    <label :class="selectedRole === 'kepala_desa' ? 'border-blue-600 bg-blue-50/50 text-blue-950 ring-2 ring-blue-600/20' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-white'"
                           class="p-3.5 border-2 rounded-2xl cursor-pointer transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-extrabold text-xs text-blue-900">Kepala Desa</span>
                            <input type="radio" name="role" value="kepala_desa" x-model="selectedRole" class="text-blue-600 focus:ring-blue-500">
                        </div>
                        <span class="text-[11px] text-slate-500 leading-snug">Akses verifikasi berkas warga desa & ajukan jaringan Lisdes.</span>
                    </label>

                    <!-- Option 3: Super Admin -->
                    <label :class="selectedRole === 'super_admin' ? 'border-amber-500 bg-amber-50/50 text-amber-950 ring-2 ring-amber-500/20' : 'border-slate-200 bg-slate-50 text-slate-700 hover:bg-white'"
                           class="p-3.5 border-2 rounded-2xl cursor-pointer transition-all flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-extrabold text-xs text-amber-900">Super Admin ESDM</span>
                            <input type="radio" name="role" value="super_admin" x-model="selectedRole" class="text-amber-600 focus:ring-amber-500">
                        </div>
                        <span class="text-[11px] text-slate-500 leading-snug">Akses penuh ke seluruh sistem, kelola master akun & backup.</span>
                    </label>

                </div>
            </div>

            <!-- Nama Lengkap & Gelar -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- NIPD / NIP -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" x-text="selectedRole === 'kepala_desa' ? 'NIPD / NIK Perangkat Desa' : 'NIP Pegawai ESDM / NIK'"></label>
                <input type="text" name="nipd" value="{{ old('nipd', $user->nipd) }}" placeholder="Nomor Induk Pegawai / Perangkat Desa"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- Email Kedinasan -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Email Kedinasan <span class="text-rose-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- Nama Desa / Instansi Unit -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    <span x-text="selectedRole === 'kepala_desa' ? 'Nama Desa / Kelurahan' : 'Unit / Sub-Dinas ESDM'"></span>
                    <span class="text-rose-500" x-show="selectedRole === 'kepala_desa'">*</span>
                </label>
                <input type="text" name="desa" value="{{ old('desa', $user->desa) }}" 
                       :required="selectedRole === 'kepala_desa'"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
            </div>

            <!-- No HP / WhatsApp -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">No HP / WhatsApp</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('dinasesdm.users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-xl shadow-md shadow-blue-700/25 transition">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
