@extends('layouts.app')

@section('title', 'Profil Guru / Pegawai')

@section('content')
<div class="space-y-8" x-data="{ tab: 'diri' }">
    <!-- Header -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 shadow-sm">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Profil Pegawai / PTK</span>
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
            <form method="POST" action="{{ route('employee.update') }}" class="space-y-6">
                @csrf
                <!-- Tab 1. Data Diri & Kepegawaian -->
                <div x-show="tab === 'diri'" class="space-y-6">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Identitas & Kepegawaian</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <label for="full_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $employee->full_name) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="nip" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIP</label>
                            <input type="text" id="nip" name="nip" value="{{ old('nip', $employee->nip) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIK</label>
                            <input type="text" id="nik" name="nik" value="{{ old('nik', $employee->nik) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="nuptk" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NUPTK</label>
                            <input type="text" id="nuptk" name="nuptk" value="{{ old('nuptk', $employee->nuptk) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="birth_place" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tempat Lahir</label>
                            <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $employee->birth_place) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Lahir</label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $employee->birth_date) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="gender" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Kelamin *</label>
                            <select id="gender" name="gender" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                                <option value="L" {{ old('gender', $employee->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender', $employee->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label for="religion" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Agama</label>
                            <select id="religion" name="religion" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                                <option value="" disabled {{ is_null(old('religion', $employee->religion)) ? 'selected' : '' }}>Pilih Agama</option>
                                <option value="Islam" {{ old('religion', $employee->religion) === 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('religion', $employee->religion) === 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katolik" {{ old('religion', $employee->religion) === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                <option value="Hindu" {{ old('religion', $employee->religion) === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Buddha" {{ old('religion', $employee->religion) === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                <option value="Khonghucu" {{ old('religion', $employee->religion) === 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                            </select>
                        </div>
                        <div>
                            <label for="employment_type" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Kepegawaian</label>
                            <select id="employment_type" name="employment_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                                <option value="PNS" {{ old('employment_type', $employee->employment_type) === 'PNS' ? 'selected' : '' }}>PNS</option>
                                <option value="PPPK" {{ old('employment_type', $employee->employment_type) === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                <option value="Non ASN" {{ old('employment_type', $employee->employment_type) === 'Non ASN' ? 'selected' : '' }}>Non ASN</option>
                                <option value="Honor Sekolah" {{ old('employment_type', $employee->employment_type) === 'Honor Sekolah' ? 'selected' : '' }}>Honor Sekolah</option>
                            </select>
                        </div>
                        <div>
                            <label for="ptk_type" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis PTK</label>
                            <select id="ptk_type" name="ptk_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                                @foreach($ptkTypes as $type)
                                    <option value="{{ $type->name }}" {{ old('ptk_type', $employee->ptk_type) === $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tab 2. Riwayat Akademik -->
                <div x-show="tab === 'akademik'" class="space-y-6" style="display: none;">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pendidikan & Bidang Ajaran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <label for="last_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pendidikan Terakhir</label>
                            <input type="text" id="last_education" name="last_education" value="{{ old('last_education', $employee->last_education) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="study_program" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Program Studi</label>
                            <input type="text" id="study_program" name="study_program" value="{{ old('study_program', $employee->study_program) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="university" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Universitas / Perguruan Tinggi</label>
                            <input type="text" id="university" name="university" value="{{ old('university', $employee->university) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="graduation_year" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Kelulusan</label>
                            <input type="number" id="graduation_year" name="graduation_year" value="{{ old('graduation_year', $employee->graduation_year) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="primary_subject" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Mata Pelajaran Utama</label>
                            <input type="text" id="primary_subject" name="primary_subject" value="{{ old('primary_subject', $employee->primary_subject) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="major_expertise" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kompetensi Keahlian (Keahlian Ajar)</label>
                            <input type="text" id="major_expertise" name="major_expertise" value="{{ old('major_expertise', $employee->major_expertise) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                    </div>
                </div>

                <!-- Tab 3. Kontak & Alamat -->
                <div x-show="tab === 'kontak'" class="space-y-6" style="display: none;">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Kontak & Alamat Rumah</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. HP Pegawai</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="whatsapp" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. WhatsApp</label>
                            <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $employee->whatsapp) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Pegawai</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Rumah Lengkap</label>
                            <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button shown on editable tabs -->
                <div x-show="tab === 'diri' || tab === 'akademik' || tab === 'kontak'" class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow-sm transition active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

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

    <!-- Employee change logs -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Perubahan Profil</h3>
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
                <div class="text-center py-8 text-slate-400 text-xs font-medium">Belum ada riwayat perubahan profil.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
