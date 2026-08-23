@extends('layouts.app')

@section('title', 'Manajemen Guru & Tendik')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Master Data Guru & Tendik</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Daftar Pendidik dan Tenaga Kependidikan SMKN 1 Cirebon.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.employees.delete-all') }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Kosongkan Guru/Tendik', message: 'Apakah Anda yakin ingin menghapus SELURUH data guru & tenaga kependidikan serta akun login terkait secara permanen? Tindakan ini tidak dapat dibatalkan.', type: 'danger' }, () => this.submit());">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                    Kosongkan Semua Guru/Tendik
                </button>
            </form>
            <a href="{{ route('admin.employees.import-ui') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Import Excel
            </a>
            <a href="{{ route('admin.employees.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Tambah Guru/Tendik
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
        <form method="GET" action="{{ route('admin.employees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Pencarian</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari NIP, NIK, atau Nama..." 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition placeholder-slate-400 bg-slate-50/50 focus:bg-white"
                />
            </div>

            <!-- PTK Type Filter -->
            <div>
                <label for="ptk_type" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Jenis PTK</label>
                <select 
                    id="ptk_type" 
                    name="ptk_type" 
                    class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white"
                >
                    <option value="">Semua Jenis</option>
                    @foreach($ptkTypes as $type)
                        <option value="{{ $type->name }}" {{ $typeFilter == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <a href="{{ route('admin.employees.index') }}" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition inline-block">
                    Reset
                </a>
                <button type="submit" class="px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex-1">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Employee Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">NIP / NIK</th>
                        <th class="p-4">Nama Lengkap</th>
                        <th class="p-4">Jenis PTK</th>
                        <th class="p-4">Kepegawaian</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $emp)
                        <tr class="hover:bg-slate-50/50 transition text-slate-600 font-medium">
                            <td class="p-4">
                                <span class="text-slate-900 font-bold block">{{ $emp->nip ?? '-' }}</span>
                                <span class="text-xs text-slate-400">{{ $emp->nik ?? '-' }}</span>
                            </td>
                            <td class="p-4 text-slate-950 font-semibold">{{ $emp->full_name }}</td>
                            <td class="p-4">{{ $emp->ptk_type ?? '-' }}</td>
                            <td class="p-4">{{ $emp->employment_type ?? '-' }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $emp->status === 'Aktif' || $emp->status === 'Active' ? 'text-green-700 bg-green-50 border border-green-200' : 'text-red-700 bg-red-50 border border-red-200' }}">
                                    {{ $emp->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.employees.show', $emp->uuid) }}" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.employees.edit', $emp->uuid) }}" class="px-2 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.employees.destroy', $emp->uuid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Hapus Guru/Tendik', message: 'Apakah Anda yakin ingin menghapus data Guru/Tendik ini? Akun login terkait juga akan dihapus.', type: 'danger' }, () => this.submit());">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-bold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 font-medium">Tidak ada data guru/tendik ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
