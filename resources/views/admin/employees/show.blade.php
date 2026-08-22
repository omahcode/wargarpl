@extends('layouts.app')

@section('title', 'Detail Guru / Pegawai - ' . $employee->full_name)

@section('content')
<div class="space-y-8" x-data="{ tab: 'diri' }">
    <!-- Header Back -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.employees.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 transition">
                &larr; Kembali ke Daftar Guru & Tendik
            </a>
            <h2 class="text-2xl font-bold text-slate-900 mt-2">{{ $employee->full_name }}</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">NIP: {{ $employee->nip ?? '-' }} &bull; Jenis PTK: {{ $employee->ptk_type ?? '-' }}</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('admin.employees.reset-password', $employee->uuid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Reset Password Guru/Tendik', message: 'Apakah Anda yakin ingin mereset password akun guru/tendik ini kembali ke bawaan (Tanggal Lahir)?', type: 'info' }, () => this.submit());">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                    Reset Password
                </button>
            </form>
            <a href="{{ route('admin.employees.edit', $employee->uuid) }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Edit Guru/Tendik
            </a>
            <form action="{{ route('admin.employees.destroy', $employee->uuid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Hapus Guru/Tendik', message: 'Apakah Anda yakin ingin menghapus data Guru/Tendik ini? Akun login terkait juga akan dihapus.', type: 'danger' }, () => this.submit());">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                    Hapus Guru/Tendik
                </button>
            </form>
        </div>
    </div>

    <!-- Main Section Tabs -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Tab Headers -->
        <div class="flex border-b border-slate-100 overflow-x-auto bg-slate-50/50">
            <button @click="tab = 'diri'" :class="tab === 'diri' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Data Diri & Kepegawaian
            </button>
            <button @click="tab = 'akademik'" :class="tab === 'akademik' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Riwayat Akademik
            </button>
            <button @click="tab = 'kontak'" :class="tab === 'kontak' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Kontak & Alamat
            </button>
        </div>

        <!-- Tab Contents -->
        <div class="p-6 sm:p-8">
            <!-- Tab 1. Data Diri & Kepegawaian -->
            <div x-show="tab === 'diri'" class="space-y-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Identitas & Kepegawaian</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">Nama Lengkap</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->full_name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">NIP / NIK</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->nip ?? '-' }} / {{ $employee->nik ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">NUPTK</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->nuptk ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Tempat / Tanggal Lahir</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->birth_place ?? '-' }}, {{ $employee->birth_date ? Carbon\Carbon::parse($employee->birth_date)->translatedFormat('d F Y') : '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Jenis Kelamin</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Agama</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->religion ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Status Kepegawaian</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->employment_type ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Jenis PTK</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->ptk_type ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Tab 2. Riwayat Akademik -->
            <div x-show="tab === 'akademik'" class="space-y-6" style="display: none;">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pendidikan & Bidang Ajaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">Pendidikan Terakhir</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->last_education ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Program Studi</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->study_program ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Universitas / Perguruan Tinggi</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->university ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Tahun Kelulusan</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->graduation_year ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Mata Pelajaran Utama</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->primary_subject ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kompetensi Keahlian (Keahlian Ajar)</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->major_expertise ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Tab 3. Kontak & Alamat -->
            <div x-show="tab === 'kontak'" class="space-y-6" style="display: none;">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Alamat & Kontak</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">No. HP / WhatsApp Pegawai</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->phone ?? '-' }} / {{ $employee->whatsapp ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Email Pegawai</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->email ?? '-' }}</strong>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-slate-400 block mb-1">Alamat Rumah Lengkap</span>
                        <strong class="text-slate-900 font-semibold">{{ $employee->address ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid: Homeroom and Change Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Homeroom classes -->
        <div class="bg-white border border-slate-200 shadow-sm p-6 rounded-2xl">
            <h3 class="text-base font-bold text-slate-900 mb-4">Wali Kelas Untuk Rombel</h3>
            <div class="space-y-3">
                @forelse ($homeroomClasses as $class)
                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">{{ $class->name }}</h4>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Jurusan: {{ $class->major->name }}</p>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 bg-slate-200 px-3 py-1 rounded-full">
                            Grade: {{ $class->grade_level }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-medium">Pegawai tidak terdaftar sebagai wali kelas rombel aktif.</div>
                @endforelse
            </div>
        </div>

        <!-- Employee change logs -->
        <div class="bg-white border border-slate-200 shadow-sm p-6 rounded-2xl">
            <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Perubahan Data Kontak</h3>
            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                @forelse ($changeLogs as $log)
                    <div class="text-xs p-3.5 bg-slate-50 border border-slate-100 rounded-xl flex justify-between gap-4">
                        <div>
                            <span class="font-bold text-slate-955 capitalize">{{ str_replace('_', ' ', $log->field_name) }}</span>
                            <div class="mt-1 flex items-center gap-1.5 text-slate-400">
                                <span class="line-through">{{ $log->old_value ?? '(Kosong)' }}</span>
                                <span>&rarr;</span>
                                <span class="text-slate-900 font-semibold">{{ $log->new_value ?? '(Kosong)' }}</span>
                            </div>
                        </div>
                        <div class="text-right text-[10px] text-slate-400 font-medium flex-shrink-0">
                            {{ Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                            <div class="mt-0.5 font-mono text-[9px]">{{ $log->ip_address }}</div>
                            <div class="mt-0.5 text-slate-500 font-semibold">Oleh: {{ $log->modifiedBy->name ?? 'Pegawai' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-medium">Belum ada riwayat perubahan kontak.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
