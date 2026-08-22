@extends('layouts.app')

@section('title', 'Import Data Guru & Tendik')

@section('content')
<div class="space-y-8 max-w-xl mx-auto">
    <!-- Header -->
    <div>
        <a href="{{ route('admin.employees.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 transition">
            &larr; Kembali ke Daftar Guru & Tendik
        </a>
        <h2 class="text-2xl font-bold text-slate-900 mt-2 font-display">Import Guru / Tendik</h2>
        <p class="text-sm text-slate-500 font-medium">Unggah berkas Excel Dapodik daftar PTK (Pendidik & Tenaga Kependidikan) secara massal.</p>
    </div>

    <!-- Import Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-base font-bold text-slate-900">Import Master Data (Excel)</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Mendukung format excel `daftar-guru` dan `daftar-tendik` resmi dari Dapodik.</p>
            </div>
            
            <div class="p-6 space-y-4">
                <form method="POST" action="{{ route('admin.students.import-preview') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <!-- Select Import Type -->
                    <div>
                        <label for="import_type" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Kategori PTK *</label>
                        <select id="import_type" name="import_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                            <option value="guru">Guru (Pendidik)</option>
                            <option value="tendik">Tenaga Kependidikan (Tendik)</option>
                        </select>
                    </div>

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
                            Mulai Upload & Validasi PTK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
