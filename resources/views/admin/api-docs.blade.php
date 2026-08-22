@extends('layouts.app')

@section('title', 'Dokumentasi API - SobatData Neper')

@section('content')
<div class="space-y-6" x-data="{ activeSection: 'start', copiedKey: null }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Dokumentasi REST API</h2>
            <p class="text-sm text-slate-400 font-medium mt-0.5">SobatData Neper &bull; Pusat Data Terintegrasi SMK Negeri 1 Cirebon</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.clients.index') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition">
                Kelola API Client →
            </a>
        </div>
    </div>

    <!-- Layout: Sidebar + Content -->
    <div class="flex flex-col lg:flex-row gap-6 items-start">

        <!-- Sidebar Navigation -->
        <div class="w-full lg:w-56 shrink-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 lg:sticky lg:top-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Navigasi</p>
            <nav class="space-y-0.5 text-sm font-semibold">
                @foreach([
                    ['id'=>'start',      'label'=>'🚀 Cara Mulai'],
                    ['id'=>'auth',       'label'=>'🔐 Autentikasi'],
                    ['id'=>'students',   'label'=>'👩‍🎓 Siswa'],
                    ['id'=>'employees',  'label'=>'👨‍🏫 Guru & Tendik'],
                    ['id'=>'classes',    'label'=>'🏫 Kelas & Jurusan'],
                    ['id'=>'stats',      'label'=>'📊 Statistik'],
                    ['id'=>'errors',     'label'=>'⚠️ Error Codes'],
                    ['id'=>'examples',   'label'=>'💻 Contoh Kode'],
                ] as $nav)
                <button @click="activeSection = '{{ $nav['id'] }}'"
                    :class="activeSection === '{{ $nav['id'] }}' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-50'"
                    class="w-full text-left px-3 py-2 rounded-lg transition text-xs">
                    {{ $nav['label'] }}
                </button>
                @endforeach
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 space-y-4">

            <!-- ===== CARA MULAI ===== -->
            <div x-show="activeSection === 'start'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">🚀 Cara Menggunakan API SobatData Neper</h3>
                        <p class="text-sm text-slate-500 mt-1">Ikuti langkah-langkah berikut untuk mengintegrasikan data dari SobatData Neper ke aplikasi Anda.</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Step 1 -->
                        <div class="flex gap-4 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                            <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-black text-sm shrink-0">1</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Buat API Client</h4>
                                <p class="text-xs text-slate-600 mt-0.5">Masuk ke menu <strong>API Management → Kelola API Client</strong>, klik "Tambah Aplikasi", isi nama aplikasi dan pilih <em>scope</em> (hak akses) yang diperlukan. Anda akan mendapat <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-blue-700 font-mono">Client ID</code> dan <code class="bg-white px-1 py-0.5 rounded border border-slate-200 text-blue-700 font-mono">Client Secret</code>.</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.clients.index') }}" class="text-xs font-bold text-blue-600 hover:underline">→ Ke Halaman API Client</a>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex gap-4 p-4 bg-violet-50 border border-violet-100 rounded-xl">
                            <div class="w-8 h-8 bg-violet-600 text-white rounded-full flex items-center justify-center font-black text-sm shrink-0">2</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Kirim Header Autentikasi</h4>
                                <p class="text-xs text-slate-600 mt-0.5">Setiap request API harus menyertakan dua header berikut:</p>
                                <div class="mt-2 bg-slate-900 rounded-lg p-3 font-mono text-xs text-emerald-300 space-y-1">
                                    <div><span class="text-slate-400">X-Client-ID:</span> <span class="text-amber-300">client_xxxxxxxxxxxxxxxx</span></div>
                                    <div><span class="text-slate-400">X-Client-Secret:</span> <span class="text-amber-300">secret_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex gap-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl">
                            <div class="w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-black text-sm shrink-0">3</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Panggil Endpoint API</h4>
                                <p class="text-xs text-slate-600 mt-0.5">Base URL API Anda (setelah hosting):</p>
                                <div class="mt-2 bg-slate-900 rounded-lg p-3 font-mono text-xs text-blue-300">
                                    https://domain-anda.com/api/v1/
                                </div>
                                <p class="text-xs text-slate-500 mt-1.5">Semua response bertipe JSON dengan format standar.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Base URL & Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-100">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Format Response</p>
                            <p class="text-sm font-bold text-slate-900 mt-1">JSON</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Versi API</p>
                            <p class="text-sm font-bold text-slate-900 mt-1">v1</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rate Limit</p>
                            <p class="text-sm font-bold text-slate-900 mt-1">60 req/menit</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== AUTENTIKASI ===== -->
            <div x-show="activeSection === 'auth'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                    <h3 class="text-lg font-bold text-slate-900">🔐 Autentikasi</h3>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                        <strong>Penting:</strong> API ini menggunakan <strong>API Key</strong> berbasis header, bukan OAuth/Bearer token. Simpan kredensial Anda dengan aman dan jangan expose ke frontend publik.
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-2">Header yang Diperlukan</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <tr><th class="px-4 py-2.5">Header</th><th class="px-4 py-2.5">Deskripsi</th><th class="px-4 py-2.5">Wajib</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-mono text-xs">
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-blue-700 font-bold">X-Client-ID</td>
                                        <td class="px-4 py-3 text-slate-600 font-sans">ID unik aplikasi Anda (dimulai dengan <code>client_</code>)</td>
                                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-[10px] font-bold">Ya</span></td>
                                    </tr>
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-blue-700 font-bold">X-Client-Secret</td>
                                        <td class="px-4 py-3 text-slate-600 font-sans">Secret token rahasia Anda (dimulai dengan <code>secret_</code>)</td>
                                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-[10px] font-bold">Ya</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-2">Scope (Hak Akses)</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <tr><th class="px-4 py-2.5">Scope</th><th class="px-4 py-2.5">Akses</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach([
                                        ['students.read',   'Membaca data publik siswa (nama, NIS, kelas)'],
                                        ['students.private','Membaca data sensitif siswa (NIK, kontak, alamat, orang tua)'],
                                        ['teachers.read',   'Membaca data guru & tendik'],
                                        ['classes.read',    'Membaca data rombel dan jurusan'],
                                        ['*',               'Akses penuh ke semua endpoint (Super Admin)'],
                                    ] as [$scope, $desc])
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-mono text-violet-700 font-bold">{{ $scope }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $desc }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-2">Health Check (Tanpa Autentikasi)</h4>
                        @include('admin.partials.api-endpoint', [
                            'method' => 'GET',
                            'path' => '/api/health',
                            'desc' => 'Cek status server. Tidak memerlukan autentikasi.',
                            'response' => '{"status":"ok","database":"connected","api":"running","timestamp":"2026-08-22T07:00:00+07:00"}',
                        ])
                    </div>
                </div>
            </div>

            <!-- ===== SISWA ===== -->
            <div x-show="activeSection === 'students'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900">👩‍🎓 Endpoint Siswa</h3>
                    <p class="text-sm text-slate-500 -mt-4">Memerlukan scope: <code class="bg-violet-50 text-violet-700 px-1.5 py-0.5 rounded border border-violet-200 font-mono text-xs">students.read</code></p>

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/students',
                        'desc' => 'Ambil daftar semua siswa dengan pagination. Mendukung berbagai filter.',
                        'params' => [
                            ['name'=>'page',          'type'=>'integer', 'desc'=>'Halaman ke-berapa (default: 1)'],
                            ['name'=>'search',        'type'=>'string',  'desc'=>'Cari berdasarkan nama, NIS, atau NISN'],
                            ['name'=>'gender',        'type'=>'string',  'desc'=>'Filter jenis kelamin: L atau P'],
                            ['name'=>'status',        'type'=>'string',  'desc'=>'Filter status: active, graduated, alumni, moved, left'],
                            ['name'=>'grade',         'type'=>'integer', 'desc'=>'Filter tingkat: 10, 11, atau 12'],
                            ['name'=>'major',         'type'=>'string',  'desc'=>'Filter kode jurusan: PPLG, DPIB, TJKT, dll'],
                            ['name'=>'class',         'type'=>'string',  'desc'=>'Filter nama kelas: X PPLG 1 (spasi → strip: X-PPLG-1)'],
                            ['name'=>'updated_since', 'type'=>'string',  'desc'=>'Delta sync - ISO 8601: 2026-08-01T00:00:00Z'],
                        ],
                        'response' => '{"success":true,"data":[{"uuid":"xxx","nis":"12631079","nisn":"0118428509","full_name":"AFIFA","gender":"P","status":"Aktif","class":{"name":"X DPIB 1","major":"DPIB"},"updated_at":"2026-08-22T06:00:00.000Z"}],"meta":{"current_page":1,"last_page":51,"per_page":50,"total":2534}}',
                    ])

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/students/{uuid}',
                        'desc' => 'Ambil detail lengkap satu siswa berdasarkan UUID. Dengan scope students.private, data sensitif ikut dikembalikan.',
                        'params' => [
                            ['name'=>'uuid', 'type'=>'string', 'desc'=>'UUID siswa (dari endpoint list)'],
                        ],
                        'response' => '{"success":true,"data":{"uuid":"abc-def","nis":"12631079","full_name":"AFIFA","gender":"P","birth_date":"2008-05-15","class":{"name":"X DPIB 1","major":"DPIB","grade":10}}}',
                    ])

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/students/{uuid}/class-history',
                        'desc' => 'Riwayat kelas siswa dari tahun ajaran ke tahun ajaran.',
                        'params' => [
                            ['name'=>'uuid', 'type'=>'string', 'desc'=>'UUID siswa'],
                        ],
                        'response' => '{"success":true,"data":[{"class_name":"X DPIB 1","school_year":"2026/2027","semester":"Ganjil","attendance_number":5}]}',
                    ])
                </div>
            </div>

            <!-- ===== GURU & TENDIK ===== -->
            <div x-show="activeSection === 'employees'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900">👨‍🏫 Endpoint Guru & Tendik</h3>
                    <p class="text-sm text-slate-500 -mt-4">Memerlukan scope: <code class="bg-violet-50 text-violet-700 px-1.5 py-0.5 rounded border border-violet-200 font-mono text-xs">teachers.read</code></p>

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/employees',
                        'desc' => 'Daftar seluruh guru dan tenaga kependidikan.',
                        'params' => [
                            ['name'=>'type',   'type'=>'string', 'desc'=>'Filter: guru atau tendik'],
                            ['name'=>'status', 'type'=>'string', 'desc'=>'Filter status: Active, Inactive'],
                            ['name'=>'search', 'type'=>'string', 'desc'=>'Cari berdasarkan nama atau NIP'],
                        ],
                        'response' => '{"success":true,"data":[{"uuid":"xxx","nip":"198001012005011001","full_name":"Budi Santoso, S.Pd","type":"Guru","status":"Active","subjects":["Matematika"]}],"meta":{...}}',
                    ])

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/employees/{uuid}',
                        'desc' => 'Detail lengkap satu guru/tendik.',
                        'params' => [
                            ['name'=>'uuid', 'type'=>'string', 'desc'=>'UUID guru/tendik'],
                        ],
                        'response' => '{"success":true,"data":{"uuid":"xxx","nip":"...","full_name":"...","type":"Guru","status":"Active"}}',
                    ])
                </div>
            </div>

            <!-- ===== KELAS & JURUSAN ===== -->
            <div x-show="activeSection === 'classes'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900">🏫 Endpoint Kelas & Jurusan</h3>
                    <p class="text-sm text-slate-500 -mt-4">Memerlukan scope: <code class="bg-violet-50 text-violet-700 px-1.5 py-0.5 rounded border border-violet-200 font-mono text-xs">classes.read</code></p>

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/classes',
                        'desc' => 'Daftar seluruh rombel aktif beserta jurusan dan wali kelas.',
                        'params' => [],
                        'response' => '{"success":true,"data":[{"id":1,"name":"X DPIB 1","grade_level":10,"major":{"code":"DPIB","name":"Desain Permodelan dan Informasi Bangunan"},"homeroom_teacher":"Budi S."}]}',
                    ])

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/classes/{id_or_name}/students',
                        'desc' => 'Daftar siswa dalam satu kelas. Bisa gunakan ID numerik atau nama kelas.',
                        'params' => [
                            ['name'=>'id_or_name', 'type'=>'string', 'desc'=>'ID numerik kelas atau nama kelas (e.g. X-DPIB-1)'],
                        ],
                        'response' => '{"success":true,"class":"X DPIB 1","total":36,"data":[{"uuid":"...","nis":"12631079","full_name":"AFIFA","gender":"P","attendance_number":1}]}',
                    ])
                </div>
            </div>

            <!-- ===== STATISTIK ===== -->
            <div x-show="activeSection === 'stats'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900">📊 Statistik & Referensi</h3>
                    <p class="text-sm text-slate-500 -mt-4">Endpoint ini tidak memerlukan scope khusus, hanya autentikasi dasar.</p>

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/statistics/students',
                        'desc' => 'Statistik ringkas jumlah siswa: total, jenis kelamin, dan per tingkat.',
                        'params' => [],
                        'response' => '{"success":true,"data":{"total":2534,"male":1102,"female":1432,"grade":{"10":862,"11":844,"12":828}}}',
                    ])

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/school-years',
                        'desc' => 'Daftar tahun ajaran.',
                        'params' => [],
                        'response' => '{"success":true,"data":[{"id":1,"name":"2026/2027","is_active":true}]}',
                    ])

                    @include('admin.partials.api-endpoint', [
                        'method' => 'GET',
                        'path' => '/api/v1/semesters',
                        'desc' => 'Daftar semester beserta status aktif.',
                        'params' => [],
                        'response' => '{"success":true,"data":[{"id":1,"school_year":"2026/2027","semester":"Ganjil","is_active":true}]}',
                    ])
                </div>
            </div>

            <!-- ===== ERROR CODES ===== -->
            <div x-show="activeSection === 'errors'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">⚠️ Kode Error</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">HTTP Code</th>
                                    <th class="px-4 py-3">Arti</th>
                                    <th class="px-4 py-3">Solusi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach([
                                    ['200','OK','Request berhasil. Data tersedia di field <code>data</code>.', 'bg-emerald-50 text-emerald-700'],
                                    ['400','Bad Request','Parameter tidak valid (mis. format tanggal salah).', 'bg-amber-50 text-amber-700'],
                                    ['401','Unauthorized','Header X-Client-ID atau X-Client-Secret salah/tidak ada.', 'bg-red-50 text-red-700'],
                                    ['403','Forbidden','Scope tidak mencukupi. Minta admin tambahkan scope yang diperlukan.', 'bg-orange-50 text-orange-700'],
                                    ['404','Not Found','Data yang dicari tidak ditemukan.', 'bg-slate-50 text-slate-600'],
                                    ['429','Too Many Requests','Rate limit tercapai (default: 60 req/menit). Tunggu sebentar.', 'bg-red-50 text-red-700'],
                                    ['500','Server Error','Kesalahan internal server. Hubungi admin SobatData Neper.', 'bg-red-50 text-red-700'],
                                ] as [$code, $status, $desc, $badge])
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-black border {{ $badge }}">{{ $code }}</span></td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $status }}</td>
                                    <td class="px-4 py-3 text-slate-600">{!! $desc !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        <h4 class="text-sm font-bold text-slate-800 mb-2">Format Response Error</h4>
                        <div class="bg-slate-900 rounded-xl p-4 font-mono text-xs text-rose-300">
                            <div>&#123;</div>
                            <div class="pl-4"><span class="text-slate-400">"success"</span>: <span class="text-red-400">false</span>,</div>
                            <div class="pl-4"><span class="text-slate-400">"message"</span>: <span class="text-amber-300">"Kredensial API tidak valid atau aplikasi dinonaktifkan."</span></div>
                            <div>&#125;</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== CONTOH KODE ===== -->
            <div x-show="activeSection === 'examples'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900">💻 Contoh Kode Integrasi</h3>

                    <!-- JavaScript / Fetch -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-slate-800">JavaScript (Fetch API)</h4>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('code-js').innerText)"
                                class="text-xs text-slate-400 hover:text-slate-700 font-semibold transition">Salin</button>
                        </div>
                        <pre id="code-js" class="bg-slate-900 text-emerald-300 rounded-xl p-4 font-mono text-xs overflow-x-auto leading-relaxed">const response = await fetch('https://domain-anda.com/api/v1/students?grade=10', {
  method: 'GET',
  headers: {
    'X-Client-ID': 'client_xxxxxxxxxxxxxxxxxxxx',
    'X-Client-Secret': 'secret_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'Accept': 'application/json',
  }
});

const { success, data, meta } = await response.json();

if (success) {
  console.log(`Total: ${meta.total} siswa`);
  data.forEach(siswa => {
    console.log(`${siswa.nis} - ${siswa.full_name} (${siswa.class?.name})`);
  });
}</pre>
                    </div>

                    <!-- PHP / cURL -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-slate-800">PHP (cURL)</h4>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('code-php').innerText)"
                                class="text-xs text-slate-400 hover:text-slate-700 font-semibold transition">Salin</button>
                        </div>
                        <pre id="code-php" class="bg-slate-900 text-blue-300 rounded-xl p-4 font-mono text-xs overflow-x-auto leading-relaxed">&lt;?php

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => 'https://domain-anda.com/api/v1/students',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'X-Client-ID: client_xxxxxxxxxxxxxxxxxxxx',
    'X-Client-Secret: secret_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'Accept: application/json',
  ],
]);

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

