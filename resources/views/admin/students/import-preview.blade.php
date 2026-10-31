@extends('layouts.app')

@section('title', 'Preview Import Data')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div>
        <a href="{{ route('admin.students.import-export-ui') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 transition">
            &larr; Kembali ke Form Import
        </a>
        <h2 class="text-2xl font-bold text-slate-900 mt-2">Hasil Validasi & Preview Import ({{ ucfirst($importType) }})</h2>
        <p class="text-sm text-slate-500 font-medium">Tinjau hasil pengecekan validitas template Excel sebelum disimpan ke database.</p>
    </div>

    <!-- Stats summary grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Baris File</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $total }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Total data terbaca dari file</p>
        </div>
        <div class="bg-white border border-emerald-100 rounded-2xl p-5 shadow-sm bg-gradient-to-br from-white to-emerald-50/30">
            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Valid & Siap Impor</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $validCount }}</div>
            <p class="text-[11px] text-emerald-600/80 mt-1 font-medium">Khusus Siswa RPL / PPLG</p>
        </div>
        <div class="bg-white border border-amber-100 rounded-2xl p-5 shadow-sm bg-gradient-to-br from-white to-amber-50/30">
            <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Dilewati Otomatis</span>
            <div class="text-2xl font-extrabold text-amber-600 mt-1">{{ $skippedCount ?? 0 }}</div>
            <p class="text-[11px] text-amber-600/80 mt-1 font-medium">Jurusan luar RPL/PPLG dilewati</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-bold text-red-500 uppercase tracking-wider">Gagal Validasi</span>
            <div class="text-2xl font-extrabold text-red-600 mt-1">{{ $errorCount }}</div>
            <p class="text-[11px] text-slate-400 mt-1">Format/kolom bermasalah</p>
        </div>
    </div>

    <!-- Errors lists -->
    @if ($errorCount > 0)
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-sm">
            <h4 class="text-sm font-bold text-red-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Ditemukan {{ $errorCount }} Baris dengan Error
            </h4>
            <p class="text-xs text-red-600 mt-1">Baris-baris di bawah ini akan diabaikan saat Anda menekan tombol konfirmasi impor di bawah.</p>
            
            <div class="mt-4 overflow-x-auto max-h-[250px] border border-red-100 rounded-xl bg-white">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-red-50/50 border-b border-red-100 text-slate-500 uppercase tracking-wider font-bold">
                            <th class="p-3">Baris File</th>
                            <th class="p-3">Keterangan Error</th>
                            <th class="p-3">Data Terkait</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-red-50">
                        @foreach ($errorRows as $err)
                            <tr class="text-red-700">
                                <td class="p-3 font-bold">#{{ $err['row'] }}</td>
                                <td class="p-3 font-semibold">{{ $err['message'] }}</td>
                                <td class="p-3 font-mono text-[10px] text-slate-500">
                                    Identitas: {{ $err['data']['nis'] ?? $err['data']['nip'] ?? '-' }} &bull; Nama: {{ $err['data']['nama'] ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Valid Preview Table -->
    @if ($validCount > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Preview Data Valid (Maksimal 10 baris pertama)</h3>
                    <p class="text-xs text-slate-500 mt-1">Hanya data valid yang akan disimpan ke database.</p>
                </div>
                
                <div x-data="{
                    importing: false,
                    progress: 0,
                    processed: 0,
                    total: {{ $validCount }},
                    errorMsg: '',
                    startImport() {
                        confirmAction({
                            title: 'Mulai Impor Data',
                            message: 'Apakah Anda yakin ingin memasukkan data valid ini ke database secara bertahap (chunk)?',
                            type: 'info',
                            confirmText: 'Mulai Impor'
                        }, () => {
                            this.importing = true;
                            this.processChunk(0);
                        });
                    },
                    processChunk(index) {
                        const chunkSize = 100;
                        fetch('{{ route('admin.students.import-chunk') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                chunk_index: index,
                                chunk_size: chunkSize
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.processed = data.total_processed;
                                this.progress = Math.round((this.processed / this.total) * 100);
                                if (data.is_finished) {
                                    setTimeout(() => {
                                        window.location.href = '{{ $importType === 'siswa' ? route('admin.students.index') : route('admin.employees.index') }}?success=Berhasil+mengimpor+' + this.total + '+data';
                                    }, 800);
                                } else {
                                    this.processChunk(index + 1);
                                }
                            } else {
                                this.importing = false;
                                this.errorMsg = data.message || 'Gagal memproses impor batch.';
                                alert(this.errorMsg);
                            }
                        })
                        .catch(err => {
                            this.importing = false;
                            this.errorMsg = 'Koneksi gagal atau terputus.';
                            alert(this.errorMsg);
                        });
                    }
                }">
                    <!-- Regular Button -->
                    <button type="button" @click="startImport" x-show="!importing" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                        Konfirmasi & Import {{ $validCount }} Data {{ ucfirst($importType) }}
                    </button>
                    
                    <!-- Progress bar -->
                    <div x-show="importing" class="w-64 space-y-2" style="display: none;">
                        <div class="flex justify-between text-xs font-bold text-slate-700">
                            <span>Mengimpor...</span>
                            <span x-text="progress + '% (' + processed + '/' + total + ')'">0%</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        @if ($importType === 'siswa')
                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                                <th class="p-4">NIS</th>
                                <th class="p-4">NISN</th>
                                <th class="p-4">Nama Lengkap</th>
                                <th class="p-4">JK</th>
                                <th class="p-4">Penugasan Rombel</th>
                            </tr>
                        @else
                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                                <th class="p-4">NIP</th>
                                <th class="p-4">NUPTK</th>
                                <th class="p-4">Nama Lengkap</th>
                                <th class="p-4">JK</th>
                                <th class="p-4">Kepegawaian</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if ($importType === 'siswa')
                            @foreach (array_slice($validRows, 0, 10) as $row)
                                <tr class="text-slate-600 font-medium">
                                    <td class="p-4 text-slate-900 font-bold">{{ $row['nis'] }}</td>
                                    <td class="p-4">{{ $row['nisn'] ?? '-' }}</td>
                                    <td class="p-4 text-slate-950 font-semibold">{{ $row['full_name'] }}</td>
                                    <td class="p-4">{{ $row['gender'] }}</td>
                                    <td class="p-4 text-slate-900 font-semibold">
                                        {{ $row['class_name'] ? $row['class_name'] : '(Belum dimasukkan)' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            @foreach (array_slice($validRows, 0, 10) as $row)
                                <tr class="text-slate-600 font-medium">
                                    <td class="p-4 text-slate-900 font-bold">{{ $row['nip'] }}</td>
                                    <td class="p-4">{{ $row['nuptk'] ?? '-' }}</td>
                                    <td class="p-4 text-slate-950 font-semibold">{{ $row['full_name'] }}</td>
                                    <td class="p-4">{{ $row['gender'] }}</td>
                                    <td class="p-4 text-slate-900 font-semibold">{{ $row['status_kepegawaian'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-sm text-slate-400 font-medium space-y-4">
            <p>Tidak ada data valid yang dapat diimpor dari file Excel yang Anda unggah.</p>
            <a href="{{ route('admin.students.import-export-ui') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition inline-block">
                Upload File Lain
            </a>
        </div>
    @endif
</div>
@endsection
