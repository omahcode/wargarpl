@extends('layouts.app')

@section('title', 'Edit Guru / Tendik - ' . $employee->full_name)

@section('content')
<div class="space-y-8 max-w-3xl mx-auto">
    <!-- Header -->
    <div>
        <a href="{{ route('admin.employees.show', $employee->uuid) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 transition">
            &larr; Kembali ke Detail Guru & Tendik
        </a>
        <h2 class="text-2xl font-bold text-slate-900 mt-2 font-display">Edit Data Guru / Tendik</h2>
        <p class="text-sm text-slate-500 font-medium">Ubah informasi dasar dan status kepegawaian.</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.employees.update', $employee->uuid) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- NIP, NUPTK, NIK -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="nip" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIP *</label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip', $employee->nip) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="nuptk" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NUPTK</label>
                    <input type="text" id="nuptk" name="nuptk" value="{{ old('nuptk', $employee->nuptk) }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIK</label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik', $employee->nik) }}" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
            </div>

            <!-- Nama & JK -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label for="full_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $employee->full_name) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="gender" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Kelamin *</label>
                    <select id="gender" name="gender" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                        <option value="L" {{ old('gender', $employee->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $employee->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <!-- Tempat/Tanggal Lahir, Agama -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="birth_place" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tempat Lahir *</label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $employee->birth_place) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Lahir *</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $employee->birth_date) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="religion" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Agama *</label>
                    <select id="religion" name="religion" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                        <option value="Islam" {{ old('religion', $employee->religion) === 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen" {{ old('religion', $employee->religion) === 'Kristen' ? 'selected' : '' }}>Kristen</option>
                        <option value="Katolik" {{ old('religion', $employee->religion) === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('religion', $employee->religion) === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('religion', $employee->religion) === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Khonghucu" {{ old('religion', $employee->religion) === 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                    </select>
                </div>
            </div>

            <!-- PTK Type, Kepegawaian status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="ptk_type" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis PTK *</label>
                    <select id="ptk_type" name="ptk_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                        <option value="Guru" {{ old('ptk_type', $employee->ptk_type) === 'Guru' ? 'selected' : '' }}>Guru</option>
                        <option value="Tenaga Administrasi" {{ old('ptk_type', $employee->ptk_type) === 'Tenaga Administrasi' ? 'selected' : '' }}>Tenaga Administrasi</option>
                        <option value="Teknisi" {{ old('ptk_type', $employee->ptk_type) === 'Teknisi' ? 'selected' : '' }}>Teknisi</option>
                        <option value="Laboran" {{ old('ptk_type', $employee->ptk_type) === 'Laboran' ? 'selected' : '' }}>Laboran</option>
                    </select>
                </div>
                <div>
                    <label for="employment_type" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Kepegawaian *</label>
                    <select id="employment_type" name="employment_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                        <option value="PNS" {{ old('employment_type', $employee->employment_type) === 'PNS' ? 'selected' : '' }}>PNS</option>
                        <option value="PPPK" {{ old('employment_type', $employee->employment_type) === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="Non ASN" {{ old('employment_type', $employee->employment_type) === 'Non ASN' ? 'selected' : '' }}>Non ASN</option>
                        <option value="Honor Sekolah" {{ old('employment_type', $employee->employment_type) === 'Honor Sekolah' ? 'selected' : '' }}>Honor Sekolah</option>
                    </select>
                </div>
            </div>

            <!-- Contacts: Email, Phone, Address -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nomor HP / WA *</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
            </div>

            <div>
                <label for="address" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Tinggal</label>
                <textarea id="address" name="address" rows="3" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">{{ old('address', $employee->address) }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="pt-6 border-t border-slate-100 flex justify-end gap-2">
                <a href="{{ route('admin.employees.show', $employee->uuid) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
