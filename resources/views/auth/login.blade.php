@extends('layouts.app')

@section('content')
<div class="py-12 bg-slate-50 min-h-[85vh] flex items-center justify-center px-4">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 border border-slate-100">

        <div class="relative overflow-hidden bg-gradient-to-br from-amber-700 via-amber-600 to-orange-700 p-8 sm:p-10 flex flex-col justify-between text-white lg:col-span-5 min-h-[260px] lg:min-h-full">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-amber-400/20 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center gap-3 bg-white/15 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/20 shadow-sm">
                    <img src="{{ asset('images/logo-jambi.png') }}" alt="Logo Jambi" class="h-8 w-auto object-contain">
                    <div class="h-5 w-px bg-white/40"></div>
                    <img src="{{ asset('images/logo-esdm.png') }}" alt="Logo ESDM" class="h-7 w-auto object-contain">
                </div>
            </div>

            <div class="relative z-10 mt-8 lg:mt-0">
                <h3 class="text-2xl font-bold leading-tight">Selamat Datang di Portal Petugas E-Listrik Dinas ESDM Provinsi Jambi</h3>
                <p class="text-xs text-indigo-100/80 mt-2 font-normal leading-relaxed">
                    Akses terintegrasi untuk pemantauan dan pengelolaan usulan bantuan daerah.
                </p>
            </div>
        </div>

        <div class="p-8 sm:p-12 lg:col-span-7 flex flex-col justify-center bg-white">
            <div class="mb-6">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Masuk Sebagai Petugas</h2>
            </div>

            @if (session('success'))
                <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-medium flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>Autentikasi Gagal:</span>
                    </div>
                    <ul class="list-disc list-inside text-[11px] text-rose-700 font-medium pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-4" method="POST" action="{{ route('login.submit') }}">
                @csrf

                <div>
                    <div class="relative">
                        <input id="email" name="email" type="email" required autofocus
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                            placeholder="Email resmi (nama@esdm.go.id)" value="{{ old('email') }}">
                        <i class="fa-solid fa-envelope absolute left-4 top-3.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                            class="w-full pl-11 pr-4 py-3 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-amber-600 focus:border-orange-600 text-xs font-medium transition placeholder:text-slate-400"
                            placeholder="Kata sandi">
                        <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 text-xs">
                    <label class="flex items-center gap-2 text-slate-500 font-medium cursor-pointer select-none">
                        <input id="remember" name="remember" type="checkbox" class="w-3.5 h-3.5 text-amber-600 border-slate-300 rounded focus:ring-amber-500">
                        <span>Ingat Sesi</span>
                    </label>
                    <a href="{{ route('login') }}" class="text-amber-600 hover:text-amber-800 font-semibold transition">
                        Lupa Password?
                    </a>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 active:scale-[0.99] text-white font-bold text-xs tracking-wider uppercase rounded-2xl shadow-md shadow-amber-600/20 transition-all flex items-center justify-center gap-2">
                        <span>Masuk</span>
                        <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center border-t border-slate-100 pt-6">
                <p class="text-[11px] text-slate-400 font-medium">
                    Belum punya akun?
                    <a href="{{ route('register.desa') }}" class="text-amber-600 hover:text-amber-800 font-semibold transition">
                        Daftar Akun Desa?
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
