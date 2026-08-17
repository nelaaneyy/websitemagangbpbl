@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold rounded-md uppercase">Data Geospasial</span>
                <span class="text-xs text-slate-400 font-semibold">Dinas ESDM</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Kelola Rasio Elektrifikasi Desa</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pemetaan geospasial & tingkat keterjangkauan jaringan listrik desa di Jambi.</p>
        </div>
        <a href="{{ route('dinasesdm.desa.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-2xl transition shadow-md shadow-blue-700/25 shrink-0">
            <i class="fa-solid fa-plus text-sm"></i>
            <span>Tambah Data Desa</span>
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-900 flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Form Pencarian -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-sm border border-slate-200/80">
        <form method="GET" action="{{ route('dinasesdm.desa.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama desa atau kabupaten..."
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-blue-600">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>

            <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white text-xs font-extrabold rounded-xl transition shadow-xs">
                Cari
            </button>

            @if(request('search'))
                <a href="{{ route('dinasesdm.desa.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl text-center transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Data Desa -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left font-semibold">
                <thead class="bg-slate-900 text-white uppercase font-extrabold tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Nama Desa</th>
                        <th class="px-5 py-4">Kabupaten / Kota</th>
                        <th class="px-5 py-4">Koordinat GPS</th>
                        <th class="px-5 py-4 text-center">Total RT</th>
                        <th class="px-5 py-4 text-center">Berlistrik</th>
                        <th class="px-5 py-4 text-center">Rasio</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($desas as $desa)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4 font-extrabold text-slate-900 text-sm">{{ $desa->nama_desa }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $desa->kabupaten }}</td>
                            <td class="px-5 py-4 text-slate-500 font-mono">
                                <i class="fa-solid fa-location-dot text-blue-600"></i> {{ $desa->latitude }}, {{ $desa->longitude }}
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-slate-800">{{ $desa->total_rt }}</td>
                            <td class="px-5 py-4 text-center font-extrabold text-emerald-600">{{ $desa->berlistrik_rt }}</td>
                            <td class="px-5 py-4 text-center font-black text-blue-700 text-sm">{{ $desa->rasio_elektrifikasi }}%</td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @if($desa->status === 'full')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full text-[11px] font-extrabold">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> 100% Full
                                    </span>
                                @elseif($desa->status === 'sebagian')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-full text-[11px] font-extrabold">
                                        <i class="fa-solid fa-clock text-amber-600"></i> Sebagian
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-50 text-rose-800 border border-rose-200 rounded-full text-[11px] font-extrabold">
                                        <i class="fa-solid fa-circle-xmark text-rose-600"></i> 0% Belum
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('dinasesdm.desa.edit', $desa) }}" class="px-3 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 rounded-xl text-xs font-bold transition">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('dinasesdm.desa.destroy', $desa) }}" onsubmit="return confirm('Yakin ingin menghapus data desa {{ $desa->nama_desa }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-xl text-xs font-bold transition">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-slate-400 font-semibold">
                                <i class="fa-solid fa-building text-3xl mb-2 text-slate-300 block"></i>
                                Belum ada data desa terdaftar. Klik "Tambah Data Desa" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $desas->links() }}
        </div>
    </div>
</div>
@endsection

