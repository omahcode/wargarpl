@extends('layouts.app')

@section('title', 'Manajemen Jenis PTK - SobatData Neper')

@section('content')
<div class="space-y-6" x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    editId: null, 
    editName: '' 
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Jenis PTK (Pendidik & Tenaga Kependidikan)</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Daftar kategori pendidik dan staf kependidikan di SMKN 1 Cirebon.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showAddModal = true" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Tambah Jenis PTK
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">Nama Kategori Jenis PTK</th>
                        <th class="p-4">Jumlah Pegawai Terdaftar</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ptkTypes as $type)
                        <tr class="text-slate-600 font-medium hover:bg-slate-50/50 transition">
                            <td class="p-4 text-slate-900 font-semibold">{{ $type->name }}</td>
                            <td class="p-4 text-slate-500 font-medium">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold border border-slate-200">
                                    {{ $type->employees_count }} Pegawai
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button @click="editId = {{ $type->id }}; editName = '{{ addslashes($type->name) }}'; showEditModal = true" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Jenis PTK">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.ptk-types.destroy', $type->id) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Hapus Jenis PTK', message: 'Apakah Anda yakin ingin menghapus jenis PTK ini? Pegawai yang memiliki jenis PTK ini akan disetel menjadi Kosong.', type: 'danger' }, () => this.submit());">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Jenis PTK">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-8 text-center text-slate-400 font-medium">
                                Belum ada data jenis PTK.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Tambah Jenis PTK -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300" style="display: none;">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden" @click.away="showAddModal = false">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-900">Tambah Jenis PTK Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form action="{{ route('admin.ptk-types.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="add_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Jenis PTK *</label>
                    <input type="text" id="add_name" name="name" required placeholder="Contoh: Guru, Laboran, Teknisi" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
                </div>
                
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold transition active:scale-[0.98]">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                        Simpan Jenis PTK
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Jenis PTK -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300" style="display: none;">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden" @click.away="showEditModal = false">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-900">Edit Jenis PTK</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form :action="'/admin/ptk-types/' + editId" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Jenis PTK *</label>
                    <input type="text" id="edit_name" name="name" x-model="editName" required placeholder="Contoh: Guru, Laboran, Teknisi" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" />
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
