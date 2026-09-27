@extends('layouts.app')

@section('content')
<div class="py-12 bg-slate-100 min-h-[85vh] flex items-center justify-center px-4">
    <!-- Card Utama: max-w-5xl, grid 2 kolom di layar desktop -->
    <div class="max-w-5xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 border border-slate-100">

        <!-- KOLOM KIRI: Branding & Informasi Pendaftaran -->
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-700 via-amber-600 to-orange-700 p-8 sm:p-10 flex flex-col justify-between text-white lg:col-span-4 min-h-[280px] lg:min-h-full">
            <!-- Lingkaran Aksen Estetis -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-amber-400/20 rounded-full blur-xl pointer-events-none"></div>

            <!-- Logo Header -->
            <div class="relative z-10">
                <div class="inline-flex items-center gap-3 bg-white/15 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/20 shadow-sm">
                    <img src="{{ asset('images/logo-jambi.png') }}" alt="Logo Jambi" class="h-8 w-auto object-contain">
                    <div class="h-5 w-px bg-white/40"></div>
                    <img src="{{ asset('images/logo-esdm.png') }}" alt="Logo ESDM" class="h-7 w-auto object-contain">
                </div>
            </div>

            <!-- Informasi & Ketentuan Registrasi -->
            <div class="relative z-10 mt-8 lg:mt-0 space-y-4">
                <div>
                    <span class="text-xs uppercase tracking-widest text-amber-200 font-semibold block mb-1">Registrasi Akun</span>
                    <h3 class="text-2xl font-bold leading-tight">Perangkat Desa E-Listrik</h3>
                    <p class="text-xs text-amber-100/90 mt-2 font-normal leading-relaxed">
                        Daftarkan akun Kepala Desa atau Staff Administrasi Desa untuk memverifikasi dan mengajukan bantuan ketenagalistrikan daerah secara terpadu.
                    </p>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Form Input Data -->
        <div class="p-8 sm:p-10 lg:col-span-8 flex flex-col justify-center bg-white">
            <div class="mb-6">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Pendaftaran Akun Desa</h2>
                <p class="text-xs text-slate-400 mt-1 font-medium">Lengkapi formulir di bawah ini dengan data yang sah dan dapat dipertanggungjawabkan</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>Terdapat Kesalahan Input:</span>
                    </div>
                    <ul class="list-disc list-inside text-[11px] text-rose-700 font-medium pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-4" method="POST" action="{{ route('register.desa.submit') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap & Gelar <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="name" name="name" type="text" required value="{{ old('name') }}"
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                                placeholder="Contoh: Drs. H. Budi Santoso, M.Si">
                            <i class="fa-solid fa-user absolute left-4 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <div class="pt-1">
                        <label for="role" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Daftar Sebagai <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="role" name="role" required
                                class="block w-full px-3 py-2.5 text-xs border border-slate-200 rounded-2xl bg-slate-50 focus:outline-none focus:border-amber-500 text-slate-700 appearance-none">
                                <option value=""> Pilih Peran / Jabatan </option>
                                <option value="kades" {{ old('role') == 'kades' ? 'selected' : '' }}>Kepala Desa / Lurah</option>
                                <option value="staf_desa" {{ old('role') == 'staf_desa' ? 'selected' : '' }}>Staf Administrasi Desa / Perangkat Desa</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        @error('role')
                            <p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIPD / NIK -->
                    <div>
                        <label for="nipd" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">NIK</label>
                        <div class="relative">
                            <input id="nipd" name="nipd" type="text" value="{{ old('nipd') }}"
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                                placeholder="Nomor Induk Kependudukan">
                            <i class="fa-solid fa-id-card absolute left-4 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Nama Desa / Kelurahan -->
                    <div>
                        <label for="desa" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Desa / Kelurahan <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="desa" name="desa" type="text" required value="{{ old('desa') }}"
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                                placeholder="Contoh: Desa Suka Makmur">
                            <i class="fa-solid fa-building-flag absolute left-4 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <div>
                        <label for="alamat" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Kantor Kepala Desa <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="alamat" name="alamat" type="text" required value="{{ old('alamat') }}"
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                                placeholder="Contoh: Jalan Raya Suka Makmur No. 1">
                            <i class="fa-solid fa-home absolute left-4 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Email Aktif -->
                    <div>
                        <label for="email" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Resmi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                                placeholder="kades@desasukamakmur.go.id">
                            <i class="fa-solid fa-envelope absolute left-4 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Nomor WhatsApp -->
                    <div>
                        <label for="no_hp" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="no_hp" name="no_hp" type="text" required value="{{ old('no_hp') }}"
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                                placeholder="08xxxxxxxxxx">
                            <i class="fa-brands fa-whatsapp absolute left-4 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                                placeholder="Minimal 8 karakter">
                            <i class="fa-solid fa-lock absolute left-4 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ulangi Kata Sandi <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="w-full pl-11 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                                placeholder="Ketik ulang password">
                            <i class="fa-solid fa-lock absolute left-4 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Upload File SK Kepala Desa -->

                <div class="pt-1">
                    <label for="sk_file" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Scan SK Pengangkatan / Surat Tugas <span class="text-slate-400 font-normal lowercase">(PDF/JPG maks. 5MB)</span>
                    </label>
                    <input id="sk_file" name="sk_file" type="file" accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100/70 file:text-amber-900 border border-slate-200 rounded-2xl bg-slate-50 cursor-pointer focus:outline-none">
                    <p class="text-[10px] text-slate-400 mt-1">Dokumen ini akan digunakan pihak Dinas ESDM Provinsi Jambi untuk memeriksa legalitas kepengurusan desa.</p>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-3">
                    <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-bold text-xs tracking-wider uppercase rounded-2xl shadow-md shadow-amber-600/20 transition-all flex items-center justify-center gap-2">
                        <span>Kirim Permohonan Akun Desa</span>
                    </button>
                </div>
            </form>

            <!-- Footer Kembali ke Login -->
            <div class="mt-6 text-center border-t border-slate-100 pt-5">
                <p class="text-[11px] text-slate-400 font-medium">
                    Sudah memiliki akun yang disetujui?
                    <a href="{{ route('login') }}" class="text-amber-600 hover:text-amber-800 font-semibold transition">
                        Masuk Ke Portal &rarr;
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
