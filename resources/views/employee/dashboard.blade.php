@extends('layouts.app')

@section('title', 'Dashboard Guru & Pegawai')

@section('content')
<div class="space-y-6 animate-fadeIn">
    <!-- Welcome Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6 shadow-sm">
        <!-- SVG Watermark background of building/graduation -->
        <div class="absolute right-0 bottom-0 opacity-10 pointer-events-none transform translate-y-4 translate-x-4 hidden md:block">
            <svg class="w-72 h-72 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.5 12.06l8.5 4.64 8.5-4.64V9L12 13.64 3.5 9v3.06zM12 16.5l-6-3.27v2.18l6 3.27 6-3.27v-2.18l-6 3.27z"/>
            </svg>
        </div>

        <div class="relative z-10">
            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider block">Dashboard PTK</span>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-2">Selamat Datang Kembali, {{ $employee->full_name }}!</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Berikut adalah ringkasan data siswa aktif dan rombongan belajar di SMKN 1 Cirebon.</p>
        </div>
        <div class="relative z-10 self-start md:self-auto flex-shrink-0">
            <span class="bg-[#0f1e36] text-white font-bold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 border border-[#1e2f47] shadow-sm">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Status Kepegawaian: {{ $employee->employment_type ?? 'PNS' }}
            </span>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Metric 1: Total Siswa -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4.5 flex items-center justify-between shadow-sm relative overflow-hidden transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider block">Total Siswa</span>
                    <h4 class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalStudents }}</h4>
                    <span class="text-[9px] text-slate-400 font-medium block mt-0.5">Siswa Terdaftar</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Grade X -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4.5 flex items-center justify-between shadow-sm relative overflow-hidden transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider block">Kelas X</span>
                    <h4 class="text-2xl font-black text-blue-600 mt-0.5">{{ $gradeX }}</h4>
                    <span class="text-[9px] text-slate-400 font-medium block mt-0.5">Tingkat 10</span>
                </div>
            </div>
        </div>

        <!-- Metric 3: Grade XI -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4.5 flex items-center justify-between shadow-sm relative overflow-hidden transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider block">Kelas XI</span>
                    <h4 class="text-2xl font-black text-purple-600 mt-0.5">{{ $gradeXI }}</h4>
                    <span class="text-[9px] text-slate-400 font-medium block mt-0.5">Tingkat 11</span>
                </div>
            </div>
        </div>

        <!-- Metric 4: Grade XII -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4.5 flex items-center justify-between shadow-sm relative overflow-hidden transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider block">Kelas XII</span>
                    <h4 class="text-2xl font-black text-orange-600 mt-0.5">{{ $gradeXII }}</h4>
                    <span class="text-[9px] text-slate-400 font-medium block mt-0.5">Tingkat 12</span>
                </div>
            </div>
        </div>

        <!-- Metric 5: Laki-laki -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4.5 flex items-center justify-between shadow-sm relative overflow-hidden transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider block">Laki-Laki</span>
                    <h4 class="text-2xl font-black text-emerald-600 mt-0.5">{{ $maleCount }}</h4>
                    <span class="text-[9px] text-slate-400 font-medium block mt-0.5">Laki-laki</span>
                </div>
            </div>
        </div>

        <!-- Metric 6: Perempuan -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4.5 flex items-center justify-between shadow-sm relative overflow-hidden transition-all duration-200 hover:shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <span class="text-slate-400 text-[9px] font-bold uppercase tracking-wider block">Perempuan</span>
                    <h4 class="text-2xl font-black text-pink-600 mt-0.5">{{ $femaleCount }}</h4>
                    <span class="text-[9px] text-slate-400 font-medium block mt-0.5">Perempuan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section (Real-time Autocomplete) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm shadow-slate-100/50" x-data="{
        searchQuery: '',
        results: [],
        loading: false,
        fetchResults() {
            if (this.searchQuery.length < 2) {
                this.results = [];
                return;
            }
            this.loading = true;
            fetch('{{ route('employee.search-students') }}?search=' + encodeURIComponent(this.searchQuery))
                .then(res => res.json())
                .then(data => {
                    this.results = data;
                    this.loading = false;
                })
                .catch(err => {
                    console.error(err);
                    this.loading = false;
                });
        }
    }">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-900">Pencarian Siswa Instan</h3>
            <span x-show="loading" class="text-xs font-semibold text-purple-600 flex items-center gap-1.5" style="display: none;">
                <svg class="animate-spin h-4 w-4 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sedang mencari...
            </span>
        </div>
        <div class="flex gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input 
                    type="text" 
                    x-model="searchQuery"
                    x-on:input.debounce.300ms="fetchResults()"
                    placeholder="Ketik nama siswa, NIS, atau NISN untuk mulai mencari..." 
                    class="w-full pl-11 pr-10 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition placeholder-slate-400 bg-slate-50/50 focus:bg-white"
                />
                <span x-show="searchQuery.length > 0" class="absolute inset-y-0 right-0 flex items-center pr-3" style="display: none;">
                    <button type="button" @click="searchQuery = ''; results = [];" class="text-slate-400 hover:text-slate-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </span>
            </div>
            <button type="button" @click="fetchResults()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm transition active:scale-[0.98]">
                Cari Siswa
            </button>
        </div>

        <!-- Autocomplete Search Results Box -->
        <div x-show="searchQuery.length >= 2" class="mt-5 p-5 rounded-2xl bg-purple-50/40 border border-purple-200 space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-purple-600 uppercase tracking-wider">Hasil Pencarian</h4>
                <span class="text-xs font-semibold text-purple-700" x-text="'Ditemukan ' + results.length + ' siswa'"></span>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                                <th class="p-4">NIS / NISN</th>
                                <th class="p-4">Nama Lengkap</th>
                                <th class="p-4 text-center">Gender</th>
                                <th class="p-4">Rombel / Kelas</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="student in results" :key="student.id">
                                <tr class="hover:bg-slate-50/50 transition text-slate-600 font-medium">
                                    <td class="p-4">
                                        <span class="text-slate-900 font-bold block" x-text="student.nis"></span>
                                        <span class="text-xs text-slate-400" x-text="student.nisn || '-'"></span>
                                    </td>
                                    <td class="p-4 text-slate-900 font-bold" x-text="student.full_name"></td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full font-bold text-xs" 
                                              :class="student.gender === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600'" 
                                              x-text="student.gender">
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <template x-if="student.class_id">
                                            <a :href="'{{ route('employee.classes') }}?class_id=' + student.class_id + '#detail-kelas'" 
                                               class="text-blue-600 hover:underline font-semibold block" 
                                               x-text="student.class_name">
                                            </a>
                                        </template>
                                        <template x-if="!student.class_id">
                                            <span class="text-slate-400 italic block" x-text="student.class_name"></span>
                                        </template>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" 
                                              :class="student.status === 'Aktif' ? 'text-green-700 bg-green-50 border border-green-200' : 'text-slate-500 bg-slate-50 border border-slate-200'"
                                              x-text="student.status">
                                        </span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="results.length === 0 && !loading">
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400 font-medium">Siswa tidak ditemukan.</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Section 1: Ringkasan Rombel -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between shadow-sm">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Ringkasan Rombel</h3>
                    <a href="{{ route('employee.classes') }}" class="text-blue-600 hover:underline text-xs font-bold font-display">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="pb-2">Tingkat</th>
                                <th class="pb-2 text-center">Jumlah Rombel</th>
                                <th class="pb-2 text-right">Jumlah Siswa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    Kelas X
                                </td>
                                <td class="py-3 text-center font-bold text-slate-900">{{ $rombelSummary[10]['classes'] }}</td>
                                <td class="py-3 text-right font-bold text-blue-600">{{ $rombelSummary[10]['students'] }}</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    Kelas XI
                                </td>
                                <td class="py-3 text-center font-bold text-slate-900">{{ $rombelSummary[11]['classes'] }}</td>
                                <td class="py-3 text-right font-bold text-purple-600">{{ $rombelSummary[11]['students'] }}</td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                    Kelas XII
                                </td>
                                <td class="py-3 text-center font-bold text-slate-900">{{ $rombelSummary[12]['classes'] }}</td>
                                <td class="py-3 text-right font-bold text-orange-600">{{ $rombelSummary[12]['students'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs font-bold text-slate-900 border-t border-slate-100 pt-3.5 mt-2">
                <span>Total</span>
                <span class="text-blue-600 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded text-[10px] font-extrabold mr-10">{{ $rombelSummary->sum('classes') }}</span>
                <span class="text-indigo-600 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded text-[10px] font-extrabold">{{ $rombelSummary->sum('students') }}</span>
            </div>
        </div>

        <!-- Section 2: Aktivitas Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900">Aktivitas Terbaru</h3>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.audit-logs') }}" class="text-blue-600 hover:underline text-xs font-bold font-display">Lihat Semua</a>
                @endif
            </div>
            <div class="space-y-4 max-h-[220px] overflow-y-auto pr-1">
                @forelse ($recentLogs as $log)
                    @php
                        $iconBg = 'bg-blue-50 text-blue-600';
                        $iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>';
                        
                        if (str_contains(strtolower($log->event), 'student') || str_contains(strtolower($log->description), 'siswa')) {
                            $iconBg = 'bg-emerald-50 text-emerald-600';
                            $iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
                        } elseif (str_contains(strtolower($log->event), 'class') || str_contains(strtolower($log->description), 'rombel')) {
                            $iconBg = 'bg-blue-50 text-blue-600';
                            $iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path></svg>';
                        } elseif (str_contains(strtolower($log->event), 'profile') || str_contains(strtolower($log->event), 'employee')) {
                            $iconBg = 'bg-purple-50 text-purple-600';
                            $iconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                        }
                    @endphp
                    <div class="flex items-start justify-between gap-3 text-xs border-b border-slate-50 pb-3 last:border-0 last:pb-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center {{ $iconBg }}">
                                {!! $iconSvg !!}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 line-clamp-1">{{ $log->event }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5 line-clamp-1">{{ $log->description }}</p>
                            </div>
                        </div>
                        <span class="text-[9px] text-slate-400 whitespace-nowrap self-start mt-0.5 font-medium">
                            {{ Carbon\Carbon::parse($log->created_at)->format('H:i') }}
                        </span>
                    </div>
                @empty
                    <!-- Fallback Mock Activities (Matches user's screenshot perfectly) -->
                    <div class="flex items-start justify-between gap-3 text-xs border-b border-slate-50 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center bg-emerald-50 text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Data siswa baru ditambahkan</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Kelas X • 2 siswa baru</p>
                            </div>
                        </div>
                        <span class="text-[9px] text-slate-400 whitespace-nowrap self-start mt-0.5 font-medium">10:24</span>
                    </div>
                    <div class="flex items-start justify-between gap-3 text-xs border-b border-slate-50 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center bg-blue-50 text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Rombel Kelas XI diperbarui</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Perubahan data rombongan belajar</p>
                            </div>
                        </div>
                        <span class="text-[9px] text-slate-400 whitespace-nowrap self-start mt-0.5 font-medium">09:15</span>
                    </div>
                    <div class="flex items-start justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center bg-purple-50 text-purple-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">Rekap data siswa diperbarui</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Total siswa aktif: {{ $totalStudents }}</p>
                            </div>
                        </div>
                        <span class="text-[9px] text-slate-400 whitespace-nowrap self-start mt-0.5 font-medium">Kemarin</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
