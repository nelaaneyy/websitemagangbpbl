@extends('layouts.staffdesa')

@section('content')
<div class="space-y-5">
    <!-- Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/60">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase tracking-wider rounded-md border border-blue-200">
                    <i class="fa-solid fa-users-gear text-blue-600 mr-1"></i> Staff Administrasi Desa
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase tracking-wider rounded-md border border-emerald-200">
                    Tahun {{ date('Y') }}
                </span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Usulan BPBL - Desa {{ auth()->user()->desa }}</h1>
        </div>

        <div class="flex items-center gap-2">
            @if(isset($activeBatch) && $activeBatch->total_warga > 0 && $activeBatch->status === 'draft_staff')
                <form action="{{ route('staffdesa.batch.kirim', $activeBatch->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengirim berkas batch ini ke Kepala Desa untuk diverifikasi?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold rounded-xl transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Kirim Batch ke Kepala Desa</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('staffdesa.pengajuan') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-extrabold rounded-xl transition shadow-xs flex items-center gap-1.5">
                <i class="fa-solid fa-user-plus text-xs"></i> Tambah Warga
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-bold text-emerald-900 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs font-bold text-rose-900 space-y-1 shadow-2xs">
            @foreach($errors->all() as $err)
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>{{ $err }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Batch Pengajuan Desa Banner -->
    @if(isset($activeBatch))
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs space-y-3.5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 bg-slate-900 text-white text-[11px] font-mono font-bold rounded-md">
                            {{ $activeBatch->kode_batch }}
                        </span>
                        <span class="text-xs text-slate-500 font-semibold">Tahun Anggaran {{ $activeBatch->tahun_anggaran }}</span>
                    </div>
                    <h5 class="text-sm font-extrabold text-slate-900 mt-1">Batch Usulan Desa: {{ $activeBatch->total_warga }} / 1000 Warga Terdata</h5>
                    <p class="text-xs text-slate-500">Kuota Resmi Pengajuan Desa untuk Tahun Anggaran {{ date('Y') }}</p>
                </div>

                <div class="flex items-center gap-2.5">
                    @if(in_array($activeBatch->status, ['diajukan_ke_esdm', 'disetujui_esdm']))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-extrabold">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Diteruskan ke Dinas ESDM
                        </span>
                    @elseif($activeBatch->status === 'dikirim_ke_kades' || $activeBatch->status === 'diverifikasi_kades')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-extrabold">
                            <i class="fa-solid fa-clock text-amber-600 animate-pulse"></i> Menunggu Pengesahan Kades
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl text-xs font-extrabold">
                            <i class="fa-solid fa-pen-to-square text-blue-600"></i> Pengisian Pendataan Staff (Draft)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Progress Bar Kuota 1000 Warga -->
            <div class="space-y-1">
                <div class="flex justify-between text-xs font-semibold">
                    <span class="text-slate-500">Kapasitas Maksimal 1000 Warga / Tahun</span>
                    <span class="text-blue-700 font-bold font-mono">{{ number_format(($activeBatch->total_warga / 1000) * 100, 1) }}% Terisi ({{ $activeBatch->total_warga }}/1000)</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-linear-to-r from-blue-600 to-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ min(100, max(1, ($activeBatch->total_warga / 1000) * 100)) }}%;"></div>
                </div>
            </div>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-2xs">
        <form method="GET" action="{{ route('staffdesa.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-center">
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK atau Nama warga..."
                       class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-600">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-600">
                <option value="">Semua Tahapan Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Drafting Staff</option>
                <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Diperiksa Kades</option>
                <option value="menunggu_verifikasi_pusat" {{ request('status') == 'menunggu_verifikasi_pusat' ? 'selected' : '' }}>Proses ESDM Provinsi</option>
                <option value="lolos_verifikasi_pusat" {{ request('status') == 'lolos_verifikasi_pusat' ? 'selected' : '' }}>Lolos Verifikasi (Approved)</option>
                <option value="ditolak/perlu_perbaikan" {{ request('status') == 'ditolak/perlu_perbaikan' ? 'selected' : '' }}>Dikembalikan / Perlu Revisi</option>
            </select>

            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                Cari & Filter
            </button>

            @if(request('search') || request('status'))
                <a href="{{ route('staffdesa.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table Warga Staff Desa -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/80 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3">Nama Warga & NIK</th>
                        <th class="px-4 py-3">RT / RW</th>
                        <th class="px-4 py-3">Koordinat Rumah</th>
                        <th class="px-4 py-3 text-center">Status Verifikasi</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($wargas as $warga)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-4 py-3">
                                <span class="block text-slate-900 font-bold text-xs">{{ $warga->nama }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">NIK: {{ $warga->nik }}</span>
                                @if($warga->alamat)
                                    <span class="block text-[11px] text-slate-500 truncate max-w-xs">{{ $warga->alamat }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                <span class="px-2.5 py-0.5 bg-slate-100 text-slate-800 rounded-md font-bold text-xs">
                                    RT {{ $warga->rt_rw ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-[11px]">
                                @if($warga->latitude && $warga->longitude)
                                    <a href="https://www.google.com/maps?q={{ $warga->latitude }},{{ $warga->longitude }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 font-bold">
                                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                                        <span>{{ number_format($warga->latitude, 5) }}, {{ number_format($warga->longitude, 5) }}</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 font-sans italic">Belum diset</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($warga->status_verifikasi === 'draft')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-pen-ruler text-slate-500 text-[10px]"></i> Draft Staff
                                    </span>
                                @elseif($warga->status_verifikasi === 'terkirim')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-clock text-amber-600 animate-pulse text-[10px]"></i> Menunggu Kades
                                    </span>
                                @elseif(in_array($warga->status_verifikasi, ['diverifikasi_kades', 'disetujui_desa']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-teal-50 text-teal-800 border border-teal-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-check text-teal-600 text-[10px]"></i> Disetujui Kades
                                    </span>
                                @elseif($warga->status_verifikasi === 'menunggu_verifikasi_pusat')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-building text-blue-600 text-[10px]"></i> Menunggu ESDM
                                    </span>
                                @elseif($warga->status_verifikasi === 'lolos_verifikasi_pusat')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i> Disetujui ESDM
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-rose-50 text-rose-800 border border-rose-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-circle-xmark text-rose-600 text-[10px]"></i> Perlu Perbaikan
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('staffdesa.warga.edit', $warga->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400 font-medium text-xs">
                                <i class="fa-solid fa-inbox text-2xl mb-1 text-slate-300 block"></i>
                                Belum ada data warga terdaftar dalam usulan batch tahun berjalan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
            {{ $wargas->links() }}
        </div>
    </div>
</div>
@endsection
