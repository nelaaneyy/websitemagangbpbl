@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ createModalOpen: false, detailModalOpen: false, activeLisdes: null }">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-extrabold rounded-md uppercase tracking-wider">Program Infrastruktur Listrik Desa</span>
                <span class="text-xs text-slate-400 font-semibold">Desa {{ auth()->user()->desa }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Usulan Jaringan Listrik Desa (Lisdes)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola dan ajukan perluasan jaringan listrik PLN untuk dusun terisolir di wilayah Desa {{ auth()->user()->desa }}.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-900 flex items-center gap-2.5 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Total Usulan</span>
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-tower-cell"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-slate-400">Usulan Lisdes Diajukan</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-amber-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-amber-700 uppercase tracking-wider">Menunggu ESDM</span>
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-amber-900 tracking-tight">{{ $stats['menunggu_verifikasi'] }}</p>
            <p class="text-[11px] text-amber-600 font-medium">Dalam Proses Verifikasi</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-emerald-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider">Disetujui ESDM</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-emerald-900 tracking-tight">{{ $stats['disetujui'] }}</p>
            <p class="text-[11px] text-emerald-600 font-medium">Lolos Penilaian Kelayakan</p>
        </div>

        <div class="bg-white p-5 sm:p-6 rounded-3xl border border-rose-200/90 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-rose-700 uppercase tracking-wider">Perlu Perbaikan</span>
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-rose-900 tracking-tight">{{ $stats['ditolak'] }}</p>
            <p class="text-[11px] text-rose-600 font-medium">Memerlukan Pengajuan Ulang</p>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base">Daftar Usulan Listrik Desa (Lisdes)</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Riwayat usulan jaringan listrik yang diajukan oleh Desa {{ auth()->user()->desa }}.</p>
            </div>
            <a href="{{ route('kepaladesa.lisdes.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition cursor-pointer shadow-sm">
                <i class="fa-solid fa-plus text-amber-400"></i> Buat Usulan Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-semibold">
                <thead class="bg-slate-900 text-white uppercase font-extrabold tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Wilayah Usulan (Dusun)</th>
                        <th class="px-5 py-4 text-center">Sasaran KK</th>
                        <th class="px-5 py-4 text-center">Jarak ke PLN</th>
                        <th class="px-5 py-4">Dokumen Pendukung</th>
                        <th class="px-5 py-4 text-center">Status Verifikasi</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lisdesList as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $item->nama_dusun }}</div>
                                <div class="text-[11px] text-slate-500 font-medium">Desa {{ $item->desa }}</div>
                                @if($item->latitude && $item->longitude)
                                    <div class="text-[11px] font-mono text-blue-600 mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                                        <span>{{ $item->latitude }}, {{ $item->longitude }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center font-extrabold text-slate-900">
                                {{ number_format($item->jumlah_kk) }} KK
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-slate-700">
                                {{ number_format($item->estimasi_jarak) }} m
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-1 text-[11px]">
                                    @if($item->surat_permohonan)
                                        <a href="{{ asset('storage/' . $item->surat_permohonan) }}" target="_blank" class="text-blue-700 hover:underline inline-flex items-center gap-1 font-bold">
                                            <i class="fa-solid fa-file-pdf text-rose-600"></i> Surat Permohonan
                                        </a>
                                    @endif
                                    @if($item->proposal_lisdes)
                                        <a href="{{ asset('storage/' . $item->proposal_lisdes) }}" target="_blank" class="text-blue-700 hover:underline inline-flex items-center gap-1 font-bold">
                                            <i class="fa-solid fa-file-lines text-blue-600"></i> Proposal Lisdes
                                        </a>
                                    @endif
                                    @if($item->foto_wilayah)
                                        <a href="{{ asset('storage/' . $item->foto_wilayah) }}" target="_blank" class="text-emerald-700 hover:underline inline-flex items-center gap-1 font-bold">
                                            <i class="fa-solid fa-image text-emerald-600"></i> Foto Kondisi Wilayah
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                @if($item->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 text-[11px] font-extrabold rounded-full border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Disetujui ESDM
                                    </span>
                                @elseif($item->status === 'ditolak')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-800 text-[11px] font-extrabold rounded-full border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Perlu Perbaikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-800 text-[11px] font-extrabold rounded-full border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu ESDM
                                    </span>
                                @endif

                                @if($item->catatan_esdm)
                                    <div class="mt-1 text-[10px] text-rose-700 bg-rose-50 p-1.5 rounded-lg border border-rose-100 font-medium max-w-xs text-left">
                                        <span class="font-bold">Catatan:</span> {{ $item->catatan_esdm }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <button type="button"
                                        @click="activeLisdes = {{ json_encode($item) }}; detailModalOpen = true;"
                                        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition inline-flex items-center gap-1 cursor-pointer">
                                    <i class="fa-solid fa-eye text-blue-600"></i> Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-tower-cell text-4xl text-slate-300 block mb-3"></i>
                                <p class="font-extrabold text-slate-700 text-sm">Belum Ada Usulan Listrik Desa (Lisdes)</p>
                                <p class="text-xs text-slate-400 max-w-md mx-auto mt-1">Klik tombol "Buat Usulan Baru" di atas untuk membuat usulan perluasan jaringan listrik dusun baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($lisdesList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $lisdesList->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Tambah Pengajuan Lisdes -->
    <div x-cloak
         x-show="createModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs overflow-y-auto"
         @click.stop="createModalOpen = false">

        <div @click.stop class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden border border-slate-200 my-8">
            <!-- Modal Header -->
            <div class="bg-slate-900 text-white p-6 sm:p-8 flex items-center justify-between relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-amber-500/10 blur-xl"></div>
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500 text-slate-950 font-black text-[10px] uppercase rounded-full tracking-wider mb-1">
                        <i class="fa-solid fa-bolt"></i> Program Lisdes ESDM
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Formulir Usulan Listrik Desa (Lisdes)</h2>
                    <p class="text-xs text-slate-300 mt-0.5">Lengkapi data dusun usulan, estimasi jumlah KK, jarak jaringan PLN, dan unggah dokumen pendukung resmi.</p>
                </div>
                <button type="button" @click="createModalOpen = false" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition cursor-pointer relative z-10">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form action="{{ route('kepaladesa.lisdes.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6 max-h-[80vh] overflow-y-auto">
                @csrf

                <div class="grid lg:grid-cols-12 gap-6">
                    <!-- Left Column: Field Inputs (7 Col) -->
                    <div class="lg:col-span-7 space-y-5">
                        <div class="space-y-4 bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
                            <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-house-laptop text-amber-600"></i> Identitas & Informasi Dusun
                            </h4>

                            <div class="space-y-1">
                                <label for="nama_dusun" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nama Dusun / RT / RW <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="nama_dusun" name="nama_dusun" value="{{ old('nama_dusun') }}" required placeholder="Contoh: Dusun III RT 08"
                                       class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label for="jumlah_kk" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Jumlah KK Sasaran <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" min="1" id="jumlah_kk" name="jumlah_kk" value="{{ old('jumlah_kk') }}" required placeholder="Jumlah KK"
                                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                                </div>

                                <div class="space-y-1">
                                    <label for="estimasi_jarak" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Jarak ke PLN (Meter) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" min="1" id="estimasi_jarak" name="estimasi_jarak" value="{{ old('estimasi_jarak') }}" required placeholder="Contoh: 1500"
                                           class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label for="keterangan_wilayah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Keterangan Kebutuhan Wilayah
                                </label>
                                <textarea id="keterangan_wilayah" name="keterangan_wilayah" rows="2" placeholder="Jelaskan kondisi akses jalan atau urgennya listrik..."
                                          class="w-full p-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-600">{{ old('keterangan_wilayah') }}</textarea>
                            </div>
                        </div>

                        <div class="space-y-3 bg-slate-50 p-5 rounded-2xl border border-slate-200/80">
                            <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-file-pdf text-rose-600"></i> Dokumen Pendukung (PDF & Foto)
                            </h4>

                            <div class="space-y-1">
                                <label for="surat_permohonan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Surat Permohonan Resmi Kades (PDF) <span class="text-rose-500">*</span>
                                </label>
                                <input type="file" id="surat_permohonan" name="surat_permohonan" accept=".pdf" required
                                       class="w-full p-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 cursor-pointer">
                            </div>

                            <div class="space-y-1">
                                <label for="proposal_lisdes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Proposal Rincian Usulan Lisdes (PDF) <span class="text-rose-500">*</span>
                                </label>
                                <input type="file" id="proposal_lisdes" name="proposal_lisdes" accept=".pdf" required
                                       class="w-full p-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-blue-100 file:text-blue-800 hover:file:bg-blue-200 cursor-pointer">
                            </div>

                            <div class="space-y-1">
                                <label for="foto_wilayah" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Foto Kondisi Dusun (Gambar) <span class="text-rose-500">*</span>
                                </label>
                                <input type="file" id="foto_wilayah" name="foto_wilayah" accept="image/*" required
                                       class="w-full p-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Leaflet Map & GPS (5 Col) -->
                    <div class="lg:col-span-5 space-y-4 bg-slate-50 p-5 rounded-2xl border border-slate-200/80 flex flex-col justify-between">
                        <div class="space-y-3">
                            <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-map-location-dot text-emerald-600"></i> Titik Lokasi Dusun (GPS Map)
                            </h4>
                            <p class="text-[11px] text-slate-500 font-medium">Klik pada peta di bawah ini untuk menentukan titik koordinat lokasi dusun.</p>

                            <!-- Map Canvas -->
                            <div class="rounded-xl overflow-hidden border border-slate-300 shadow-xs">
                                <div id="modalLisdesMap" class="w-full h-56 z-10"></div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Latitude</label>
                                    <input type="text" id="modal_latitude" name="latitude" value="{{ old('latitude') }}" readonly required placeholder="-1.6101"
                                           class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-800">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Longitude</label>
                                    <input type="text" id="modal_longitude" name="longitude" value="{{ old('longitude') }}" readonly required placeholder="103.6131"
                                           class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-mono font-bold text-slate-800">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 pt-2 border-t border-slate-200">
                            <button type="button" onclick="deteksiModalGPS()" class="w-full py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 rounded-xl text-xs font-extrabold transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-location-crosshairs text-blue-600"></i> Deteksi Lokasi GPS Saya
                            </button>
                            <button type="submit" class="w-full py-3.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Usulan Lisdes Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Pengajuan Lisdes -->
    <div x-cloak
         x-show="detailModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs"
         @click.stop="detailModalOpen = false">

        <div @click.stop class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-6 sm:p-8 space-y-5 border border-slate-200" x-if="activeLisdes">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-tower-cell text-amber-500"></i> Detail Usulan Lisdes
                </h3>
                <button type="button" @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <template x-if="activeLisdes">
                <div class="space-y-4 text-xs font-semibold">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Wilayah Dusun:</span>
                            <span class="font-extrabold text-slate-900" x-text="activeLisdes.nama_dusun"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Desa:</span>
                            <span class="font-bold text-slate-800" x-text="activeLisdes.desa"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Jumlah KK Sasaran:</span>
                            <span class="font-bold text-slate-900" x-text="activeLisdes.jumlah_kk + ' KK'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Estimasi Jarak ke PLN:</span>
                            <span class="font-bold text-slate-900" x-text="activeLisdes.estimasi_jarak + ' Meter'"></span>
                        </div>
                    </div>

                    <div class="space-y-1" x-show="activeLisdes.keterangan_wilayah">
                        <span class="text-slate-400 text-[11px] block font-bold">Keterangan Akses & Kebutuhan:</span>
                        <p class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-slate-700 leading-relaxed" x-text="activeLisdes.keterangan_wilayah"></p>
                    </div>

                    <div class="space-y-1" x-show="activeLisdes.catatan_esdm">
                        <span class="text-rose-600 text-[11px] block font-bold">Catatan Penolakan / Verifikator ESDM:</span>
                        <p class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-800 font-bold leading-relaxed" x-text="activeLisdes.catatan_esdm"></p>
                    </div>
                </div>
            </template>

            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl transition">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let modalMap = null;
            let modalMarker = null;

            window.initLisdesMap = function() {
                if (modalMap) {
                    setTimeout(() => modalMap.invalidateSize(), 300);
                    return;
                }

                const defaultLat = -1.6101;
                const defaultLng = 103.6131;

                modalMap = L.map('modalLisdesMap').setView([defaultLat, defaultLng], 11);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors | ESDM'
                }).addTo(modalMap);

                function updateModalInputs(lat, lng) {
                    const latEl = document.getElementById('modal_latitude');
                    const lngEl = document.getElementById('modal_longitude');
                    if (latEl) latEl.value = lat.toFixed(7);
                    if (lngEl) lngEl.value = lng.toFixed(7);
                }

                modalMap.on('click', function(e) {
                    const { lat, lng } = e.latlng;
                    if (modalMarker) {
                        modalMarker.setLatLng([lat, lng]);
                    } else {
                        modalMarker = L.marker([lat, lng], { draggable: true }).addTo(modalMap);
                        modalMarker.on('dragend', function(evt) {
                            const pos = modalMarker.getLatLng();
                            updateModalInputs(pos.lat, pos.lng);
                        });
                    }
                    updateModalInputs(lat, lng);
                });

                setTimeout(() => modalMap.invalidateSize(), 300);
            };

            window.deteksiModalGPS = function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        if (modalMap) {
                            modalMap.setView([lat, lng], 15);
                            if (modalMarker) {
                                modalMarker.setLatLng([lat, lng]);
                            } else {
                                modalMarker = L.marker([lat, lng], { draggable: true }).addTo(modalMap);
                                modalMarker.on('dragend', function(evt) {
                                    const pos = modalMarker.getLatLng();
                                    const latEl = document.getElementById('modal_latitude');
                                    const lngEl = document.getElementById('modal_longitude');
                                    if (latEl) latEl.value = pos.lat.toFixed(7);
                                    if (lngEl) lngEl.value = pos.lng.toFixed(7);
                                });
                            }
                            const latEl = document.getElementById('modal_latitude');
                            const lngEl = document.getElementById('modal_longitude');
                            if (latEl) latEl.value = lat.toFixed(7);
                            if (lngEl) lngEl.value = lng.toFixed(7);
                        }
                    }, function(error) {
                        alert("Gagal mendeteksi lokasi GPS device. Silakan klik posisi dusun pada peta.");
                    }, { enableHighAccuracy: true });
                } else {
                    alert("Browser Anda tidak mendukung Geolocation GPS.");
                }
            };
        });
    </script>
@endpush
