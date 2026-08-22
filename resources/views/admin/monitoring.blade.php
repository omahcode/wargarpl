@extends('layouts.app')

@section('title', 'Monitoring Perbaikan Data')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Monitoring Perbaikan Data</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Lacak progress verifikasi mandiri siswa per Rombongan Belajar.</p>
        </div>
    </div>

    <!-- Campaign Header Status -->
    @if ($campaign)
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <span class="text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-full uppercase tracking-wider">Campaign</span>
                <h3 class="text-base font-bold text-slate-900 mt-2">{{ $campaign->title }}</h3>
                <p class="text-xs text-slate-500 mt-1">Periode aktif: {{ Carbon\Carbon::parse($campaign->start_date)->translatedFormat('d M Y') }} s.d. {{ Carbon\Carbon::parse($campaign->end_date)->translatedFormat('d M Y') }}</p>
            </div>
            <div class="flex flex-col text-left sm:text-right font-medium">
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Status Kampanye</span>
                <span class="text-sm text-slate-900 font-semibold mt-1">
                    {{ $campaign->is_active ? 'Aktif' : 'Non-aktif' }}
                </span>
            </div>
        </div>

        <!-- Class Breakdown -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                            <th class="p-4">Kelas / Rombel</th>
                            <th class="p-4">Total Siswa</th>
                            <th class="p-4">Sudah Verifikasi</th>
                            <th class="p-4">Belum Verifikasi</th>
                            <th class="p-4">Progress (%)</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $grandTotal = 0;
                            $grandVerified = 0;
                            $grandUnverified = 0;
                        @endphp
                        @forelse ($classStats as $stat)
                            @php
                                $total = $stat->total_students;
                                $verified = $stat->verified_count;
                                $belum = $stat->unverified_count;
                                $percent = $total > 0 ? round(($verified / $total) * 100) : 0;
                                
                                $grandTotal += $total;
                                $grandVerified += $verified;
                                $grandUnverified += $belum;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition text-slate-600 font-medium">
                                <td class="p-4 text-slate-950 font-bold">{{ $stat->name }}</td>
                                <td class="p-4">{{ $total }}</td>
                                <td class="p-4 text-green-600 font-semibold">{{ $verified }}</td>
                                <td class="p-4 text-amber-600 font-semibold">{{ $belum }}</td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 bg-slate-100 h-2 rounded-full overflow-hidden border">
                                            <div class="bg-slate-900 h-full rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-800 font-bold">{{ $percent }}%</span>
                                    </div>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('admin.students.index', ['class_id' => $stat->id]) }}" class="text-slate-500 hover:text-slate-900 transition text-xs font-bold inline-flex items-center gap-0.5">
                                        Lihat Kelas &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 font-semibold">Belum ada statistik kelas tersedia.</td>
                            </tr>
                        @endforelse
                        <!-- Grand Total row -->
                        @if (count($classStats) > 0)
                            <tr class="bg-slate-900 text-white font-bold border-t border-slate-800">
                                <td class="p-4 text-white">TOTAL SEKOLAH</td>
                                <td class="p-4">{{ $grandTotal }}</td>
                                <td class="p-4 text-green-400">{{ $grandVerified }}</td>
                                <td class="p-4 text-amber-400">{{ $grandUnverified }}</td>
                                <td class="p-4" colspan="2">
                                    @php
                                        $grandPercent = $grandTotal > 0 ? round(($grandVerified / $grandTotal) * 100) : 0;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-32 bg-slate-800 h-2.5 rounded-full overflow-hidden border border-slate-700">
                                            <div class="bg-white h-full rounded-full" style="width: {{ $grandPercent }}%"></div>
                                        </div>
                                        <span class="text-xs text-white font-extrabold">{{ $grandPercent }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-sm text-slate-400 font-medium">
            Tidak ada campaign verifikasi yang sedang aktif saat ini.
        </div>
    @endif
</div>
@endsection
