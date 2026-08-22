@extends('layouts.app')

@section('title', 'Tahun Ajaran & Semester - SobatData Neper')

@section('content')
<div class="space-y-6" x-data="{ showAddModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Periode Akademik</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Kelola dan aktifkan Tahun Ajaran dan Semester aktif untuk verifikasi data mandiri.</p>
        </div>
        <button @click="showAddModal = true" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
            Tambah Tahun Ajaran
        </button>
    </div>

    <!-- Active Indicator Banner -->
    @php
        $activeYear = $schoolYears->where('is_active', true)->first();
        $activeSemester = null;
        if ($activeYear) {
            $activeSemester = $activeYear->semesters->where('is_active', true)->first();
        }
    @endphp
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1">
            <span class="text-[10px] font-bold bg-white/20 px-2.5 py-1 rounded-full uppercase tracking-wider">Periode Aktif Saat Ini</span>
            <h3 class="text-xl font-bold">
                @if($activeYear && $activeSemester)
                    Tahun Ajaran {{ $activeYear->name }} &bull; Semester {{ $activeSemester->semester }}
                @else
                    Belum ada periode akademik yang diaktifkan!
                @endif
            </h3>
            <p class="text-xs text-blue-100 font-medium">Semua transaksi data, rombel baru, dan login siswa akan didasarkan pada periode aktif ini.</p>
        </div>
        @if(!$activeYear || !$activeSemester)
            <div class="px-4 py-2 bg-amber-500 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 border border-amber-400">
                <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                Sistem Butuh Periode Aktif
            </div>
        @endif
    </div>

    <!-- Grid of School Years -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($schoolYears as $year)
            <div class="bg-white border {{ $year->is_active ? 'border-blue-500 ring-1 ring-blue-500/20' : 'border-slate-200' }} rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
                <!-- Card Header -->
                <div class="p-6 pb-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-slate-900">Tahun Ajaran {{ $year->name }}</h4>
                        <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">Dibuat: {{ $year->created_at->translatedFormat('d M Y') }}</span>
                    </div>
                    @if($year->is_active)
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 text-xs font-bold rounded-full">
                            Aktif
                        </span>
                    @else
                        <form action="{{ route('admin.periods.school-year.activate', $year->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold rounded-full transition">
                                Aktifkan
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Card Body: Semesters -->
                <div class="p-6 space-y-4 flex-1">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Semester</p>
                    <div class="space-y-3">
                        @foreach($year->semesters as $sem)
                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl">
                                <div>
                                    <span class="text-sm font-bold text-slate-800">Semester {{ $sem->semester }}</span>
                                    @if($sem->is_active)
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block ml-1" title="Aktif"></span>
                                    @endif
                                </div>
                                <div>
                                    @if($sem->is_active)
                                        <span class="text-xs font-bold text-green-600 bg-green-50 border border-green-200 px-2 py-0.5 rounded-md">
                                            Aktif
                                        </span>
                                    @else
                                        <form action="{{ route('admin.periods.semester.activate', $sem->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Card Footer -->
                @if(!$year->is_active)
                    <div class="p-4 border-t border-slate-100 bg-slate-50/20 flex justify-end">
                        <form action="{{ route('admin.periods.school-year.destroy', $year->id) }}" method="POST"
                            onsubmit="event.preventDefault(); confirmAction({ title: 'Hapus Tahun Ajaran', message: 'Apakah Anda yakin ingin menghapus Tahun Ajaran ini beserta Semesternya? Tindakan ini tidak dapat dibatalkan.', type: 'danger' }, () => this.submit());">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-600 hover:text-red-800 transition">
                                Hapus Tahun Ajaran
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full bg-white border border-slate-200 rounded-2xl p-12 text-center">
                <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-sm font-semibold text-slate-400">Belum ada tahun ajaran terdaftar.</p>
                <p class="text-xs text-slate-300 mt-1">Silakan tambah tahun ajaran baru terlebih dahulu.</p>
            </div>
        @endforelse
    </div>

    <!-- Modal: Tambah Tahun Ajaran -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300" style="display: none;">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden animate-scale-up" @click.away="showAddModal = false">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Tambah Tahun Ajaran</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('admin.periods.school-year.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Tahun Ajaran</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        placeholder="Contoh: 2027/2028" 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition placeholder-slate-400 bg-slate-50/50 focus:bg-white"
                    />
                    <p class="text-[10px] text-slate-400 mt-1.5">Gunakan format tahun mulai/tahun selesai, misal 2027/2028.</p>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
