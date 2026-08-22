@extends('layouts.app')

@section('title', 'Perbaiki Profil Siswa')

@section('content')
<div class="space-y-8" x-data="{ activeTab: 'diri' }">
    <!-- Header Back -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('student.dashboard') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 inline-flex items-center gap-1 transition">
                &larr; Kembali ke Dashboard
            </a>
            <h2 class="text-2xl font-bold text-slate-900 mt-2">Formulir Perbaikan Profil</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Periksa data Anda kembali dan lakukan koreksi jika ada kesalahan.</p>
        </div>
    </div>

    <!-- Alert / Warning -->
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 shadow-sm">
        <h4 class="text-base font-bold text-amber-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            PERHATIAN
        </h4>
        <p class="text-sm text-amber-700 mt-2 leading-relaxed font-medium">
            Anda hanya dapat melakukan perubahan data <strong>1 kali dalam semester ini</strong>. 
            Semua perubahan akan dicatat ke dalam log audit sekolah. Pastikan Anda memeriksa ulang seluruh tab sebelum menekan tombol <strong>Kirim Perubahan</strong>.
        </p>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('student.update') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" onsubmit="event.preventDefault(); confirmAction({ title: 'Kirim Perubahan Data', message: 'Apakah Anda yakin data yang diisi sudah lengkap dan benar? Tindakan ini akan mengunci hak edit Anda.', type: 'info', confirmText: 'Ya, Kirim' }, () => this.submit());">
        @csrf

        <!-- Tab Headers -->
        <div class="flex border-b border-slate-100 overflow-x-auto bg-slate-50/50">
            <button type="button" @click="activeTab = 'diri'" :class="activeTab === 'diri' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Data Diri
            </button>
            <button type="button" @click="activeTab = 'alamat'" :class="activeTab === 'alamat' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Alamat & Kontak
            </button>
            <button type="button" @click="activeTab = 'ortu'" :class="activeTab === 'ortu' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Orang Tua & Wali
            </button>
            <button type="button" @click="activeTab = 'tambahan'" :class="activeTab === 'tambahan' ? 'border-slate-900 text-slate-900 font-bold bg-white' : 'border-transparent text-slate-400 hover:text-slate-600 font-semibold'" class="px-6 py-4 border-b-2 text-sm transition whitespace-nowrap outline-none">
                Fisik & Tambahan
            </button>
        </div>

        <div class="p-6 sm:p-8">
            <!-- 1. Tab Data Diri -->
            <div x-show="activeTab === 'diri'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Lengkap (Tidak dapat diubah)</label>
                        <input type="text" value="{{ $student->full_name }}" disabled class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm font-medium outline-none" />
                    </div>
                    <div>
                        <label for="nickname" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Panggilan</label>
                        <input type="text" id="nickname" name="nickname" value="{{ old('nickname', $student->nickname) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">NIS (Tidak dapat diubah)</label>
                        <input type="text" value="{{ $student->nis }}" disabled class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm font-medium outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">NISN (Tidak dapat diubah)</label>
                        <input type="text" value="{{ $student->nisn }}" disabled class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-500 text-sm font-medium outline-none" />
                    </div>
                    <div>
                        <label for="birth_place" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tempat Lahir</label>
                        <input type="text" id="birth_place" name="birth_place" value="{{ old('birth_place', $student->birth_place) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="birth_date" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Lahir</label>
                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $student->birth_date) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="religion" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Agama</label>
                        <input type="text" id="religion" name="religion" value="{{ old('religion', $student->religion) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="nationality" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kewarganegaraan</label>
                        <input type="text" id="nationality" name="nationality" value="{{ old('nationality', $student->nationality) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                </div>
            </div>

            <!-- 2. Tab Alamat & Kontak -->
            <div x-show="activeTab === 'alamat'" class="space-y-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. Telepon Rumah</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $student->phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="whatsapp" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. WhatsApp</label>
                        <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $student->whatsapp) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email Pribadi</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Rumah (Jalan, No, Blok)</label>
                        <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition">{{ old('address', $student->address) }}</textarea>
                    </div>
                    <div>
                        <label for="rt" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">RT</label>
                        <input type="text" id="rt" name="rt" value="{{ old('rt', $student->rt) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="rw" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">RW</label>
                        <input type="text" id="rw" name="rw" value="{{ old('rw', $student->rw) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="village" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Desa / Kelurahan</label>
                        <input type="text" id="village" name="village" value="{{ old('village', $student->village) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="district" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kecamatan</label>
                        <input type="text" id="district" name="district" value="{{ old('district', $student->district) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="city" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kabupaten / Kota</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $student->city) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="province" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Provinsi</label>
                        <input type="text" id="province" name="province" value="{{ old('province', $student->province) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="postal_code" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kode Pos</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $student->postal_code) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                </div>
            </div>

            <!-- 3. Tab Orang Tua & Wali -->
            <div x-show="activeTab === 'ortu'" class="space-y-8" style="display: none;">
                <!-- Ayah -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-2">Data Ayah Kandung</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="father_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap Ayah</label>
                            <input type="text" id="father_name" name="father_name" value="{{ old('father_name', $student->father_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="father_nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIK Ayah</label>
                            <input type="text" id="father_nik" name="father_nik" value="{{ old('father_nik', $student->father_nik) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="father_birth_year" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Lahir Ayah</label>
                            <input type="number" id="father_birth_year" name="father_birth_year" value="{{ old('father_birth_year', $student->father_birth_year) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="father_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pendidikan Terakhir Ayah</label>
                            <input type="text" id="father_education" name="father_education" value="{{ old('father_education', $student->father_education) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="father_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pekerjaan Ayah</label>
                            <input type="text" id="father_occupation" name="father_occupation" value="{{ old('father_occupation', $student->father_occupation) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="father_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Penghasilan Ayah</label>
                            <input type="text" id="father_income" name="father_income" value="{{ old('father_income', $student->father_income) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="father_phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. HP Ayah</label>
                            <input type="text" id="father_phone" name="father_phone" value="{{ old('father_phone', $student->father_phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                    </div>
                </div>

                <!-- Ibu -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-2">Data Ibu Kandung</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mother_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap Ibu</label>
                            <input type="text" id="mother_name" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="mother_nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIK Ibu</label>
                            <input type="text" id="mother_nik" name="mother_nik" value="{{ old('mother_nik', $student->mother_nik) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="mother_birth_year" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun Lahir Ibu</label>
                            <input type="number" id="mother_birth_year" name="mother_birth_year" value="{{ old('mother_birth_year', $student->mother_birth_year) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="mother_education" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pendidikan Terakhir Ibu</label>
                            <input type="text" id="mother_education" name="mother_education" value="{{ old('mother_education', $student->mother_education) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="mother_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pekerjaan Ibu</label>
                            <input type="text" id="mother_occupation" name="mother_occupation" value="{{ old('mother_occupation', $student->mother_occupation) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="mother_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Penghasilan Ibu</label>
                            <input type="text" id="mother_income" name="mother_income" value="{{ old('mother_income', $student->mother_income) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="mother_phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. HP Ibu</label>
                            <input type="text" id="mother_phone" name="mother_phone" value="{{ old('mother_phone', $student->mother_phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                    </div>
                </div>

                <!-- Wali -->
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 pb-2">Data Wali (Isi jika tinggal bersama wali)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="visitor_wali_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap Wali</label>
                            <input type="text" id="visitor_wali_name" name="visitor_wali_name" value="{{ old('visitor_wali_name', $student->visitor_wali_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="wali_nik" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">NIK Wali</label>
                            <input type="text" id="wali_nik" name="wali_nik" value="{{ old('wali_nik', $student->wali_nik) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="wali_relationship" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hubungan Hubungan Wali</label>
                            <input type="text" id="wali_relationship" name="wali_relationship" value="{{ old('wali_relationship', $student->wali_relationship) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="wali_occupation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pekerjaan Wali</label>
                            <input type="text" id="wali_occupation" name="wali_occupation" value="{{ old('wali_occupation', $student->wali_occupation) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="wali_income" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Penghasilan Wali</label>
                            <input type="text" id="wali_income" name="wali_income" value="{{ old('wali_income', $student->wali_income) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                        <div>
                            <label for="wali_phone" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">No. HP Wali</label>
                            <input type="text" id="wali_phone" name="wali_phone" value="{{ old('wali_phone', $student->wali_phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Tab Fisik & Tambahan -->
            <div x-show="activeTab === 'tambahan'" class="space-y-6" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="child_number" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Anak Ke-</label>
                        <input type="number" id="child_number" name="child_number" value="{{ old('child_number', $student->child_number) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="sibling_count" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jumlah Saudara Kandung</label>
                        <input type="number" id="sibling_count" name="sibling_count" value="{{ old('sibling_count', $student->sibling_count) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="height_cm" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tinggi Badan (cm)</label>
                        <input type="number" id="height_cm" name="height_cm" value="{{ old('height_cm', $student->height_cm) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="weight_kg" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Berat Badan (kg)</label>
                        <input type="number" id="weight_kg" name="weight_kg" value="{{ old('weight_kg', $student->weight_kg) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="transportation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alat Transportasi</label>
                        <input type="text" id="transportation" name="transportation" value="{{ old('transportation', $student->transportation) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="home_distance_km" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jarak Rumah ke Sekolah (km)</label>
                        <input type="text" id="home_distance_km" name="home_distance_km" value="{{ old('home_distance_km', $student->home_distance_km) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="travel_time_minutes" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Waktu Tempuh (menit)</label>
                        <input type="text" id="travel_time_minutes" name="travel_time_minutes" value="{{ old('travel_time_minutes', $student->travel_time_minutes) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                    <div>
                        <label for="special_needs" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kebutuhan Khusus</label>
                        <input type="text" id="special_needs" name="special_needs" value="{{ old('special_needs', $student->special_needs) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Footer -->
        <div class="px-6 py-5 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <span class="text-xs text-slate-400 font-medium">Pastikan semua data di 4 tab sudah diperiksa.</span>
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow-sm transition active:scale-[0.98]">
                Kirim Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
