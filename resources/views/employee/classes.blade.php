@extends('layouts.app')

@section('title', 'Daftar Rombongan Belajar - WargaRPL')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Daftar Rombongan Belajar (Kelas)</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Gunakan penyaringan di bawah untuk menyaring rombel secara cepat.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold bg-slate-950 text-white px-3 py-1.5 rounded-lg border border-slate-800">
                Total: {{ $classesWithCount->count() }} Rombel
            </span>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <form method="GET" action="{{ route('employee.classes') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search class name -->
            <div>
                <label for="search_class" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Rombel / Kelas</label>
                <input 
                    type="text" 
                    id="search_class" 
                    name="search_class" 
                    value="{{ $searchClass }}" 
                    placeholder="Contoh: PPLG 1..." 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition placeholder-slate-400 bg-slate-50/50 focus:bg-white"
                />
            </div>

            <!-- Grade Level Filter -->
            <div>
                <label for="grade" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tingkat Kelas</label>
                <select 
                    id="grade" 
                    name="grade" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white"
                >
                    <option value="">Semua Tingkat</option>
                    <option value="10" {{ $grade == '10' ? 'selected' : '' }}>Tingkat 10 (X)</option>
                    <option value="11" {{ $grade == '11' ? 'selected' : '' }}>Tingkat 11 (XI)</option>
                    <option value="12" {{ $grade == '12' ? 'selected' : '' }}>Tingkat 12 (XII)</option>
                </select>
            </div>

            <!-- Major Filter -->
            <div>
                <label for="major_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Kompetensi Keahlian (Jurusan)</label>
                <select 
                    id="major_id" 
                    name="major_id" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white"
                >
                    <option value="">Semua Jurusan</option>
                    @foreach ($majors as $major)
                        <option value="{{ $major->id }}" {{ $majorId == $major->id ? 'selected' : '' }}>{{ $major->name }} ({{ $major->code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <a href="{{ route('employee.classes') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition inline-block">
                    Reset
                </a>
                <button type="submit" class="px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex-1">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Selected Class Detail Roster -->
    @if ($selectedClass)
        @php
            $classMaleCount = $classMembers->filter(fn($m) => $m->student && $m->student->gender === 'L')->count();
            $classFemaleCount = $classMembers->filter(fn($m) => $m->student && $m->student->gender === 'P')->count();
        @endphp
        <div id="detail-kelas" class="bg-blue-50/30 rounded-2xl border border-blue-200 p-6 space-y-4 animate-fadeIn">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full uppercase">Detail Rombel</span>
                    <h3 class="text-xl font-bold text-slate-900 mt-2">Daftar Siswa Kelas {{ $selectedClass->name }}</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">Jurusan: {{ $selectedClass->major->name }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2.5 py-0.5 bg-blue-50/50 text-blue-700 border border-blue-100 rounded-full text-[10px] font-bold">Laki-laki: {{ $classMaleCount }}</span>
                        <span class="px-2.5 py-0.5 bg-pink-50/50 text-pink-700 border border-pink-100 rounded-full text-[10px] font-bold">Perempuan: {{ $classFemaleCount }}</span>
                    </div>
                </div>
                <a href="{{ route('employee.classes', request()->except('class_id')) }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-sm transition inline-flex items-center gap-1.5 self-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Tutup Detail
                </a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                                <th class="p-4 w-12 text-center">No</th>
                                <th class="p-4">NIS / NISN</th>
                                <th class="p-4">Nama Lengkap</th>
                                <th class="p-4 text-center">Gender</th>
                                <th class="p-4">Kontak</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($classMembers as $idx => $member)
                                <tr class="hover:bg-slate-50/50 transition text-slate-600 font-medium">
                                    <td class="p-4 text-center text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                    <td class="p-4">
                                        <span class="text-slate-900 font-bold block">{{ $member->student->nis }}</span>
                                        <span class="text-xs text-slate-400">{{ $member->student->nisn ?? '-' }}</span>
                                    </td>
                                    <td class="p-4 text-slate-900 font-bold">{{ $member->student->full_name }}</td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full font-bold text-xs {{ $member->student->gender === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}" title="{{ $member->student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}">
                                            {{ $member->student->gender }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span class="block text-xs font-semibold text-slate-800">{{ $member->student->phone ?? '-' }}</span>
                                        <span class="block text-[10px] text-slate-400 font-medium">{{ $member->student->email ?? '-' }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $member->student->status === 'Aktif' ? 'text-green-700 bg-green-50 border border-green-200' : 'text-slate-500 bg-slate-50 border border-slate-200' }}">
                                            {{ $member->student->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 font-medium">Tidak ada siswa aktif terdaftar di rombel/kelas ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Classes Grid List -->
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse ($classesWithCount as $class)
                @php
                    $isSelected = $selectedClass && $selectedClass->id == $class->id;
                @endphp
                <div class="bg-white rounded-2xl border transition-all shadow-sm p-5 flex flex-col justify-between gap-4 {{ $isSelected ? 'border-blue-600 ring-1 ring-blue-600' : 'border-slate-200 hover:border-slate-350' }}">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <h4 class="text-lg font-bold text-slate-950">{{ $class->name }}</h4>
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-800 rounded-full text-[10px] font-extrabold border border-slate-200">
                                {{ $class->student_count }} Siswa
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Jurusan: {{ $class->major->name }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">Tingkat: Kelas {{ $class->grade_level }}</p>
                    </div>
                    
                    <a href="{{ route('employee.classes', ['class_id' => $class->id] + request()->except('class_id')) }}#detail-kelas" class="w-full text-center py-2 rounded-xl text-xs font-bold transition shadow-sm {{ $isSelected ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-slate-900 hover:bg-slate-800 text-white' }}">
                        {{ $isSelected ? 'Melihat Rincian' : 'Lihat Detail Rombel' }}
                    </a>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400 text-sm font-semibold bg-slate-50/50 border border-slate-200 border-dashed rounded-2xl">
                    Tidak ada rombel/kelas yang cocok dengan kriteria filter pencarian Anda.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
