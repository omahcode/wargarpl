@extends('layouts.app')

@section('title', 'Dokumentasi API - WargaRPL')

@section('content')
<div class="space-y-6" x-data="{ activeSection: 'start', copiedKey: null }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-2xl font-bold text-slate-900">Dokumentasi REST API</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">WargaRPL API</span>
            </div>
            <p class="text-sm text-slate-400 font-medium mt-0.5">WargaRPL &bull; Bank Data Terpadu Siswa RPL & PPLG SMK Negeri 1 Cirebon</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.clients.index') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                Kelola API Token →
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
                    ['id'=>'auth',       'label'=>'🔐 Autentikasi Token'],
                    ['id'=>'students',   'label'=>'👩‍🎓 Siswa RPL/PPLG'],
                    ['id'=>'employees',  'label'=>'👨‍🏫 Guru Produktif'],
                    ['id'=>'classes',    'label'=>'🏫 Rombel & Kelas'],
                    ['id'=>'stats',      'label'=>'📊 Statistik'],
                    ['id'=>'errors',     'label'=>'⚠️ Error Codes'],
                    ['id'=>'examples',   'label'=>'💻 Contoh Kode'],
                ] as $nav)
                <button @click="activeSection = '{{ $nav['id'] }}'"
                    :class="activeSection === '{{ $nav['id'] }}' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-50'"
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
                        <h3 class="text-lg font-bold text-slate-900">🚀 Cara Menggunakan API WargaRPL</h3>
                        <p class="text-sm text-slate-500 mt-1">Gunakan API WargaRPL untuk mengakses data siswa RPL/PPLG secara aman menggunakan Bearer Token.</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Step 1 -->
                        <div class="flex gap-4 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                            <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-black text-sm shrink-0">1</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Generate API Token</h4>
                                <p class="text-xs text-slate-600 mt-0.5">Buka menu <strong>Integrasi API → API Token</strong>, klik tombol <strong>"Generate API Token Baru"</strong>, beri nama aplikasi Anda dan pilih hak akses (scope). Sistem akan men-generate token dengan format <code class="bg-white px-1.5 py-0.5 rounded border border-slate-200 text-indigo-700 font-mono font-bold">wgr_live_...</code>.</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.clients.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">→ Ke Halaman Manajemen Token</a>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex gap-4 p-4 bg-violet-50 border border-violet-100 rounded-xl">
                            <div class="w-8 h-8 bg-violet-600 text-white rounded-full flex items-center justify-center font-black text-sm shrink-0">2</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Kirim Header Authorization</h4>
                                <p class="text-xs text-slate-600 mt-0.5">Sertakan token Anda pada setiap HTTP request dengan format Bearer standard:</p>
                                <div class="mt-2 bg-slate-900 rounded-lg p-3 font-mono text-xs text-emerald-300 space-y-1">
                                    <div><span class="text-slate-400">Authorization:</span> <span class="text-cyan-300">Bearer wgr_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxx</span></div>
                                    <div class="text-[11px] text-slate-500 pt-1">Atau alternatif: <span class="text-slate-400">X-API-Token:</span> <span class="text-cyan-300">wgr_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxx</span></div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex gap-4 p-4 bg-emerald-50 border border-emerald-100 rounded-xl">
                            <div class="w-8 h-8 bg-emerald-600 text-white rounded-full flex items-center justify-center font-black text-sm shrink-0">3</div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Panggil Endpoint API</h4>
                                <p class="text-xs text-slate-600 mt-0.5">Endpoint API WargaRPL:</p>
                                <div class="mt-2 bg-slate-900 rounded-lg p-3 font-mono text-xs text-cyan-300">
                                    {{ url('/api/v1/') }}/
                                </div>
                                <p class="text-xs text-slate-500 mt-1.5">Semua respons berformat standar JSON.</p>
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
                            <p class="text-sm font-bold text-slate-900 mt-1">v1 (WargaRPL)</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rate Limit Default</p>
                            <p class="text-sm font-bold text-slate-900 mt-1">60 req/menit</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== AUTENTIKASI ===== -->
            <div x-show="activeSection === 'auth'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                    <h3 class="text-lg font-bold text-slate-900">🔐 Autentikasi Token WargaRPL</h3>

                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-sm text-indigo-900 leading-relaxed">
                        <strong>Autentikasi Token Tunggal (Bearer Token):</strong> Anda hanya perlu men-generate satu token dari halaman admin WargaRPL dan memasukkannya ke header HTTP. Tidak perlu konfigurasi rumit client secret ganda.
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-2">Header yang Didukung</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <tr><th class="px-4 py-2.5">Header</th><th class="px-4 py-2.5">Contoh Nilai</th><th class="px-4 py-2.5">Status</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-mono text-xs">
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-indigo-700 font-bold">Authorization</td>
                                        <td class="px-4 py-3 text-slate-700"><code>Bearer wgr_live_xxxxxxxxxxxxxxxxxxxx</code></td>
                                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-[10px] font-bold font-sans">Direkomendasikan</span></td>
                                    </tr>
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 text-indigo-700 font-bold">X-API-Token</td>
                                        <td class="px-4 py-3 text-slate-700"><code>wgr_live_xxxxxxxxxxxxxxxxxxxx</code></td>
                                        <td class="px-4 py-3"><span class="px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-full text-[10px] font-bold font-sans">Didukung</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-2">Scope (Hak Akses Token)</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <tr><th class="px-4 py-2.5">Scope</th><th class="px-4 py-2.5">Akses Data</th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    @foreach([
                                        ['students.read',   'Membaca data publik siswa RPL/PPLG (nama, NIS, kelas, gender)'],
                                        ['students.private','Membaca data privat/sensitif siswa (NIK, kontak, alamat, ortu)'],
                                        ['teachers.read',   'Membaca data guru produktif RPL & tendik'],
                                        ['classes.read',    'Membaca data rombel dan konsentrasi keahlian RPL'],
                                    ] as [$sc, $desc])
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-mono font-bold text-indigo-700">{{ $sc }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $desc }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== SISWA ===== -->
            <div x-show="activeSection === 'students'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">👩‍🎓 Data Siswa WargaRPL</h3>
                        <p class="text-sm text-slate-500 mt-1">Endpoint untuk mengambil daftar siswa-siswi jurusan RPL & PPLG.</p>
                    </div>

                    @include('admin.partials.api-endpoint', [
                        'method'      => 'GET',
                        'path'        => '/api/v1/students',
                        'scope'       => 'students.read',
                        'description' => 'Mengambil daftar siswa RPL/PPLG terdaftar dengan pagination & filter.',
                        'params'      => [
                            ['name'=>'grade',         'type'=>'integer', 'desc'=>'Tingkat kelas: 10, 11, atau 12'],
                            ['name'=>'major',         'type'=>'string',  'desc'=>'Kode konsentrasi keahlian: PPLG atau RPL'],
                            ['name'=>'class',         'type'=>'string',  'desc'=>'Nama rombel, contoh: X-PPLG-1, XI-RPL-2'],
                            ['name'=>'search',        'type'=>'string',  'desc'=>'Pencarian nama atau NIS siswa'],
                            ['name'=>'per_page',      'type'=>'integer', 'desc'=>'Jumlah data per halaman (default 25, maks 100)'],
                            ['name'=>'updated_since', 'type'=>'string',  'desc'=>'Filter perubahan sejak ISO 8601 UTC'],
                        ],
                        'response'    => '{
  "success": true,
  "data": [
    {
      "uuid": "9e1c2b3a-...",
      "nis": "222310001",
      "nisn": "0061234567",
      "full_name": "Ahmad Dani",
      "gender": "L",
      "status": "Aktif",
      "class": {
        "name": "X PPLG 1",
        "grade_level": 10,
        "major": "PPLG"
      }
    }
  ],
  "meta": { "total": 216, "page": 1, "per_page": 25, "last_page": 9 }
}'
                    ])

                    @include('admin.partials.api-endpoint', [
                        'method'      => 'GET',
                        'path'        => '/api/v1/students/{uuid}',
                        'scope'       => 'students.read',
                        'description' => 'Mengambil detail profil satu siswa berdasarkan UUID.',
                        'params'      => [
                            ['name'=>'uuid', 'type'=>'string (path)', 'desc'=>'UUID unik siswa']
                        ],
                        'response'    => '{
  "success": true,
  "data": {
    "uuid": "9e1c2b3a-...",
    "nis": "222310001",
    "full_name": "Ahmad Dani",
    "class": "X PPLG 1",
    "major": "PPLG"
  }
}'
                    ])
                </div>
            </div>

            <!-- ===== GURU & TENDIK ===== -->
            <div x-show="activeSection === 'employees'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">👨‍🏫 Data Guru & Tendik RPL</h3>
                        <p class="text-sm text-slate-500 mt-1">Endpoint untuk membaca data pendidik produktif RPL.</p>
                    </div>

                    @include('admin.partials.api-endpoint', [
                        'method'      => 'GET',
                        'path'        => '/api/v1/employees',
                        'scope'       => 'teachers.read',
                        'description' => 'Mengambil daftar pendidik produktif RPL & tendik.',
                        'params'      => [
                            ['name'=>'search',   'type'=>'string', 'desc'=>'Cari nama atau NIP'],
                            ['name'=>'per_page', 'type'=>'integer', 'desc'=>'Data per halaman']
                        ],
                        'response'    => '{
  "success": true,
  "data": [
    {
      "uuid": "a1b2c3d4-...",
      "nip": "198001012005011001",
      "full_name": "Guru Produktif RPL, S.Kom.",
      "ptk_type": "Guru Produktif"
    }
  ]
}'
                    ])
                </div>
            </div>

            <!-- ===== KELAS & JURUSAN ===== -->
            <div x-show="activeSection === 'classes'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">🏫 Rombongan Belajar RPL & PPLG</h3>
                        <p class="text-sm text-slate-500 mt-1">Data rombel aktif untuk jurusan RPL dan PPLG.</p>
                    </div>

                    @include('admin.partials.api-endpoint', [
                        'method'      => 'GET',
                        'path'        => '/api/v1/classes',
                        'scope'       => 'classes.read',
                        'description' => 'Daftar semua rombel RPL/PPLG tahun ajaran aktif.',
                        'params'      => [
                            ['name'=>'grade', 'type'=>'integer', 'desc'=>'Filter tingkat: 10, 11, 12']
                        ],
                        'response'    => '{
  "success": true,
  "data": [
    { "id": 1, "name": "X PPLG 1", "grade_level": 10, "major": "PPLG" },
    { "id": 2, "name": "XI PPLG 1", "grade_level": 11, "major": "PPLG" },
    { "id": 3, "name": "XII RPL 1", "grade_level": 12, "major": "RPL" }
  ]
}'
                    ])
                </div>
            </div>

            <!-- ===== STATISTIK ===== -->
            <div x-show="activeSection === 'stats'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">📊 Statistik WargaRPL</h3>
                        <p class="text-sm text-slate-500 mt-1">Ringkasan total siswa dan sebaran per angkatan.</p>
                    </div>

                    @include('admin.partials.api-endpoint', [
                        'method'      => 'GET',
                        'path'        => '/api/v1/statistics/students',
                        'scope'       => 'None (Token Bebas Scope)',
                        'description' => 'Statistik total siswa RPL, gender, dan tingkat kelas.',
                        'params'      => [],
                        'response'    => '{
  "success": true,
  "data": {
    "total": 216,
    "male": 140,
    "female": 76,
    "by_grade": { "10": 72, "11": 72, "12": 72 }
  }
}'
                    ])
                </div>
            </div>

            <!-- ===== ERROR CODES ===== -->
            <div x-show="activeSection === 'errors'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-lg font-bold text-slate-900">⚠️ Kode Respon HTTP</h3>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <tr><th class="px-4 py-2.5">HTTP Code</th><th class="px-4 py-2.5">Arti</th><th class="px-4 py-2.5">Penyebab & Solusi</th></tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                @foreach([
                                    ['200','OK','Permintaan berhasil diproses.', 'bg-emerald-50 text-emerald-700'],
                                    ['400','Bad Request','Parameter tidak valid (misal: format tanggal).', 'bg-amber-50 text-amber-700'],
                                    ['401','Unauthorized','Token API tidak ada atau tidak valid.', 'bg-red-50 text-red-700'],
                                    ['403','Forbidden','Token tidak memiliki scope yang dibutuhkan.', 'bg-orange-50 text-orange-700'],
                                    ['404','Not Found','Data siswa atau rombel tidak ditemukan.', 'bg-slate-50 text-slate-700'],
                                    ['429','Rate Limit Exceeded','Melebihi batas request per menit.', 'bg-purple-50 text-purple-700'],
                                    ['500','Server Error','Kesalahan internal server. Hubungi admin WargaRPL.', 'bg-red-50 text-red-700'],
                                ] as [$code, $status, $desc, $color])
                                <tr>
                                    <td class="px-4 py-3 font-mono font-bold"><span class="px-2 py-0.5 rounded-full {{ $color }}">{{ $code }}</span></td>
                                    <td class="px-4 py-3 font-bold text-slate-800">{{ $status }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $desc }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ===== CONTOH KODE ===== -->
            <div x-show="activeSection === 'examples'" x-cloak>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-900">💻 Contoh Kode Integrasi Token</h3>

                    <!-- JavaScript / Fetch -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-slate-800">JavaScript / Node.js (Fetch API)</h4>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('code-js').innerText)"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition">Salin Kode</button>
                        </div>
                        <pre id="code-js" class="bg-slate-900 text-emerald-300 rounded-xl p-4 font-mono text-xs overflow-x-auto leading-relaxed">const API_TOKEN = 'wgr_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

