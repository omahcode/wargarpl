@extends('layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Master Data Siswa</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Daftar siswa SMKN 1 Cirebon dan status campaign verifikasi aktif.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.students.delete-all') }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Kosongkan Semua Siswa', message: 'Apakah Anda yakin ingin menghapus SELURUH data siswa dan akun login terkait secara permanen? Tindakan ini tidak dapat dibatalkan.', type: 'danger' }, () => this.submit());">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                    Kosongkan Semua Siswa
                </button>
            </form>
            <a href="{{ route('admin.students.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Tambah Siswa
            </a>
            <a href="{{ route('admin.students.import-export-ui') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Import / Export Excel
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pencarian</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari NIS, NISN, atau Nama Siswa..." 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition placeholder-slate-400 bg-slate-50/50 focus:bg-white"
                />
            </div>

            <!-- Class Filter -->
            <div>
                <label for="class_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Rombel</label>
                <select 
                    id="class_id" 
                    name="class_id" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white"
                >
                    <option value="">Semua Rombel</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ $classFilter == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Major Filter -->
            <div>
                <label for="major_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Jurusan</label>
                <select 
                    id="major_id" 
                    name="major_id" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white"
                >
                    <option value="">Semua Jurusan</option>
                    @foreach ($majors as $major)
                        <option value="{{ $major->id }}" {{ $majorFilter == $major->id ? 'selected' : '' }}>{{ $major->code }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Verification Filter -->
            <div>
                <label for="verification_status" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Status Verifikasi</label>
                <select 
                    id="verification_status" 
                    name="verification_status" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white"
                >
                    <option value="">Semua Status</option>
                    <option value="Belum diperiksa" {{ $verificationFilter == 'Belum diperiksa' ? 'selected' : '' }}>Belum diperiksa</option>
                    <option value="Sudah diperiksa tanpa perubahan" {{ $verificationFilter == 'Sudah diperiksa tanpa perubahan' ? 'selected' : '' }}>Sudah diperiksa (OK)</option>
                    <option value="Sudah diperbaiki" {{ $verificationFilter == 'Sudah diperbaiki' ? 'selected' : '' }}>Sudah diperbaiki</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="sm:col-span-2 lg:col-span-5 flex justify-end gap-2 pt-2 border-t border-slate-100 mt-2">
                <a href="{{ route('admin.students.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Reset
                </a>
                <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Student Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">NIS / NISN</th>
                        <th class="p-4">Nama Lengkap</th>
                        <th class="p-4">JK</th>
                        <th class="p-4">Rombel Aktif</th>
                        <th class="p-4">Verifikasi Campaign</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $student)
                        <tr class="hover:bg-slate-50/50 transition text-slate-600 font-medium">
                            <td class="p-4">
                                <span class="text-slate-900 font-bold block">{{ $student->nis }}</span>
                                <span class="text-xs text-slate-400">{{ $student->nisn ?? '-' }}</span>
                            </td>
                            <td class="p-4 text-slate-950 font-semibold">{{ $student->full_name }}</td>
                            <td class="p-4">{{ $student->gender }}</td>
                            <td class="p-4 text-slate-900 font-semibold">{{ $student->class_name ?? 'Belum ada kelas' }}</td>
                            <td class="p-4">
                                @php
                                    $campaign = \App\Models\VerificationCampaign::where('is_active', true)->first();
                                    $verif = null;
                                    if ($campaign) {
                                        $verif = DB::table('student_verifications')
                                            ->where('student_id', $student->id)
                                            ->where('campaign_id', $campaign->id)
                                            ->first();
                                    }
                                @endphp
                                @if($verif)
                                    @php
                                        $badgeClass = match($verif->status) {
                                            'Belum diperiksa'                => 'text-amber-700 bg-amber-50 border-amber-200',
                                            'Menunggu Validasi Admin'        => 'text-blue-700 bg-blue-50 border-blue-200',
                                            'Sudah diperiksa tanpa perubahan'=> 'text-emerald-700 bg-emerald-50 border-emerald-200',
                                            'Sudah diperbaiki'               => 'text-indigo-700 bg-indigo-50 border-indigo-200',
                                            default                          => 'text-slate-600 bg-slate-50 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full border {{ $badgeClass }}">
                                        @if($verif->status === 'Menunggu Validasi Admin')
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        @endif
                                        {{ $verif->status }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.students.show', $student->uuid) }}" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.students.edit', $student->uuid) }}" class="px-2 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.students.destroy', $student->uuid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Hapus Siswa', message: 'Apakah Anda yakin ingin menghapus siswa ini? Akun login terkait juga akan dihapus.', type: 'danger' }, () => this.submit());">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-bold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 font-medium">Tidak ada data siswa ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
