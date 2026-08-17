@extends('layouts.app')

@section('content')
<div class="py-16 bg-slate-50/70 min-h-[75vh] flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden space-y-8 p-8 sm:p-10">
            <!-- Header Branding -->
            <div class="text-center space-y-3">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-700 via-blue-800 to-indigo-900 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-700/25 mx-auto text-white text-2xl">
                    <i class="fa-solid fa-user-shield text-amber-400"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Portal Login Petugas</h2>
                    <p class="text-xs text-slate-500 mt-1">Akses khusus Kepala Desa & Verifikator Dinas ESDM</p>
                </div>
            </div>

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-semibold flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl text-xs space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>Gagal Masuk:</span>
                    </div>
                    <ul class="list-disc list-inside text-[11px] text-rose-700 pl-1 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-5" method="POST" action="{{ route('login.submit') }}">
                @csrf
                
                <div class="space-y-2">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email Resmi</label>
                    <div class="relative">
                        <input id="email" name="email" type="email" required autofocus
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="nama@esdm.go.id / kades@desa.go.id" value="{{ old('email') }}">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-400"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi / Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-600 focus:border-blue-600 text-sm font-semibold transition"
                            placeholder="••••••••">
                        <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400"></i>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1 text-xs">
                    <label class="flex items-center gap-2 text-slate-600 font-semibold cursor-pointer">
                        <input id="remember" name="remember" type="checkbox" class="w-4 h-4 text-blue-700 border-slate-300 rounded focus:ring-blue-600">
                        <span>Ingat Sesi Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white font-extrabold text-sm rounded-xl shadow-lg shadow-blue-700/25 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-amber-300"></i>
                    <span>Masuk Ke Panel Kontrol</span>
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-medium">
                    Belum memiliki akun Kepala Desa?
                    <a href="{{ route('register.desa') }}" class="font-extrabold text-blue-700 hover:underline block sm:inline mt-1 sm:mt-0">
                        Daftar Akun Desa &rarr;
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection

