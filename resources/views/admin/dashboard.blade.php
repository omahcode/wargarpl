@extends('layouts.app')

@section('title', 'Dashboard - WargaRPL')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display flex items-center gap-2">
                <span>Ringkasan Data WargaRPL</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 font-mono font-bold border border-indigo-200">Khusus RPL/PPLG</span>
            </h2>
            <p class="text-sm text-slate-400 font-medium mt-0.5">
                @if($activeYear)
                    Tahun Ajaran {{ $activeYear->name }} &bull; Bank Data Siswa RPL & PPLG - WargaRPL
                @else
                    Bank Data Siswa RPL & PPLG - SMKN 1 Cirebon
                @endif
            </p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="self-start sm:self-center px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
            Lihat Semua Siswa →
        </a>
    </div>

    <!-- Stat Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Siswa -->
        <a href="{{ route('admin.students.index') }}" class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center gap-4 hover:border-slate-300 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Warga RPL (Siswa)</span>
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($totalStudents) }}</span>
            </div>
        </a>

        <!-- Pegawai -->
        <a href="{{ route('admin.employees.index') }}" class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center gap-4 hover:border-slate-300 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Guru & Tendik RPL</span>
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($totalEmployees) }}</span>
            </div>
        </a>

        <!-- Rombel -->
        <a href="{{ route('admin.classes.index') }}" class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center gap-4 hover:border-slate-300 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-xl bg-violet-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Rombel RPL/PPLG</span>
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($totalClasses) }}</span>
            </div>
        </a>

        <!-- Jurusan -->
        <a href="{{ route('admin.majors.index') }}" class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex items-center gap-4 hover:border-slate-300 hover:shadow-md transition">
            <div class="w-11 h-11 rounded-xl bg-cyan-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Konsentrasi RPL</span>
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($totalMajors) }}</span>
            </div>
        </a>
    </div>

    <!-- Active Campaign -->
    @if ($campaign)
    <div class="bg-white border border-amber-200 rounded-2xl p-6 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-amber-50/80 to-transparent pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-amber-700 bg-amber-100 border border-amber-200 px-2.5 py-1 rounded-full uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Campaign Verifikasi Aktif
                    </span>
                    <h3 class="text-lg font-bold text-slate-900 mt-2">{{ $campaign->title }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $campaign->start_date }} – {{ $campaign->end_date }}</p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-3xl font-black text-amber-600">{{ $verificationStats['persen'] }}%</span>
                    <p class="text-xs text-slate-400 font-semibold">Selesai Verifikasi</p>
                </div>
            </div>

            <div class="w-full bg-slate-100 h-2 rounded-full mt-4 overflow-hidden">
                <div class="bg-amber-500 h-full rounded-full transition-all duration-700" style="width: {{ $verificationStats['persen'] }}%"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
                <div class="text-center p-3 bg-white rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total</span>
                    <div class="text-xl font-extrabold text-slate-900 mt-0.5">{{ number_format($verificationStats['total']) }}</div>
                </div>
                <div class="text-center p-3 bg-white rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold text-amber-500 uppercase tracking-wider block">Belum Diperiksa</span>
                    <div class="text-xl font-extrabold text-amber-600 mt-0.5">{{ number_format($verificationStats['belum']) }}</div>
                </div>
                <div class="text-center p-3 bg-white rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider block">Sesuai</span>
                    <div class="text-xl font-extrabold text-emerald-600 mt-0.5">{{ number_format($verificationStats['tanpa_perubahan']) }}</div>
                </div>
                <div class="text-center p-3 bg-white rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Diperbaiki</span>
                    <div class="text-xl font-extrabold text-indigo-600 mt-0.5">{{ number_format($verificationStats['diperbaiki']) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Middle Row: Grade + Gender + Major Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Grade Breakdown -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-5">Siswa per Tingkat</h4>
            @php $totalGrade = $gradeX + $gradeXI + $gradeXII; @endphp
            <div class="space-y-4">
                @foreach([['label'=>'Kelas X','val'=>$gradeX,'color'=>'bg-blue-500'], ['label'=>'Kelas XI','val'=>$gradeXI,'color'=>'bg-violet-500'], ['label'=>'Kelas XII','val'=>$gradeXII,'color'=>'bg-emerald-500']] as $g)
                <div>
                    <div class="flex justify-between text-sm font-semibold mb-1.5">
                        <span class="text-slate-600">{{ $g['label'] }}</span>
                        <span class="text-slate-900 font-bold">{{ number_format($g['val']) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="{{ $g['color'] }} h-full rounded-full transition-all duration-500"
                            style="width: {{ $totalGrade > 0 ? round(($g['val']/$totalGrade)*100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-5 pt-4 border-t border-slate-100">
                <div class="flex justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                    <span>Jenis Kelamin</span>
                    <span class="text-slate-700">L: {{ number_format($maleStudents) }} &bull; P: {{ number_format($femaleStudents) }}</span>
                </div>
            </div>
        </div>

        <!-- Major Breakdown -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Siswa per Jurusan</h4>
                <a href="{{ route('admin.majors.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Kelola Jurusan →</a>
            </div>
            @forelse ($majorStats as $ms)
            @php
                $maxMajor = $majorStats->max('total') ?: 1;
                $pct = round(($ms->total / $maxMajor) * 100);
            @endphp
            <div class="mb-3.5">
                <div class="flex justify-between text-sm mb-1.5">
                    <div>
                        <span class="font-bold text-slate-900">{{ $ms->code }}</span>
                        <span class="text-slate-400 text-xs font-medium ml-1.5 hidden sm:inline">{{ Str::limit($ms->name, 35) }}</span>
                    </div>
                    <span class="font-bold text-slate-700 shrink-0">{{ number_format($ms->total) }} siswa</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <div class="py-8 text-center text-slate-400 text-sm font-medium">Belum ada data jurusan.</div>
            @endforelse
        </div>
    </div>

    <!-- Per-Class Breakdown Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Detail Siswa per Rombel</h4>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $activeYear ? 'Tahun Ajaran ' . $activeYear->name : 'Semua Rombel' }}
                    &bull; {{ $classBreakdown->count() }} Rombel Terdaftar
                </p>
            </div>
            <a href="{{ route('admin.classes.index') }}" class="text-xs font-semibold text-blue-600 hover:underline shrink-0">Kelola Rombel →</a>
        </div>

        @if($classBreakdown->count() > 0)
        {{-- Group classes by grade --}}
        @foreach([10 => 'Kelas X', 11 => 'Kelas XI', 12 => 'Kelas XII'] as $grade => $gradeLabel)
            @php $gradeClasses = $classBreakdown->where('grade_level', $grade); @endphp
            @if($gradeClasses->count() > 0)
            <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/30">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $gradeLabel }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-0 divide-y sm:divide-y-0 sm:divide-x-0">
                @foreach($gradeClasses as $cls)
                <a href="{{ route('admin.classes.show', $cls->id) }}"
                    class="group flex items-center justify-between px-5 py-4 border-b border-slate-100 hover:bg-blue-50/50 transition cursor-pointer">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 group-hover:text-blue-700 transition truncate">{{ $cls->name }}</p>
                        <p class="text-xs text-slate-400 font-medium mt-0.5 truncate">
                            {{ $cls->major?->code ?? '-' }}
                            @if($cls->homeroomTeacher)
                                &bull; {{ Str::words($cls->homeroomTeacher->full_name, 2, '') }}
                            @endif
                        </p>
                    </div>
                    <div class="ml-3 shrink-0 text-right">
                        <span class="text-lg font-extrabold text-slate-900 group-hover:text-blue-700 transition">{{ $cls->student_count }}</span>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">siswa</p>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        @endforeach
        @else
        <div class="py-12 text-center">
            <svg class="w-10 h-10 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <p class="text-sm text-slate-400 font-medium">Belum ada rombel yang terdaftar.</p>
            <a href="{{ route('admin.classes.index') }}" class="text-xs font-semibold text-blue-600 mt-1 inline-block hover:underline">Tambah Rombel →</a>
        </div>
        @endif
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Aktivitas Sistem Terakhir</h4>
                <p class="text-xs text-slate-400 mt-0.5">8 Log audit terbaru</p>
            </div>
            <a href="{{ route('admin.audit-logs') }}" class="text-xs font-semibold text-blue-600 hover:underline shrink-0">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/30 border-b border-slate-100">
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Pengguna</th>
                        <th class="px-6 py-3">Event</th>
                        <th class="px-6 py-3 hidden md:table-cell">Keterangan</th>
                        <th class="px-6 py-3 hidden lg:table-cell">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentAuditLogs as $log)
                    <tr class="text-slate-600 font-medium hover:bg-slate-50/50 transition">
                        <td class="px-6 py-3.5 text-xs text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-3.5 text-slate-900 font-semibold whitespace-nowrap">
                            {{ $log->user->name ?? 'System' }}
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-md border border-slate-200 whitespace-nowrap">
                                {{ $log->event }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 hidden md:table-cell text-xs text-slate-500 max-w-xs truncate">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-3.5 hidden lg:table-cell font-mono text-xs text-slate-400">
                            {{ $log->ip_address }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs font-medium">Belum ada log aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
