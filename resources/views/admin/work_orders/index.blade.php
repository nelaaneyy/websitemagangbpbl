@extends('layouts.app')

@section('title', 'Portal Work Order Pemasangan Listrik (PLN Vendor & Field Team)')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-slate-800/80 p-6 rounded-2xl border border-slate-700 shadow-xl backdrop-blur-md">
            <div>
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-amber-500/20 text-amber-400 rounded-xl border border-amber-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <h1 class="text-2xl font-bold text-white tracking-wide">Portal Work Order Pemasangan (PLN Vendor)</h1>
                </div>
                <p class="text-slate-400 text-sm mt-1">Manajemen lifecycle pemasangan KWH meter & penugasan teknisi vendor PLN di lapangan.</p>
            </div>
            
            @if(auth()->user()->isVerifikatorEsdm() || auth()->user()->isSuperAdmin())
            <button onclick="document.getElementById('modal-create-wo').classList.remove('hidden')" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-semibold rounded-xl text-sm transition-all shadow-lg shadow-amber-500/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Terbitkan Work Order Baru
            </button>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- List Table -->
        <div class="bg-slate-800/80 rounded-2xl border border-slate-700 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/80 text-xs uppercase text-slate-400 border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Nomor WO</th>
                            <th class="px-6 py-4">Calon Penerima Manfaat</th>
                            <th class="px-6 py-4">Vendor PLN / Petugas</th>
                            <th class="px-6 py-4">Status Lifecycle</th>
                            <th class="px-6 py-4">No. KWH Meter</th>
                            <th class="px-6 py-4 text-right">Aksi & Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($workOrders as $wo)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 font-mono text-amber-400 font-medium">
                                {{ $wo->nomor_wo }}
                                <div class="text-xs text-slate-500 mt-0.5">{{ $wo->tanggal_wo ? $wo->tanggal_wo->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white">{{ $wo->warga->nama ?? '-' }}</div>
                                <div class="text-xs text-slate-400">NIK: {{ $wo->warga->nik_masked ?? '-' }} | Desa {{ $wo->warga->desa ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-200">{{ $wo->vendor->name ?? 'Belum Ditugaskan' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($wo->status === 'ditugaskan')
                                    <span class="px-3 py-1 bg-blue-500/20 text-blue-400 border border-blue-500/30 rounded-full text-xs font-semibold">Ditugaskan</span>
                                @elseif($wo->status === 'proses_pemasangan')
                                    <span class="px-3 py-1 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded-full text-xs font-semibold">Proses Pemasangan</span>
                                @elseif($wo->status === 'selesai_pemasangan')
                                    <span class="px-3 py-1 bg-purple-500/20 text-purple-400 border border-purple-500/30 rounded-full text-xs font-semibold">Selesai (Menunggu Verifikasi ESDM)</span>
                                @else
                                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full text-xs font-semibold">Terverifikasi / Terpasang</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-200">
                                {{ $wo->nomor_kwh_meter ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="openModalUpdate({{ $wo->id }}, '{{ $wo->status }}', '{{ $wo->nomor_kwh_meter }}')" class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-slate-200 text-xs font-medium rounded-lg border border-slate-600 transition-colors">
                                    Update Status / Foto
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada Work Order pemasangan yang diterbitkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-700 bg-slate-900/50">
                {{ $workOrders->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Create WO -->
<div id="modal-create-wo" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-lg font-bold text-white">Terbitkan Work Order Pemasangan Baru</h3>
        <form action="{{ route('admin.work_orders.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Pilih Penerima Manfaat (Lolos Verifikasi)</label>
                <select name="warga_id" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500">
                    @foreach(\App\Models\Warga::whereIn('status_verifikasi', ['lolos_verifikasi_pusat', 'disetujui_desa'])->get() as $w)
                        <option value="{{ $w->id }}">{{ $w->nama }} (NIK: {{ $w->nik_masked }}) - Desa {{ $w->desa }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Pilih Vendor PLN Pelaksana</label>
                <select name="vendor_id" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500">
                    <option value="">-- Pilih Vendor PLN --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Tanggal Work Order</label>
                <input type="date" name="tanggal_wo" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-create-wo').classList.add('hidden')" class="px-4 py-2 text-slate-400 hover:text-white text-sm font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 text-sm font-semibold rounded-xl">Terbitkan WO</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Update WO -->
<div id="modal-update-wo" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
        <h3 class="text-lg font-bold text-white">Update Lifecycle Work Order</h3>
        <form id="form-update-wo" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Status Lifecycle</label>
                <select id="update-status" name="status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500">
                    <option value="proses_pemasangan">Proses Pemasangan Jaringan / KWH</option>
                    <option value="selesai_pemasangan">Selesai Pemasangan (Menunggu Verifikasi ESDM)</option>
                    @if(auth()->user()->isVerifikatorEsdm() || auth()->user()->isSuperAdmin())
                        <option value="terverifikasi">Terverifikasi Final & Terpasang Resmi</option>
                    @endif
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Nomor KWH Meter Terpasang</label>
                <input type="text" id="update-kwh" name="nomor_kwh_meter" placeholder="Contoh: 1420593819" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Foto Bukti Pemasangan Terpasang</label>
                <input type="file" name="foto_pemasangan" accept="image/*" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-300">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modal-update-wo').classList.add('hidden')" class="px-4 py-2 text-slate-400 hover:text-white text-sm font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 text-sm font-semibold rounded-xl">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalUpdate(id, status, kwh) {
        const form = document.getElementById('form-update-wo');
        form.action = '/dinasesdm/work-orders/' + id + '/status';
        document.getElementById('update-status').value = status;
        document.getElementById('update-kwh').value = kwh || '';
        document.getElementById('modal-update-wo').classList.remove('hidden');
    }
</script>
@endsection
