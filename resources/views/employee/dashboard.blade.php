@extends('layouts.app')

@section('title', 'Dashboard Guru / Pegawai')

@section('content')
<div class="space-y-8" x-data="{ tab: 'diri' }">
    <!-- Welcome Header -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 shadow-sm">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dashboard PTK</span>
            <h2 class="text-2xl font-bold text-slate-900 mt-1">{{ $employee->full_name }}</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">NIP: {{ $employee->nip ?? '-' }} &bull; Jenis PTK: {{ $employee->ptk_type ?? '-' }}</p>
        </div>
        <div>
            <span class="text-xs font-bold bg-slate-900 text-white px-3 py-1.5 rounded-lg border border-slate-800">
                Status: {{ $employee->employment_type ?? '-' }} ({{ $employee->status }})
            </span>
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
            <button @click="tab = 'security'" :class="tab === 'security' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Keamanan & Password
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
                <form method="POST" action="{{ route('employee.update') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. HP Pegawai</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="whatsapp" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. WhatsApp</label>
                            <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $employee->whatsapp) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Pegawai</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Rumah Lengkap</label>
                            <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition">{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow-sm transition active:scale-[0.98]">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 4. Keamanan & Password -->
            <div x-show="tab === 'security'" class="space-y-6" style="display: none;">
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Ubah Password Akun</h3>
                    <p class="text-xs text-slate-500 mt-1">Gunakan form di bawah ini untuk memperbarui password default Anda demi menjaga keamanan akun.</p>
                </div>
                
                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-xs font-semibold text-red-600 max-w-md">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('change-password') }}" class="max-w-md space-y-4">
                    @csrf
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50" />
                    </div>
                    <div>
                        <label for="new_password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50" />
                        <p class="text-[10px] text-slate-400 mt-1">Minimal 8 karakter.</p>
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50" />
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Grid: Homeroom and Change Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Homeroom classes -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
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
                    <div class="text-center py-8 text-slate-400 text-xs font-medium">Anda tidak terdaftar sebagai wali kelas untuk rombel aktif.</div>
                @endforelse
            </div>
        </div>

        <!-- Employee change logs -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Perubahan Data Kontak</h3>
            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                @forelse ($changeLogs as $log)
                    <div class="text-xs p-3 rounded-xl border border-slate-100 bg-slate-50/50 flex justify-between gap-4">
                        <div>
                            <span class="font-bold text-slate-700 capitalize">{{ str_replace('_', ' ', $log->field_name) }}</span>
                            <div class="mt-1 flex items-center gap-1.5 text-slate-400">
                                <span class="line-through">{{ $log->old_value ?? '(Kosong)' }}</span>
                                <span>&rarr;</span>
                                <span class="text-slate-800 font-medium">{{ $log->new_value ?? '(Kosong)' }}</span>
                            </div>
                        </div>
                        <div class="text-right text-[10px] text-slate-400 font-medium flex-shrink-0">
                            {{ Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                            <div class="mt-1 font-mono text-[9px]">{{ $log->ip_address }}</div>
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
