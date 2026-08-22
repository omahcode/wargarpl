@extends('layouts.app')

@section('title', 'Detail Siswa - ' . $student->full_name)

@section('content')
<div class="space-y-8" x-data="{ tab: 'diri' }">
    <!-- Header Back -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.students.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 transition">
                &larr; Kembali ke Daftar Siswa
            </a>
            <h2 class="text-2xl font-bold text-slate-900 mt-2">{{ $student->full_name }}</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">NIS: {{ $student->nis }} &bull; NISN: {{ $student->nisn ?? '-' }}</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('admin.students.reset-password', $student->uuid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Reset Password Siswa', message: 'Apakah Anda yakin ingin mereset password siswa ini kembali ke bawaan (Tanggal Lahir)?', type: 'info' }, () => this.submit());">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                    Reset Password
                </button>
            </form>
            <a href="{{ route('admin.students.edit', $student->uuid) }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                Edit Siswa
            </a>
            <form action="{{ route('admin.students.destroy', $student->uuid) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); confirmAction({ title: 'Hapus Siswa', message: 'Apakah Anda yakin ingin menghapus siswa ini? Akun login terkait juga akan dihapus.', type: 'danger' }, () => this.submit());">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                    Hapus Siswa
                </button>
            </form>
        </div>
    </div>

    <!-- Active Campaign & Verification Controls -->
    @if ($campaign)
        @php
            $status = $verification?->status ?? 'Belum diperiksa';
            $isPending  = $status === 'Menunggu Validasi Admin';
            $isVerified = in_array($status, ['Sudah diperiksa tanpa perubahan', 'Sudah diperbaiki']);
            $statusColors = [
                'Belum diperiksa'                    => 'text-amber-600 bg-amber-50 border-amber-200',
                'Menunggu Validasi Admin'             => 'text-blue-700 bg-blue-50 border-blue-200',
                'Sudah diperiksa tanpa perubahan'     => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                'Sudah diperbaiki'                    => 'text-indigo-700 bg-indigo-50 border-indigo-200',
            ];
            $dotColors = [
                'Belum diperiksa'                    => 'bg-amber-500',
                'Menunggu Validasi Admin'             => 'bg-blue-500 animate-pulse',
                'Sudah diperiksa tanpa perubahan'     => 'bg-emerald-500',
                'Sudah diperbaiki'                    => 'bg-indigo-500',
            ];
            $sc = $statusColors[$status] ?? 'text-slate-600 bg-slate-50 border-slate-200';
            $dc = $dotColors[$status] ?? 'bg-slate-400';
        @endphp

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Campaign Verifikasi Aktif</span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">{{ $campaign->title }}</h3>
                </div>
                <div class="text-sm font-bold shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border {{ $sc }}">
                        <span class="w-2 h-2 rounded-full {{ $dc }}"></span>
                        {{ $status }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-5">

                {{-- ===== PENDING VALIDATION PANEL ===== --}}
                @if ($isPending)
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-blue-900">Menunggu Persetujuan Admin</h4>
                                <p class="text-xs text-blue-700 mt-0.5">
                                    Siswa telah mengajukan perbaikan data pada
                                    <strong>{{ \Carbon\Carbon::parse($verification->edit_used_at)->translatedFormat('d F Y, H:i') }} WIB</strong>.
                                    Tinjau perubahan di tab <em>Riwayat Perubahan</em> dan pilih tindakan:
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Approve --}}
                            <form method="POST" action="{{ route('admin.students.approve-correction', $student->uuid) }}"
                                onsubmit="event.preventDefault(); confirmAction({ title: 'Setujui Perbaikan Data', message: 'Anda akan menyetujui dan mengonfirmasi perbaikan data yang diajukan siswa ini.', type: 'info' }, () => this.submit());">
                                @csrf
                                <input type="hidden" name="notes" value="Disetujui oleh admin.">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition active:scale-[0.98]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Setujui Perbaikan
                                </button>
                            </form>

                            {{-- Reject --}}
                            <form method="POST" action="{{ route('admin.students.reject-correction', $student->uuid) }}"
                                onsubmit="event.preventDefault(); confirmAction({ title: 'Tolak Perbaikan Data', message: 'Data siswa akan dikembalikan ke kondisi sebelumnya dan siswa dapat mengedit ulang.', type: 'danger' }, () => this.submit());">
                                @csrf
                                <div class="flex gap-2">
                                    <input type="text" name="notes" placeholder="Alasan penolakan (wajib)..." required
                                        class="flex-1 min-w-0 px-3 py-2.5 rounded-xl border border-red-200 focus:border-red-500 focus:ring-1 focus:ring-red-400 outline-none text-sm font-medium bg-white placeholder-red-300">
                                    <button type="submit" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition active:scale-[0.98]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Tolak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                {{-- ===== ALREADY VERIFIED (allow unlock) ===== --}}
                @elseif ($verification && $verification->edit_used_at !== null)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-amber-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Hak Edit Sudah Digunakan
                        </h4>
                        <p class="text-xs text-amber-700 mt-1">
                            Siswa telah melakukan verifikasi pada
                            <strong>{{ \Carbon\Carbon::parse($verification->edit_used_at)->translatedFormat('d F Y, H:i') }} WIB</strong>.
                        </p>
                    </div>
                    <!-- Unlock form -->
                    <form method="POST" action="{{ route('admin.students.unlock', $student->uuid) }}" class="space-y-3 max-w-lg">
                        @csrf
                        <label for="reason" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Alasan Buka Hak Edit (Wajib)</label>
                        <textarea id="reason" name="reason" rows="2" required placeholder="Masukkan alasan pembukaan hak edit..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white"></textarea>
                        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition active:scale-[0.98]">
                            Buka Hak Edit Siswa
                        </button>
                    </form>

                {{-- ===== EDIT STILL AVAILABLE ===== --}}
                @else
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-xs text-emerald-700 font-medium">
                        Siswa saat ini masih memiliki hak edit/akses perbaikan data untuk semester ini.
                    </div>
                @endif

                {{-- Unlock history --}}
                @if ($verification && $verification->unlock_reason)
                    <div class="pt-4 border-t border-slate-100 text-xs">
                        <span class="text-slate-400 block font-bold uppercase tracking-wider mb-1">Riwayat Pembukaan Terakhir</span>
                        <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                            <p class="text-slate-800 font-medium">"{{ $verification->unlock_reason }}"</p>
                            <p class="text-[10px] text-slate-400 mt-1">Dibuka oleh: <strong>{{ $verification->unlockedByAdmin->name ?? 'Admin' }}</strong> &bull; {{ \Carbon\Carbon::parse($verification->edit_unlocked_at)->translatedFormat('d M Y, H:i') }} WIB</p>
                        </div>
                    </div>
                @endif

                {{-- Admin validation notes --}}
                @if ($verification && $verification->admin_notes && !$isPending)
                    <div class="pt-4 border-t border-slate-100 text-xs">
                        <span class="text-slate-400 block font-bold uppercase tracking-wider mb-1">Catatan Validasi Admin</span>
                        <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl">
                            <p class="text-slate-800 font-medium">"{{ $verification->admin_notes }}"</p>
                            @if($verification->adminValidatedByUser)
                            <p class="text-[10px] text-slate-400 mt-1">
                                Oleh: <strong>{{ $verification->adminValidatedByUser->name }}</strong>
                                &bull; {{ \Carbon\Carbon::parse($verification->admin_validated_at)->translatedFormat('d M Y, H:i') }} WIB
                            </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif


    <!-- Profile Details & Tabs -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <!-- Tab Headers -->
        <div class="flex border-b border-slate-100 overflow-x-auto bg-slate-50/50">
            <button @click="tab = 'diri'" :class="tab === 'diri' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Data Diri
            </button>
            <button @click="tab = 'alamat'" :class="tab === 'alamat' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Alamat & Kontak
            </button>
            <button @click="tab = 'ortu'" :class="tab === 'ortu' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Orang Tua & Wali
            </button>
            <button @click="tab = 'tambahan'" :class="tab === 'tambahan' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Detail Fisik & Transportasi
            </button>
        </div>

        <div class="p-6">
            <!-- 1. Data Diri -->
            <div x-show="tab === 'diri'" class="space-y-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Identitas Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">Nama Lengkap</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->full_name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Nama Panggilan</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->nickname ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">NIS / NISN</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->nis }} / {{ $student->nisn ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">NIK</span>
                        <strong class="text-slate-900 font-semibold font-mono">{{ $student->nik ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Tempat / Tanggal Lahir</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Jenis Kelamin</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Agama</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->religion ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kewarganegaraan</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->nationality }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Jalur Masuk / Asal Sekolah</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->admission_path ?? '-' }} / {{ $student->previous_school ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 2. Alamat & Kontak -->
            <div x-show="tab === 'alamat'" class="space-y-6" style="display: none;">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Kontak & Alamat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">No. HP / WhatsApp Siswa</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->phone ?? '-' }} / {{ $student->whatsapp ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Email Siswa</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->email ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Alamat Lengkap</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->address ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">RT / RW</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->rt ?? '-' }} / {{ $student->rw ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Desa / Kelurahan / Kecamatan</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->village ?? '-' }} / {{ $student->district ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kabupaten / Kota / Provinsi</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->city ?? '-' }} / {{ $student->province ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kode Pos</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->postal_code ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Orang Tua & Wali -->
            <div x-show="tab === 'ortu'" class="space-y-6" style="display: none;">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Ayah -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-1">Data Ayah</h4>
                        <div class="text-sm space-y-2">
                            <p><span class="text-slate-400 block">Nama Ayah:</span> <strong class="text-slate-900">{{ $student->father_name ?? '-' }}</strong></p>
                            <p><span class="text-slate-400 block">NIK / No HP:</span> <strong class="text-slate-900">{{ $student->father_nik ?? '-' }} / {{ $student->father_phone ?? '-' }}</strong></p>
                            <p><span class="text-slate-400 block">Pekerjaan / Penghasilan:</span> <strong class="text-slate-900">{{ $student->father_occupation ?? '-' }} / {{ $student->father_income ?? '-' }}</strong></p>
                        </div>
                    </div>

                    <!-- Ibu -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-1">Data Ibu</h4>
                        <div class="text-sm space-y-2">
                            <p><span class="text-slate-400 block">Nama Ibu:</span> <strong class="text-slate-900">{{ $student->mother_name ?? '-' }}</strong></p>
                            <p><span class="text-slate-400 block">NIK / No HP:</span> <strong class="text-slate-900">{{ $student->mother_nik ?? '-' }} / {{ $student->mother_phone ?? '-' }}</strong></p>
                            <p><span class="text-slate-400 block">Pekerjaan / Penghasilan:</span> <strong class="text-slate-900">{{ $student->mother_occupation ?? '-' }} / {{ $student->mother_income ?? '-' }}</strong></p>
                        </div>
                    </div>

                    <!-- Wali -->
                    <div class="space-y-3 lg:col-span-2">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider border-b pb-1">Data Wali</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <p><span class="text-slate-400 block">Nama Wali:</span> <strong class="text-slate-900">{{ $student->visitor_wali_name ?? '-' }}</strong></p>
                            <p><span class="text-slate-400 block">Hubungan Wali:</span> <strong class="text-slate-900">{{ $student->wali_relationship ?? '-' }}</strong></p>
                            <p><span class="text-slate-400 block">Pekerjaan / No HP:</span> <strong class="text-slate-900">{{ $student->wali_occupation ?? '-' }} / {{ $student->wali_phone ?? '-' }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Detail Fisik & Tambahan -->
            <div x-show="tab === 'tambahan'" class="space-y-6" style="display: none;">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Fisik & Penunjang</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">Anak Ke / Jumlah Saudara</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->child_number ?? '-' }} / {{ $student->sibling_count ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Tinggi / Berat Badan</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->height_cm ?? '-' }} cm / {{ $student->weight_kg ?? '-' }} kg</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Alat Transportasi ke Sekolah</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->transportation ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Jarak / Waktu Tempuh Rumah</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->home_distance_km ?? '-' }} km / {{ $student->travel_time_minutes ?? '-' }} Menit</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kebutuhan Khusus</span>
                        <strong class="text-slate-900 font-semibold">{{ $student->special_needs ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid: History & Change Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Rombel History -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Rombongan Belajar</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-3">Tahun Ajaran</th>
                            <th class="pb-3">Semester</th>
                            <th class="pb-3">Rombel</th>
                            <th class="pb-3">No Absen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($classHistory as $ch)
                            <tr class="text-slate-600 font-medium">
                                <td class="py-3">{{ $ch->year_name }}</td>
                                <td class="py-3">{{ $ch->semester }}</td>
                                <td class="py-3 text-slate-950 font-bold">{{ $ch->class_name }}</td>
                                <td class="py-3">{{ $ch->attendance_number ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400 text-xs font-semibold">Belum ada riwayat rombel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Student verifications change logs -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Perbaikan Profil</h3>
            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2">
                @forelse ($changeLogs as $log)
                    <div class="text-xs p-3.5 bg-slate-50 border border-slate-100 rounded-xl flex justify-between gap-4">
                        <div>
                            <span class="font-bold text-slate-950 capitalize">{{ str_replace('_', ' ', $log->field_name) }}</span>
                            <div class="mt-1 flex items-center gap-1.5 text-slate-400">
                                <span class="line-through">{{ $log->old_value ?? '(Kosong)' }}</span>
                                <span>&rarr;</span>
                                <span class="text-slate-900 font-semibold">{{ $log->new_value ?? '(Kosong)' }}</span>
                            </div>
                        </div>
                        <div class="text-right text-[10px] text-slate-400 font-medium flex-shrink-0">
                            {{ Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                            <div class="mt-0.5 font-mono text-[9px]">{{ $log->ip_address }}</div>
                            <div class="mt-0.5 text-slate-500 font-semibold">Oleh: {{ $log->modifiedBy->name ?? 'Siswa' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 text-xs font-medium">Siswa belum melakukan perbaikan data profil.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
