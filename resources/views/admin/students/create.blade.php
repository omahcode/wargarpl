@extends('layouts.app')

@section('title', 'Tambah Siswa Baru')

@section('content')
<div class="space-y-8 max-w-3xl mx-auto">
    <!-- Header -->
    <div>
        <a href="{{ route('admin.students.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 transition">
            &larr; Kembali ke Daftar Siswa
        </a>
        <h2 class="text-2xl font-bold text-slate-900 mt-2 font-display">Registrasi Siswa Baru</h2>
        <p class="text-sm text-slate-500 font-medium">Lengkapi biodata dasar siswa untuk membuat profil dan akun portal login.</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.students.store') }}" class="space-y-6">
            @csrf

            <!-- NIS, NISN, NIK -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="nis" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIS (Nomor Induk Siswa) *</label>
                    <input type="text" id="nis" name="nis" value="{{ old('nis') }}" required placeholder="Contoh: 24251036" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="nisn" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NISN</label>
                    <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" placeholder="10 Digit NISN" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIK (Akan Dienkripsi)</label>
                    <input type="text" id="nik" name="nik" value="{{ old('nik') }}" placeholder="16 Digit NIK" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
            </div>

            <!-- Nama & Nickname -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label for="full_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required placeholder="Nama Sesuai Ijazah" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="nickname" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Panggilan</label>
                    <input type="text" id="nickname" name="nickname" value="{{ old('nickname') }}" placeholder="Nama Panggilan" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
            </div>

            <!-- JK, Tempat/Tanggal Lahir -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="gender" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Kelamin *</label>
                    <select id="gender" name="gender" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label for="birth_place" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tempat Lahir *</label>
                    <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place') }}" required placeholder="Kota Lahir" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                <div>
                    <label for="birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Lahir *</label>
                    <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
            </div>

            <!-- Agama, Rombel, Tahun Masuk -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="religion" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Agama *</label>
                    <select id="religion" name="religion" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                        <option value="Islam" {{ old('religion') === 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen" {{ old('religion') === 'Kristen' ? 'selected' : '' }}>Kristen</option>
                        <option value="Katolik" {{ old('religion') === 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('religion') === 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('religion') === 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Khonghucu" {{ old('religion') === 'Khonghucu' ? 'selected' : '' }}>Khonghucu</option>
                    </select>
                </div>
                <div>
                    <label for="class_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Penugasan Rombel *</label>
                    <select id="class_id" name="class_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white">
                        <option value="">Pilih Rombel</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="entry_year" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Masuk (Angkatan) *</label>
                    <input type="number" id="entry_year" name="entry_year" value="{{ old('entry_year', date('Y')) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-6 border-t border-slate-100 flex justify-end gap-2">
                <a href="{{ route('admin.students.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    Simpan Data Siswa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