if ($data['success']) {
  foreach ($data['data'] as $siswa) {
    echo $siswa['nis'] . ' - ' . $siswa['full_name'] . "\n";
  }
}</pre>
                    </div>

                    <!-- Python -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-slate-800">Python (requests)</h4>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('code-python').innerText)"
                                class="text-xs text-slate-400 hover:text-slate-700 font-semibold transition">Salin</button>
                        </div>
                        <pre id="code-python" class="bg-slate-900 text-yellow-300 rounded-xl p-4 font-mono text-xs overflow-x-auto leading-relaxed">import requests

headers = {
    'X-Client-ID': 'client_xxxxxxxxxxxxxxxxxxxx',
    'X-Client-Secret': 'secret_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    'Accept': 'application/json',
}

r = requests.get('https://domain-anda.com/api/v1/students', headers=headers, params={'grade': 10})
result = r.json()

if result['success']:
    for siswa in result['data']:
        print(f"{siswa['nis']} - {siswa['full_name']}")</pre>
                    </div>

                    <!-- CORS Note -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-800">
                        <strong>🌐 Catatan CORS untuk Frontend Web:</strong><br>
                        Jika menggunakan JavaScript di browser (React, Vue, dsb.), panggil API dari <strong>backend server</strong> Anda (server-side), bukan langsung dari browser. Menyimpan <code>X-Client-Secret</code> di frontend publik adalah risiko keamanan.<br><br>
                        Alternatifnya, minta admin SobatData Neper untuk mengaktifkan CORS whitelist untuk domain Anda.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
