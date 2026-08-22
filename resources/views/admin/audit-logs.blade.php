@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Audit Log Sistem</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Daftar rekaman aktivitas seluruh administrator, staf, dan siswa.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <form method="GET" action="{{ route('admin.audit-logs') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pencarian</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari aktivitas atau nama pengguna..." 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition placeholder-slate-400 bg-slate-50/50 focus:bg-white"
                />
            </div>

            <!-- Event Filter -->
            <div>
                <label for="event" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Aktivitas</label>
                <select 
                    id="event" 
                    name="event" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white"
                >
                    <option value="">Semua Aktivitas</option>
                    @foreach ($events as $ev)
                        <option value="{{ $ev }}" {{ $eventFilter == $ev ? 'selected' : '' }}>{{ $ev }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <a href="{{ route('admin.audit-logs') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition inline-block">
                    Reset
                </a>
                <button type="submit" class="px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex-1">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Pengguna</th>
                        <th class="p-4">Peran</th>
                        <th class="p-4">Aktivitas</th>
                        <th class="p-4">Deskripsi</th>
                        <th class="p-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($auditLogs as $log)
                        <tr class="hover:bg-slate-50/50 transition text-slate-600 font-medium">
                            <td class="p-4 text-xs">{{ Carbon\Carbon::parse($log->created_at)->translatedFormat('d-m-Y H:i:s') }}</td>
                            <td class="p-4 text-slate-955 font-bold">{{ $log->user->name ?? 'System' }}</td>
                            <td class="p-4 capitalize text-xs">{{ $log->user->role ?? '-' }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold border bg-slate-50 text-slate-700">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-850">{{ $log->description }}</td>
                            <td class="p-4 font-mono text-xs">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 font-semibold">Tidak ada log aktivitas ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($auditLogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
