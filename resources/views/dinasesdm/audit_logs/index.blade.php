@extends('layouts.app')

@section('title', 'Immutable Audit Trail & Activity Logs - SIPELITA ESDM')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 shadow-xl backdrop-blur-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-indigo-500/20 text-indigo-400 rounded-xl border border-indigo-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <h1 class="text-2xl font-bold text-white tracking-wide">Immutable Audit Trail & System Logs</h1>
                </div>
                <p class="text-slate-400 text-sm mt-1">Rekam jejak aktivitas user, timestamp, alamat IP, koordinat GPS, dan snapshot perubahan state data.</p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('dinasesdm.audit_logs.index') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user, IP, atau aktivitas..." class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 w-full sm:w-64">
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl text-sm transition-colors">Cari</button>
            </form>
        </div>

        <!-- Table Audit Trail -->
        <div class="bg-slate-800/80 rounded-2xl border border-slate-700 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-900/80 text-xs uppercase text-slate-400 border-b border-slate-700">
                        <tr>
                            <th class="px-6 py-4">Waktu (Timestamp)</th>
                            <th class="px-6 py-4">Pengguna (User & Role)</th>
                            <th class="px-6 py-4">Aksi / Event</th>
                            <th class="px-6 py-4">Deskripsi Aktivitas</th>
                            <th class="px-6 py-4">IP Address & GPS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($logs as $log)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4 text-xs font-mono text-slate-400">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white">{{ $log->user_name }}</div>
                                <div class="text-xs text-indigo-400">{{ strtoupper($log->user_role) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 bg-slate-900 text-slate-300 border border-slate-700 rounded-md font-mono text-xs font-semibold">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-300">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-400">
                                <div>IP: {{ $log->ip_address }}</div>
                                @if($log->gps_coords)
                                    <div class="text-amber-400">GPS: {{ $log->gps_coords }}</div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada rekam log audit trail yang tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-700 bg-slate-900/50">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
