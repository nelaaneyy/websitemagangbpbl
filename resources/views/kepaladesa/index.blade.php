@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    <!-- Header & Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200/60">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase tracking-wider rounded-md border border-blue-200">
                    <i class="fa-solid fa-landmark text-blue-600 mr-1"></i> Kantor Kepala Desa
                </span>
                <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase tracking-wider rounded-md border border-emerald-200">
                    Tahun {{ date('Y') }}
                </span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Verifikasi Usulan BPBL - Desa {{ auth()->user()->desa }}</h1>
            <p class="text-xs text-slate-500 font-medium">Validasi desil kemiskinan dan kelayakan warga sebelum diteruskan ke Dinas ESDM Provinsi Jambi.</p>
        </div>

        <div class="flex items-center gap-2">
            @if(isset($activeBatch) && $activeBatch->total_warga > 0 && in_array($activeBatch->status, ['draft_staff', 'dikirim_ke_kades', 'diverifikasi_kades']))
                <button type="button" onclick="openSptjmModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold rounded-xl transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-stamp text-xs"></i>
                    <span>Kirim Batch ke Dinas ESDM (SPTJM)</span>
                </button>
            @endif

            <a href="{{ route('warga.pengajuan') }}" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white text-xs font-extrabold rounded-xl transition shadow-xs flex items-center gap-1.5">
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
                    <h5>Batch Usulan Resmi Desa: {{ $wargas->count() }} Warga Terdata</h5>
                    <p>Usulan Kuota Desa Tahun Anggaran {{ date('Y') }}</p>
                </div>

                <div class="flex items-center gap-2.5">
                    @if($activeBatch->status === 'diajukan_ke_esdm' || $activeBatch->status === 'disetujui_esdm')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-extrabold">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Telah Diajukan ke Dinas ESDM
                        </span>
                        @if($activeBatch->sptjm_accepted_at)
                            <span class="text-[11px] text-slate-400 font-mono" title="Waktu Pengesahan SPTJM">
                                (SPTJM Sah: {{ $activeBatch->sptjm_accepted_at->format('d/m/Y H:i') }})
                            </span>
                        @endif
                    @elseif($activeBatch->status === 'dikirim_ke_kades')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl text-xs font-extrabold">
                            <i class="fa-solid fa-clock text-amber-600 animate-pulse"></i> Menunggu Validasi Kades
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl text-xs font-extrabold">
                            <i class="fa-solid fa-pen-ruler text-slate-500"></i> {{ $activeBatch->status_label }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="space-y-1">
                <div class="flex justify-between text-xs font-semibold">
                    <span class="text-slate-500">Kapasitas Maksimal 100 Warga / Tahun</span>
                    <span class="text-blue-700 font-bold font-mono">{{ $activeBatch->total_warga }}% Terisi</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ min(100, max(2, $activeBatch->total_warga)) }}%;"></div>
                </div>
            </div>
        </div>
    @endif

    <!-- Minimal Filter & Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-2xs">
        <form method="GET" action="{{ route('kepaladesa.index') }}" class="flex flex-col sm:flex-row gap-2.5 items-center">
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NIK atau Nama warga..."
                       class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-blue-600">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
            </div>

            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-600">
                <option value="">Semua Tahapan Status</option>
                <option value="terkirim" {{ request('status') == 'terkirim' ? 'selected' : '' }}>Terkirim (Menunggu Kades)</option>
                <option value="diverifikasi_kades" {{ request('status') == 'diverifikasi_kades' ? 'selected' : '' }}>Diverifikasi Kades</option>
                <option value="menunggu_verifikasi_pusat" {{ request('status') == 'menunggu_verifikasi_pusat' ? 'selected' : '' }}>Menunggu ESDM Provinsi Jambi</option>
                <option value="lolos_verifikasi_pusat" {{ request('status') == 'lolos_verifikasi_pusat' ? 'selected' : '' }}>Lolos Verifikasi (Approved)</option>
                <option value="ditolak/perlu_perbaikan" {{ request('status') == 'ditolak/perlu_perbaikan' ? 'selected' : '' }}>Dikembalikan / Perlu Revisi</option>
            </select>

            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                Cari & Filter
            </button>

            @if(request('search') || request('status'))
                <a href="{{ route('kepaladesa.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table (Clean Minimalist with Desil Badge) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100/80 text-slate-700 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3">Nama Warga & NIK</th>
                        <th class="px-4 py-3">RT / RW</th>
                        <th class="px-4 py-3 text-center">Status Desil</th>
                        <th class="px-4 py-3 text-center">Status Verifikasi</th>
                        <th class="px-4 py-3 text-right">Aksi Validasi</th>
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
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @php $desilVal = $warga->desil ?: 'Desil 1'; @endphp
                                @if($desilVal === 'Desil 1')
                                    <span class="px-2.5 py-0.5 bg-rose-50 text-rose-800 border border-rose-200 rounded-md text-[11px] font-extrabold" title="Sangat Miskin / Prioritas Utama">
                                        <i class="fa-solid fa-triangle-exclamation mr-0.5 text-rose-600"></i> Desil 1
                                    </span>
                                @elseif($desilVal === 'Desil 2')
                                    <span class="px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-md text-[11px] font-extrabold" title="Miskin">
                                        Desil 2
                                    </span>
                                @elseif($desilVal === 'Desil 3')
                                    <span class="px-2.5 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 rounded-md text-[11px] font-extrabold" title="Hampir Miskin">
                                        Desil 3
                                    </span>
                                @elseif($desilVal === 'Desil 4')
                                    <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded-md text-[11px] font-extrabold" title="Rentan Miskin">
                                        Desil 4
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-md text-[11px] font-bold" title="SKTM Desa">
                                        {{ $desilVal }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($warga->status_verifikasi === 'terkirim')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-md text-[11px] font-bold">
                                        <i class="fa-solid fa-clock text-amber-600 animate-pulse text-[10px]"></i> Menunggu Kades
                                    </span>
                                @elseif($warga->status_verifikasi === 'diverifikasi_kades' || $warga->status_verifikasi === 'disetujui_desa')
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
                                        <i class="fa-solid fa-circle-xmark text-rose-600 text-[10px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-1">
                                <a href="{{ route('kepaladesa.show', $warga) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                    <i class="fa-solid fa-clipboard-check text-xs"></i>
                                    Periksa 1 per 1
                                </a>

                                @if($warga->status_verifikasi === 'terkirim')
                                    <form action="{{ route('kepaladesa.approve', $warga) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition" title="Tandai Layak & Setujui">
                                            <i class="fa-solid fa-check"></i> Layak
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-400 font-medium text-xs">
                                <i class="fa-solid fa-inbox text-2xl mb-1 text-slate-300 block"></i>
                                Belum ada permohonan warga terdaftar untuk desa ini.
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

<!-- Modal Legalitas Dokumen & SPTJM Kepala Desa -->
@if(isset($activeBatch))
<div id="sptjmModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Pernyataan Tanggung Jawab Mutlak (SPTJM)</h3>
                    <p class="text-xs text-slate-500 font-medium">Pengesahan Legalitas Pengajuan Usulan BPBL Desa {{ auth()->user()->desa }}</p>
                </div>
            </div>
            <button type="button" onclick="closeSptjmModal()" class="text-slate-400 hover:text-slate-600 font-bold p-1 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('kepaladesa.batch.submit_esdm', $activeBatch->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs space-y-2.5 text-slate-700 leading-relaxed max-h-56 overflow-y-auto">
                <p class="font-bold text-slate-900 uppercase text-[11px] tracking-wide">
                    Landasan Hukum: Peraturan Menteri ESDM RI Nomor 3 Tahun 2022 Pasal 3 Ayat (2) Huruf c
                </p>
                <p>
                    Saya yang bertanda tangan di bawah ini, Kepala Desa <strong>{{ auth()->user()->desa }}</strong>, dengan ini menyatakan dengan sadar dan penuh rasa tanggung jawab bahwa:
                </p>
                <ol class="list-decimal list-inside space-y-1.5 pl-1">
                    <li>Daftar calon penerima sejumlah <strong>{{ $activeBatch->total_warga }} rumah tangga</strong> yang tercantum dalam Batch <strong>{{ $activeBatch->kode_batch }}</strong> telah diverifikasi dan divalidasi kebenaran identitas serta kondisi faktual di lapangan.</li>
                    <li>Seluruh warga yang diusulkan berdomisili sah di Desa {{ auth()->user()->desa }}, tergolong keluarga tidak mampu (Desil 1 s/d Desil 4 atau pemegang SKTM resmi), dan <strong>belum memiliki sambungan listrik PLN mandiri</strong>.</li>
                    <li>Jarak tarikan kabel saluran rumah ke tiang listrik/jaringan PLN terdekat memenuhi standar teknis (maksimal 30 meter).</li>
                    <li>Segala konsekuensi hukum yang timbul di kemudian hari akibat ketidaksesuaian data yang diusulkan menjadi tanggung jawab penuh pihak Pemerintah Desa.</li>
                </ol>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Surat Pengantar Usulan Desa:</label>
                    <input type="text" name="nomor_surat_pengantar" value="B-500.10.17/{{ date('Y') }}/DESA-{{ strtoupper(\Illuminate\Support\Str::slug(auth()->user()->desa)) }}"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white text-xs font-semibold font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan Tambahan untuk Verifikator ESDM:</label>
                    <input type="text" name="catatan_kades" placeholder="Contoh: Seluruh berkas SKTM lengkap terlampir..."
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl focus:bg-white text-xs font-semibold">
                </div>
            </div>

            <div class="flex items-start gap-3 p-3.5 bg-emerald-50/80 border border-emerald-200 rounded-xl">
                <input type="checkbox" name="sptjm_agreement" id="chk_sptjm" required value="1" class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                <label for="chk_sptjm" class="text-xs text-emerald-950 font-bold leading-snug cursor-pointer">
                    Saya selaku Kepala Desa secara sadar menyatakan bahwa seluruh dokumen & data usulan ini memiliki kekuatan hukum yang sah dan menyetujui pengesahan secara elektronik sebagai pengganti tanda tangan dan cap stempel manual.
                </label>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeSptjmModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs shadow-md transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Sahkan & Teruskan ke Dinas ESDM</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openSptjmModal() {
    document.getElementById('sptjmModal').classList.remove('hidden');
}

function closeSptjmModal() {
    document.getElementById('sptjmModal').classList.add('hidden');
}
</script>
@endif
@endsection