const response = await fetch('{{ url('/api/v1/students?grade=10') }}', {
  method: 'GET',
  headers: {
    'Authorization': `Bearer ${API_TOKEN}`,
    'Accept': 'application/json',
  }
});

const { success, data, meta } = await response.json();

if (success) {
  console.log(`Total: ${meta.total} Siswa WargaRPL`);
  data.forEach(siswa => {
    console.log(`${siswa.nis} - ${siswa.full_name} (${siswa.class_name})`);
  });
}</pre>
                    </div>

                    <!-- cURL CLI -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-slate-800">cURL (Terminal / Command Line)</h4>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('code-curl').innerText)"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition">Salin Kode</button>
                        </div>
                        <pre id="code-curl" class="bg-slate-900 text-cyan-300 rounded-xl p-4 font-mono text-xs overflow-x-auto leading-relaxed">curl -X GET "{{ url('/api/v1/students?grade=10') }}" \
  -H "Authorization: Bearer wgr_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"</pre>
                    </div>

                    <!-- PHP / cURL -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-bold text-slate-800">PHP (cURL)</h4>
                            <button onclick="navigator.clipboard.writeText(document.getElementById('code-php').innerText)"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition">Salin Kode</button>
                        </div>
                        <pre id="code-php" class="bg-slate-900 text-blue-300 rounded-xl p-4 font-mono text-xs overflow-x-auto leading-relaxed">&lt;?php

$token = 'wgr_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => '{{ url('/api/v1/students') }}',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    "Authorization: Bearer {$token}",
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
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition">Salin Kode</button>
                        </div>
                        <pre id="code-python" class="bg-slate-900 text-yellow-300 rounded-xl p-4 font-mono text-xs overflow-x-auto leading-relaxed">import requests

token = 'wgr_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
headers = {
    'Authorization': f'Bearer {token}',
    'Accept': 'application/json',
}

r = requests.get('{{ url('/api/v1/students') }}', headers=headers, params={'grade': 10})
result = r.json()

if result.get('success'):
    for siswa in result['data']:
        print(f"{siswa['nis']} - {siswa['full_name']}")</pre>
                    </div>

                    <!-- CORS Note -->
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-xs text-indigo-900 leading-relaxed">
                        <strong>🌐 Catatan Keamanan:</strong><br>
                        Panggil API WargaRPL dari <strong>backend server</strong> aplikasi Anda (server-side). Jangan expose API token ke client-side frontend publik tanpa proxy/backend.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
