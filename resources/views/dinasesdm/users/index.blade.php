@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header Navigation & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-200/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold rounded-md uppercase">Otorisasi Akses Pengguna</span>
                <span class="text-xs text-slate-400 font-semibold">Dinas ESDM</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">Kelola & Verifikasi Akun Pengguna</h2>
            <p class="text-xs text-slate-500 mt-0.5">Persetujuan pendaftaran akun baru & otorisasi hak akses Kepala Desa dan Petugas ESDM.</p>
        </div>
        @if(in_array(auth()->user()->role, ['instansi', 'super_admin']))
            <a href="{{ route('dinasesdm.users.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-2xl transition shadow-md shadow-blue-700/25 shrink-0">
                <i class="fa-solid fa-user-plus text-sm"></i>
                <span>Tambah Akun Baru</span>
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-900 flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Control Bar (Filter Status & Role) -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-3 rounded-2xl border border-slate-200/80">
        <!-- Status Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
            <a href="{{ route('dinasesdm.users.index', ['status' => 'approved', 'role' => request('role', 'all')]) }}"
                class="px-4 py-2 rounded-xl text-xs font-extrabold transition whitespace-nowrap flex items-center gap-2 {{ $status === 'approved' ? 'bg-blue-700 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Akun Aktif</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'approved' ? 'bg-blue-900 text-white' : 'bg-slate-200 text-slate-700' }}">
                    {{ $countApproved }}
                </span>
            </a>

            <a href="{{ route('dinasesdm.users.index', ['status' => 'pending', 'role' => request('role', 'all')]) }}"
                class="px-4 py-2 rounded-xl text-xs font-extrabold transition whitespace-nowrap flex items-center gap-2 {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Menunggu Verifikasi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $countPending > 0 ? 'bg-white text-amber-700 font-black animate-pulse' : ($status === 'pending' ? 'bg-amber-700 text-white' : 'bg-slate-200 text-slate-700') }}">
                    {{ $countPending }}
                </span>
            </a>

            <a href="{{ route('dinasesdm.users.index', ['status' => 'rejected', 'role' => request('role', 'all')]) }}"
                class="px-4 py-2 rounded-xl text-xs font-extrabold transition whitespace-nowrap flex items-center gap-2 {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <span>Ditolak</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $status === 'rejected' ? 'bg-rose-900 text-white' : 'bg-slate-200 text-slate-700' }}">
                    {{ $countRejected }}
                </span>
            </a>
        </div>

        <!-- Role Filter Dropdown -->
        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="text-xs font-bold text-slate-500">Peran:</span>
            <form method="GET" action="{{ route('dinasesdm.users.index') }}" class="flex items-center">
                <input type="hidden" name="status" value="{{ $status }}">
                <select name="role" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-600">
                    <option value="all" {{ request('role', 'all') === 'all' ? 'selected' : '' }}>Semua Role</option>
                    <option value="verifikator_esdm" {{ request('role') === 'verifikator_esdm' ? 'selected' : '' }}>Verifikator ESDM</option>
                    <option value="kepala_desa" {{ request('role') === 'kepala_desa' ? 'selected' : '' }}>Kepala Desa</option>
                    <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin ESDM</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-semibold">
                <thead class="bg-slate-900 text-white uppercase font-extrabold tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Nama & NIP</th>
                        <th class="px-5 py-4">Role & Hak Akses</th>
                        <th class="px-5 py-4">Desa / Unit Kerja</th>
                        <th class="px-5 py-4">Kontak Email / HP</th>
                        <th class="px-5 py-4">Dokumen SK</th>
                        <th class="px-5 py-4 text-right">Aksi Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $user->name }}</div>
                                @if($user->nipd)
                                    <div class="text-[11px] font-mono text-slate-400">NIP/NIPD: {{ $user->nipd }}</div>
                                @else
                                    <div class="text-[11px] text-slate-300 italic">NIP/NIPD belum diisi</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($user->role === 'verifikator_esdm')
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-900 border border-indigo-300 rounded-xl font-extrabold text-[11px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-user-check text-indigo-600"></i> Verifikator ESDM
                                    </span>
                                @elseif(in_array($user->role, ['super_admin', 'instansi']))
                                    <span class="px-3 py-1 bg-slate-900 text-amber-400 border border-slate-700 rounded-xl font-extrabold text-[11px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-user-shield text-amber-400"></i> Super Admin ESDM
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-blue-100 text-blue-900 border border-blue-300 rounded-xl font-extrabold text-[11px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-user-tie text-blue-600"></i> Kepala Desa
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 bg-slate-100 text-slate-800 rounded-xl font-bold text-xs inline-block">
                                    {{ $user->desa ?: 'Dinas ESDM Jambi' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-700">
                                <div class="font-semibold">{{ $user->email }}</div>
                                @if($user->no_hp)
                                    <div class="text-[11px] font-mono text-slate-400 mt-0.5">WA: {{ $user->no_hp }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($user->sk_file)
                                    <a href="{{ Storage::url($user->sk_file) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-xl text-xs font-bold transition">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat SK
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Tanpa File SK</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($user->status === 'pending')
                                        <form method="POST" action="{{ route('dinasesdm.users.approve', $user) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-extrabold text-xs shadow-xs transition flex items-center gap-1">
                                                <i class="fa-solid fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('dinasesdm.users.reject', $user) }}" class="inline" onsubmit="return confirm('Tolak pendaftaran akun ini?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-xl font-bold text-xs transition">
                                                <i class="fa-solid fa-xmark"></i> Tolak
                                            </button>
                                        </form>
                                    @elseif(in_array(auth()->user()->role, ['instansi', 'super_admin']))
                                        <a href="{{ route('dinasesdm.users.edit', $user) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-xl text-xs font-bold transition">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <form method="POST" action="{{ route('dinasesdm.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Hapus akun ini secara permanen?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-xl text-xs font-bold transition">
                                                <i class="fa-solid fa-trash-can"></i> Hapus
                                            </button>
                                        </form>
                                    @else
                                        @if($user->status === 'approved')
                                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-[11px] font-extrabold flex items-center gap-1">
                                                <i class="fa-solid fa-check"></i> Disetujui
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-xl text-[11px] font-extrabold flex items-center gap-1">
                                                <i class="fa-solid fa-xmark"></i> Ditolak
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 text-xs italic">
                                Tidak ada data akun dengan kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
