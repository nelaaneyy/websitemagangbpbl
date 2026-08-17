@extends('layouts.app')

@section('content')
<div class="py-12 bg-slate-50/70">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">
        
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden space-y-8 p-6 sm:p-10">
            <!-- Header Branding -->
            <div class="text-center space-y-3">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-700/25 mx-auto text-white text-2xl">
                    <i class="fa-solid fa-user-plus text-amber-400"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Akun Perangkat Desa</h2>
                    <p class="text-xs text-slate-500 mt-1">Daftarkan akun Kepala Desa / Sekretaris Desa untuk verifikasi berkas BPBL</p>
                </div>
            </div>

            <!-- E-Gov Alert Info -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-start gap-3 text-xs text-blue-900 font-medium">
                <i class="fa-solid fa-circle-info text-blue-600 text-base mt-0.5 shrink-0"></i>
                <p class="leading-relaxed">
                    <strong>Penting:</strong> Akun baru memerlukan persetujuan & verifikasi dokumen SK Pengangkatan oleh <strong>Dinas ESDM</strong> sebelum dapat digunakan untuk login.
                </p>
            </div>

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl text-xs space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>Terdapat Kesalahan Input:</span>
                    </div>
                    <ul class="list-disc list-inside text-[11px] text-rose-700 pl-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" method="POST" action="{{ route('register.desa.submit') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2 space-y-2">
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="Contoh: Drs. H. Budi Santoso, M.Si">
                    </div>

                    <!-- NIPD -->
                    <div class="space-y-2">
                        <label for="nipd" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">NIPD / NIK</label>
                        <input id="nipd" name="nipd" type="text" value="{{ old('nipd') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="Nomor Induk Perangkat Desa">
                    </div>

                    <!-- Nama Desa / Kelurahan -->
                    <div class="space-y-2">
                        <label for="desa" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Desa / Kelurahan <span class="text-rose-500">*</span></label>
                        <input id="desa" name="desa" type="text" required value="{{ old('desa') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="Contoh: Desa Suka Makmur">
                    </div>

                    <!-- Email -->
                    <div class="space-y-2">
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email Aktif <span class="text-rose-500">*</span></label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="kades@desasukamakmur.go.id">
                    </div>

                    <!-- No HP / Whatsapp -->
                    <div class="space-y-2">
                        <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">No. WhatsApp / HP</label>
                        <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password <span class="text-rose-500">*</span></label>
                        <input id="password" name="password" type="password" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="Minimal 8 karakter">
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Ulangi Password <span class="text-rose-500">*</span></label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="Ketik ulang password">
                    </div>
                </div>

                <!-- Upload File SK / Surat Tugas -->
                <div class="space-y-2">
                    <label for="sk_file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Scan SK Pengangkatan / Surat Tugas <span class="text-slate-400 font-normal lowercase">(PDF / JPG max 5MB)</span>
                    </label>
                    <input id="sk_file" name="sk_file" type="file" accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-100 file:text-blue-800 border border-slate-300 rounded-xl cursor-pointer">
                    <p class="text-[11px] text-slate-500">Digunakan oleh Tim Dinas ESDM untuk memvalidasi legalitas akun perangkat desa.</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 hover:from-blue-800 hover:to-indigo-800 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-700/25 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane text-amber-300"></i>
                        <span>Kirim Permohonan Akun Desa</span>
                    </button>
                </div>
            </form>

            <div class="text-center pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 font-medium">
                    Sudah memiliki akun yang disetujui? 
                    <a href="{{ route('login') }}" class="font-extrabold text-blue-700 hover:underline">Masuk Ke Portal &rarr;</a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection

