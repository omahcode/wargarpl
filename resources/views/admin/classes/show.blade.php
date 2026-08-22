@extends('layouts.app')

@section('title', 'Detail Rombel: {{ $class->name }} - SIDATA NEPER')

@section('content')
<div class="space-y-6">
    <!-- Back Button + Header -->
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-900 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Rombel
        </a>
    </div>

    <!-- Class Info Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Left: Class Identity -->
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black text-lg shadow-sm shrink-0">
                    {{ $class->grade_level }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 font-display">{{ $class->name }}</h2>
                    <p class="text-sm text-slate-500 font-medium mt-0.5">
                        {{ $class->major->name }} &bull; {{ $class->major->code }}
                    </p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="text-xs font-bold px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                            Tingkat {{ $class->grade_level }}
                        </span>
                        <span class="text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full border border-slate-200">
                            {{ $class->schoolYear->name ?? 'Tahun Ajaran Tidak Diketahui' }}
                        </span>
                        <span class="text-xs font-bold px-2.5 py-1 {{ $class->status ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }} rounded-full border">
                            {{ $class->status ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Stats -->
            <div class="grid grid-cols-2 gap-3 shrink-0">
                <div class="text-center px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="text-2xl font-black text-slate-900">{{ $members->count() }}</div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-0.5">Total Siswa</div>
                </div>
                <div class="text-center px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="text-base font-bold text-slate-900 truncate max-w-[120px]">{{ $class->homeroomTeacher->full_name ?? '-' }}</div>
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-0.5">Wali Kelas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Daftar Anggota Kelas</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $members->count() }} siswa terdaftar di kelas ini.</p>
            </div>
            <a href="{{ route('admin.students.index', ['class_id' => $class->id]) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                Lihat di Master Data →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/30 border-b border-slate-100">
                        <th class="px-6 py-3">No. Absen</th>
                        <th class="px-6 py-3">NIS / NISN</th>
                        <th class="px-6 py-3">Nama Lengkap</th>
                        <th class="px-6 py-3">JK</th>
                        <th class="px-6 py-3">Tempat, Tgl Lahir</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($members as $member)
                        @php $s = $member->student; @endphp
                        <tr class="hover:bg-slate-50/50 transition text-slate-600 font-medium">
                            <td class="px-6 py-3.5 text-center">
                                <span class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs inline-flex items-center justify-center">
                                    {{ $loop->iteration }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                @if($s)
                                    <span class="text-slate-900 font-bold block text-sm">{{ $s->nis }}</span>
                                    <span class="text-xs text-slate-400">{{ $s->nisn ?? '-' }}</span>
                                @else
                                    <span class="text-slate-400 text-xs italic">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                @if($s)
                                    <div>
                                        <span class="text-slate-900 font-semibold">{{ $s->full_name }}</span>
                                        @if($s->nickname)
                                            <span class="text-xs text-slate-400 font-medium"> ({{ $s->nickname }})</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                @if($s)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $s->gender === 'L' ? 'bg-sky-50 text-sky-700 border border-sky-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                                        {{ $s->gender === 'L' ? 'L' : 'P' }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                @if($s && $s->birth_date)
                                    <span class="text-slate-700 font-medium">
                                        {{ $s->birth_place ? $s->birth_place . ', ' : '' }}{{ \Carbon\Carbon::parse($s->birth_date)->translatedFormat('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                @if($s)
                                    @php
                                        $statusClasses = [
                                            'Aktif'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Lulus'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'Keluar'    => 'bg-red-50 text-red-700 border-red-200',
                                            'Pindah'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'Alumni'    => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        ];
                                        $sc = $statusClasses[$s->status] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $sc }}">
                                        {{ $s->status }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                @if($s)
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.students.show', $s->uuid) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.students.edit', $s->uuid) }}" class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition">
                                            Edit
                                        </a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"></path></svg>
                                    <p class="text-sm font-semibold text-slate-400">Belum ada siswa di kelas ini.</p>
                                    <p class="text-xs text-slate-300">Siswa akan muncul saat import Dapodik atau tambah manual.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Gender Distribution Card -->
    @if($members->count() > 0)
    @php
        $maleCount = $members->filter(fn($m) => $m->student?->gender === 'L')->count();
        $femaleCount = $members->filter(fn($m) => $m->student?->gender === 'P')->count();
        $total = $members->count();
        $malePct = $total > 0 ? round(($maleCount / $total) * 100) : 0;
        $femalePct = $total > 0 ? round(($femaleCount / $total) * 100) : 0;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Laki-laki</p>
            <p class="text-3xl font-black text-sky-600 mt-1">{{ $maleCount }}</p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3">
                <div class="bg-sky-500 h-1.5 rounded-full" style="width: {{ $malePct }}%"></div>
            </div>
            <p class="text-xs text-slate-400 font-medium mt-1.5">{{ $malePct }}% dari total</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perempuan</p>
            <p class="text-3xl font-black text-rose-500 mt-1">{{ $femaleCount }}</p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3">
                <div class="bg-rose-400 h-1.5 rounded-full" style="width: {{ $femalePct }}%"></div>
            </div>
            <p class="text-xs text-slate-400 font-medium mt-1.5">{{ $femalePct }}% dari total</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Anggota</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ $total }}</p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3">
                <div class="bg-slate-600 h-1.5 rounded-full" style="width: 100%"></div>
            </div>
            <p class="text-xs text-slate-400 font-medium mt-1.5">Siswa terdaftar di rombel ini</p>
        </div>
    </div>
    @endif
</div>
@endsection
