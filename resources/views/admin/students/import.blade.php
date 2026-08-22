@extends('layouts.app')

@section('title', 'Import & Export Data Siswa')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <a href="{{ route('admin.students.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 transition">
            &larr; Kembali ke Daftar Siswa
        </a>
        <h2 class="text-2xl font-bold text-slate-900 mt-2 font-display">Import / Export Data Siswa</h2>
        <p class="text-sm text-slate-500 font-medium">Unggah berkas Excel Dapodik daftar peserta didik (PD) atau unduh master data siswa ke format CSV.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Import Card -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-900">Import Data Siswa (Excel)</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">Mendukung format spreadsheet `daftar_pd` resmi dari sistem Dapodik.</p>
                </div>
                
                <div class="p-6 space-y-4">
                    <form method="POST" action="{{ route('admin.students.import-preview') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="import_type" value="siswa" />
                        
                        <!-- Select File Excel -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih File Template Excel (.xlsx)</label>
                            <input 
                                type="file" 
                                name="excel_file" 
                                required 
                                accept=".xlsx, .xls"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 transition cursor-pointer"
                            />
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                                Mulai Upload & Validasi Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Export Card -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900">Ekspor Data Siswa (CSV)</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Unduh seluruh atau sebagian data siswa dengan filter berikut.</p>
            </div>
            
            <form method="GET" action="{{ route('admin.students.export') }}" class="p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Class Filter -->
                    <div>
                        <label for="class_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Filter Rombel</label>
                        <select id="class_id" name="class_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                            <option value="">Semua Rombel</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year Filter -->
                    <div>
                        <label for="entry_year" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Masuk (Angkatan)</label>
                        <select id="entry_year" name="entry_year" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                            <option value="">Semua Angkatan</option>
                            @foreach ($years as $yr)
                                <option value="{{ $yr }}">{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div>
                        <label for="gender" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                            <option value="">Semua Jenis Kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Siswa</label>
                        <select id="status" name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                            <option value="">Semua Status</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Lulus">Lulus</option>
                            <option value="Pindah">Pindah</option>
                            <option value="Keluar">Keluar</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                        Ekspor ke CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
