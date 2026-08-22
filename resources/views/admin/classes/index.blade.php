@extends('layouts.app')

@section('title', 'Manajemen Rombongan Belajar - SIDATA NEPER')

@section('content')
<div class="space-y-6" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    editId: null, 
    editName: '', 
    editMajorId: '', 
    editGradeLevel: '', 
    editTeacherId: '' 
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Rombongan Belajar (Rombel)</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Daftar kelas aktif di SMKN 1 Cirebon.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.classes.delete-all') }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Kosongkan Semua Rombel', message: 'Apakah Anda yakin ingin menghapus SELURUH rombel/kelas? Seluruh keanggotaan kelas (siswa yang ditempatkan) juga akan terhapus.', type: 'danger' }, () => this.submit());">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                    Kosongkan Semua
                </button>
            </form>
            <button @click="showAddModal = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Tambah Rombel
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">Nama Rombel</th>
                        <th class="p-4">Tingkat</th>
                        <th class="p-4">Jurusan</th>
                        <th class="p-4">Wali Kelas</th>
                        <th class="p-4">Siswa</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($classes as $c)
                        <tr class="text-slate-600 font-medium hover:bg-slate-50/50 transition">
                            <td class="p-4 text-slate-950 font-bold">{{ $c->name }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100">
                                    Tingkat {{ $c->grade_level }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-700 font-semibold">{{ $c->major->name }} ({{ $c->major->code }})</td>
                            <td class="p-4 text-slate-900 font-semibold">{{ $c->homeroomTeacher->full_name ?? '-' }}</td>
                            <td class="p-4">
                                <a href="{{ route('admin.classes.show', $c->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"></path></svg>
                                    {{ $c->members_count }} Siswa
                                </a>
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button @click="
                                        editId = {{ $c->id }}; 
                                        editName = '{{ addslashes($c->name) }}'; 
                                        editMajorId = '{{ $c->major_id }}'; 
                                        editGradeLevel = '{{ $c->grade_level }}'; 
                                        editTeacherId = '{{ $c->homeroom_teacher_id ?? '' }}'; 
                                        showEditModal = true
                                    " class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Rombel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.classes.destroy', $c->id) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Hapus Rombel', message: 'Apakah Anda yakin ingin menghapus rombel ini? Keanggotaan siswa di kelas ini akan terhapus.', type: 'danger' }, () => this.submit());">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Rombel">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 font-medium">
                                Belum ada data rombongan belajar (kelas).
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Tambah Rombel -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300" style="display: none;">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden" @click.away="showAddModal = false">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-900">Tambah Rombel Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('admin.classes.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="add_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Rombel / Kelas *</label>
                    <input type="text" id="add_name" name="name" required placeholder="Contoh: XII PPLG 1" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50" />
                </div>
                <div>
                    <label for="add_grade_level" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tingkat Kelas *</label>
                    <select id="add_grade_level" name="grade_level" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                        <option value="10">Tingkat 10 (X)</option>
                        <option value="11">Tingkat 11 (XI)</option>
                        <option value="12">Tingkat 12 (XII)</option>
                    </select>
                </div>
                <div>
                    <label for="add_major_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Jurusan *</label>
                    <select id="add_major_id" name="major_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                        <option value="" disabled selected>Pilih Jurusan</option>
                        @foreach ($majors as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="add_teacher_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wali Kelas (Opsional)</label>
                    <select id="add_teacher_id" name="homeroom_teacher_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                        <option value="">Tanpa Wali Kelas</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold transition active:scale-[0.98]">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                        Simpan Rombel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Rombel -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300" style="display: none;">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden" @click.away="showEditModal = false">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-900">Edit Rombongan Belajar</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form :action="'/admin/classes/' + editId" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Rombel / Kelas *</label>
                    <input type="text" id="edit_name" name="name" x-model="editName" required placeholder="Contoh: XII PPLG 1" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50" />
                </div>
                <div>
                    <label for="edit_grade_level" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tingkat Kelas *</label>
                    <select id="edit_grade_level" name="grade_level" x-model="editGradeLevel" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                        <option value="10">Tingkat 10 (X)</option>
                        <option value="11">Tingkat 11 (XI)</option>
                        <option value="12">Tingkat 12 (XII)</option>
                    </select>
                </div>
                <div>
                    <label for="edit_major_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Jurusan *</label>
                    <select id="edit_major_id" name="major_id" x-model="editMajorId" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                        <option value="" disabled>Pilih Jurusan</option>
                        @foreach ($majors as $m)
                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_teacher_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Wali Kelas (Opsional)</label>
                    <select id="edit_teacher_id" name="homeroom_teacher_id" x-model="editTeacherId" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50">
                        <option value="">Tanpa Wali Kelas</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold transition active:scale-[0.98]">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
