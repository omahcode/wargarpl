@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 shadow-sm">
        <div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Selamat Datang,</span>
            <h2 class="text-2xl font-bold text-slate-900 mt-1">{{ $student->full_name }}</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">NIS: {{ $student->nis }} &bull; NISN: {{ $student->nisn ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg border border-slate-200">
                Kelas: {{ $student->currentClassMember->schoolClass->name ?? 'Belum memiliki kelas' }}
            </span>
        </div>
    </div>

    <!-- Campaign and Verification Box -->
    @if ($campaign)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="text-xs font-semibold text-amber-600 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full uppercase tracking-wider">Campaign Verifikasi Aktif</span>
                    <h3 class="text-lg font-bold text-slate-900 mt-2">{{ $campaign->title }}</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">Batas waktu: {{ Carbon\Carbon::parse($campaign->start_date)->translatedFormat('d F Y') }} s.d. {{ Carbon\Carbon::parse($campaign->end_date)->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider block sm:text-right text-slate-400">Status Pengecekan</span>
                    <div class="mt-1 inline-flex items-center gap-1.5 text-sm font-semibold 
                        {{ $verification->status === 'Belum diperiksa' ? 'text-amber-600' : 'text-green-600' }}">
                        <span class="w-2 h-2 rounded-full {{ $verification->status === 'Belum diperiksa' ? 'bg-amber-500 animate-pulse' : 'bg-green-500' }}"></span>
                        {{ $verification->status }}
                    </div>
                </div>
            </div>

            <div class="p-6">
                @if ($verification->edit_used_at === null)
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                        <h4 class="text-sm font-bold text-amber-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            PENTING: Verifikasi Data Mandiri
                        </h4>
                        <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                            Silakan lakukan pengecekan seluruh data Anda di bawah ini dengan teliti. 
                            Anda hanya memiliki <strong>1 kali hak perubahan data</strong> di semester ini. Pastikan seluruh data sudah benar sebelum mengirimkan perubahan.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <form method="POST" action="{{ route('student.verify-correct') }}" onsubmit="event.preventDefault(); confirmAction({ title: 'Konfirmasi Data', message: 'Apakah Anda yakin seluruh data di bawah sudah benar? Anda tidak dapat mengubah data lagi setelah konfirmasi.', type: 'info', confirmText: 'Ya, Sudah Benar' }, () => this.submit());">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
                                Data Saya Sudah Benar
                            </button>
                        </form>
                        <a href="{{ route('student.edit') }}" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold text-center transition active:scale-[0.98]">
                            Perbaiki Data
                        </a>
                    </div>
                @else
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                        <h4 class="text-sm font-bold text-green-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pengecekan Selesai
                        </h4>
                        <p class="text-xs text-green-700 mt-1 leading-relaxed">
                            Anda telah melakukan pengisian/verifikasi data untuk semester ini pada <strong>{{ Carbon\Carbon::parse($verification->edit_used_at)->translatedFormat('d F Y, H:i') }} WIB</strong>.
                            Hak edit Anda telah dinonaktifkan. Hubungi administrator/staf TU apabila masih terdapat kesalahan data.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Profile Details (Tabs) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ tab: 'diri' }">
        <!-- Tab Headers -->
        <div class="flex border-b border-slate-100 overflow-x-auto">
            <button @click="tab = 'diri'" :class="tab === 'diri' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-600 font-medium'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Data Diri
            </button>
            <button @click="tab = 'alamat'" :class="tab === 'alamat' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-600 font-medium'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Alamat & Kontak
            </button>
            <button @click="tab = 'ortu'" :class="tab === 'ortu' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-600 font-medium'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Orang Tua / Wali
            </button>
            <button @click="tab = 'tambahan'" :class="tab === 'tambahan' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-600 font-medium'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Detail Fisik & Transportasi
            </button>
            <button @click="tab = 'security'" :class="tab === 'security' ? 'border-slate-900 text-slate-900 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-600 font-medium'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Keamanan & Password
            </button>
        </div>

        <!-- Tab Contents -->
        <div class="p-6">
            <!-- 1. Data Diri -->
            <div x-show="tab === 'diri'" class="space-y-6">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Identitas Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">Nama Lengkap</span>
                        <strong class="text-slate-900 font-medium">{{ $student->full_name }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Nama Panggilan</span>
                        <strong class="text-slate-900 font-medium">{{ $student->nickname ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">NIS / NISN</span>
                        <strong class="text-slate-900 font-medium">{{ $student->nis }} / {{ $student->nisn ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">NIK</span>
                        <strong class="text-slate-900 font-medium">{{ $student->nik ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Tempat / Tanggal Lahir</span>
                        <strong class="text-slate-900 font-medium">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date ? Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Jenis Kelamin</span>
                        <strong class="text-slate-900 font-medium">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Agama</span>
                        <strong class="text-slate-900 font-medium">{{ $student->religion ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kewarganegaraan</span>
                        <strong class="text-slate-900 font-medium">{{ $student->nationality }}</strong>
                    </div>
                </div>
            </div>

            <!-- 2. Alamat & Kontak -->
            <div x-show="tab === 'alamat'" class="space-y-6" style="display: none;">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Kontak & Alamat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">No. HP Siswa</span>
                        <strong class="text-slate-900 font-medium">{{ $student->phone ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">No. WhatsApp Siswa</span>
                        <strong class="text-slate-900 font-medium">{{ $student->whatsapp ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Email Siswa</span>
                        <strong class="text-slate-900 font-medium">{{ $student->email ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Alamat Lengkap</span>
                        <strong class="text-slate-900 font-medium">{{ $student->address ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">RT / RW</span>
                        <strong class="text-slate-900 font-medium">{{ $student->rt ?? '-' }} / {{ $student->rw ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Desa / Kelurahan</span>
                        <strong class="text-slate-900 font-medium">{{ $student->village ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kecamatan</span>
                        <strong class="text-slate-900 font-medium">{{ $student->district ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kabupaten / Kota</span>
                        <strong class="text-slate-900 font-medium">{{ $student->city ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Provinsi</span>
                        <strong class="text-slate-900 font-medium">{{ $student->province ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kode Pos</span>
                        <strong class="text-slate-900 font-medium">{{ $student->postal_code ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Orang Tua / Wali -->
            <div x-show="tab === 'ortu'" class="space-y-8" style="display: none;">
                <!-- Ayah -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Data Ayah Kandung</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <span class="text-slate-400 block mb-1">Nama Ayah</span>
                            <strong class="text-slate-900 font-medium">{{ $student->father_name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">NIK Ayah</span>
                            <strong class="text-slate-900 font-medium">{{ $student->father_nik ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Tahun Lahir Ayah</span>
                            <strong class="text-slate-900 font-medium">{{ $student->father_birth_year ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Pendidikan Terakhir Ayah</span>
                            <strong class="text-slate-900 font-medium">{{ $student->father_education ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Pekerjaan Ayah</span>
                            <strong class="text-slate-900 font-medium">{{ $student->father_occupation ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Penghasilan Bulanan Ayah</span>
                            <strong class="text-slate-900 font-medium">{{ $student->father_income ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100" />

                <!-- Ibu -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Data Ibu Kandung</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <span class="text-slate-400 block mb-1">Nama Ibu</span>
                            <strong class="text-slate-900 font-medium">{{ $student->mother_name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">NIK Ibu</span>
                            <strong class="text-slate-900 font-medium">{{ $student->mother_nik ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Tahun Lahir Ibu</span>
                            <strong class="text-slate-900 font-medium">{{ $student->mother_birth_year ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Pendidikan Terakhir Ibu</span>
                            <strong class="text-slate-900 font-medium">{{ $student->mother_education ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Pekerjaan Ibu</span>
                            <strong class="text-slate-900 font-medium">{{ $student->mother_occupation ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Penghasilan Bulanan Ibu</span>
                            <strong class="text-slate-900 font-medium">{{ $student->mother_income ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100" />

                <!-- Wali -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Data Wali (Opsional)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <span class="text-slate-400 block mb-1">Nama Wali</span>
                            <strong class="text-slate-900 font-medium">{{ $student->visitor_wali_name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Hubungan Wali</span>
                            <strong class="text-slate-900 font-medium">{{ $student->wali_relationship ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Pekerjaan Wali</span>
                            <strong class="text-slate-900 font-medium">{{ $student->wali_occupation ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">No. HP Wali</span>
                            <strong class="text-slate-900 font-medium">{{ $student->wali_phone ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Detail Fisik & Tambahan -->
            <div x-show="tab === 'tambahan'" class="space-y-6" style="display: none;">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Detail Fisik & Transportasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-slate-400 block mb-1">Anak Ke / Jumlah Saudara</span>
                        <strong class="text-slate-900 font-medium">{{ $student->child_number ?? '-' }} / {{ $student->sibling_count ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Tinggi / Berat Badan</span>
                        <strong class="text-slate-900 font-medium">{{ $student->height_cm ?? '-' }} cm / {{ $student->weight_kg ?? '-' }} kg</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Alat Transportasi ke Sekolah</span>
                        <strong class="text-slate-900 font-medium">{{ $student->transportation ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Jarak Rumah ke Sekolah</span>
                        <strong class="text-slate-900 font-medium">{{ $student->home_distance_km ?? '-' }} km</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Waktu Tempuh ke Sekolah</span>
                        <strong class="text-slate-900 font-medium">{{ $student->travel_time_minutes ?? '-' }} Menit</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kebutuhan Khusus</span>
                        <strong class="text-slate-900 font-medium">{{ $student->special_needs ?? '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 5. Keamanan & Password -->
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
    </div>

    <!-- Grid: History & Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Class History -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Rombongan Belajar</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="pb-3">Tahun Ajaran</th>
                            <th class="pb-3">Semester</th>
                            <th class="pb-3">Rombel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($classHistory as $ch)
                            <tr class="text-slate-600 font-medium">
                                <td class="py-3">{{ $ch->year_name }}</td>
                                <td class="py-3">{{ $ch->semester }}</td>
                                <td class="py-3 text-slate-900 font-semibold">{{ $ch->class_name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-slate-400 text-xs font-medium">Belum ada riwayat rombel.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Audit/Change logs -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Perbaikan Profil</h3>
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
                    <div class="text-center py-8 text-slate-400 text-xs font-medium">Belum ada riwayat perbaikan data profil.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
