@extends('layouts.app')

@section('title', 'API Token Management - WargaRPL')

@section('content')
<div class="space-y-8" x-data="{ openCreateModal: false, copied: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-2xl font-bold text-slate-900">Manajemen API Token</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">WargaRPL API</span>
            </div>
            <p class="text-sm text-slate-500 font-medium mt-1">Generate token akses untuk integrasi aplikasi eksternal, website jurusan, atau sistem sekolah.</p>
        </div>
        <button @click="openCreateModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98] shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Generate API Token Baru
        </button>
    </div>

    <!-- Alert: Token Generated Display -->
    @if (session('new_token'))
        <div class="bg-gradient-to-br from-indigo-50 to-slate-50 border-2 border-indigo-300 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <h3 class="text-base font-bold text-indigo-950 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-sm shadow-sm">✓</span>
                    TOKEN API BERHASIL DIBUAT
                </h3>
                <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold border border-amber-200">
                    ⚠️ Simpan Sekarang
                </span>
            </div>
            <p class="text-xs text-slate-600 font-medium leading-relaxed">
                Salin token di bawah ini sekarang. Token hanya ditampilkan secara utuh satu kali saat generate demi menjaga keamanan data warga RPL.
            </p>
            <div class="p-4 bg-white border border-indigo-200 rounded-xl space-y-3 font-mono text-xs text-slate-900">
                <div class="flex items-center justify-between text-xs text-slate-500 font-sans pb-2 border-b border-slate-100">
                    <span>Aplikasi: <strong class="text-slate-900">{{ session('new_client_name') }}</strong></span>
                    <span>Tipe: <strong>Bearer Token</strong></span>
                </div>
                <div class="flex items-center justify-between gap-3 bg-slate-950 text-emerald-400 p-3 rounded-lg overflow-x-auto">
                    <span class="select-all font-mono text-xs tracking-wider font-semibold" id="tokenDisplay">{{ session('new_token') }}</span>
                    <button 
                        type="button" 
                        @click="navigator.clipboard.writeText('{{ session('new_token') }}'); copied = true; setTimeout(() => copied = false, 2500)"
                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-sans font-bold rounded-md transition flex items-center gap-1.5 shrink-0"
                    >
                        <template x-if="!copied">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                Salin Token
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="text-emerald-200 flex items-center gap-1 font-bold">✓ Tersalin!</span>
                        </template>
                    </button>
                </div>
                <div class="text-[11px] text-slate-500 font-sans pt-1">
                    <strong>Cara Penggunaan:</strong> Kirim header HTTP <code class="px-1.5 py-0.5 bg-slate-100 text-slate-800 rounded font-mono font-semibold">Authorization: Bearer {{ session('new_token') }}</code> saat memanggil endpoint API WargaRPL.
                </div>
            </div>
        </div>
    @endif

    <!-- API Clients / Tokens List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar API Token Terdaftar</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Token yang dapat digunakan pihak ketiga untuk mengakses data WargaRPL.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                Total: {{ $clients->count() }} Token
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">Nama Token / Aplikasi</th>
                        <th class="p-4">Token (Snippet)</th>
                        <th class="p-4">Hak Akses (Scopes)</th>
                        <th class="p-4">Rate Limit</th>
                        <th class="p-4">Total Request</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        <tr class="text-slate-600 font-medium hover:bg-slate-50/50 transition">
                            <td class="p-4">
                                <div class="font-bold text-slate-950">{{ $client->name }}</div>
                                <div class="text-[11px] text-slate-400">Dibuat {{ $client->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="p-4 font-mono text-xs">
                                @if($client->token)
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200 text-slate-700 select-all">
                                            {{ substr($client->token, 0, 12) }}...{{ substr($client->token, -4) }}
                                        </span>
                                        <button 
                                            type="button" 
                                            @click="navigator.clipboard.writeText('{{ $client->token }}'); alert('Token disalin!')" 
                                            title="Salin Token Lengkap" 
                                            class="text-slate-400 hover:text-slate-700"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="font-mono text-xs text-slate-400">{{ $client->client_id ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($client->scopes ?? [] as $sc)
                                        <span class="text-[10px] font-semibold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-100">{{ $sc }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4 font-mono text-xs">{{ $client->rate_limit }} req/m</td>
                            <td class="p-4 font-bold text-slate-900">{{ number_format($client->api_logs_count) }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $client->status === 'active' ? 'text-green-700 bg-green-50 border border-green-200' : 'text-red-700 bg-red-50 border border-red-200' }}">
                                    {{ $client->status === 'active' ? 'Aktif' : 'Revoked' }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Toggle Status -->
                                    <form method="POST" action="{{ route('admin.clients.toggle', $client->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg border text-xs font-bold transition {{ $client->status === 'active' ? 'border-amber-200 hover:bg-amber-50 text-amber-700' : 'border-green-200 hover:bg-green-50 text-green-700' }}">
                                            {{ $client->status === 'active' ? 'Revoke' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <!-- Regenerate -->
                                    <form method="POST" action="{{ route('admin.clients.regenerate', $client->id) }}" onsubmit="return confirm('Generate ulang token? Token lama tidak akan dapat digunakan lagi.')" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-50 text-xs font-bold text-slate-700 transition" title="Buat Token Baru">
                                            Reset
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.clients.destroy', $client->id) }}" onsubmit="return confirm('Hapus API Token ini secara permanen?')" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 rounded-lg border border-red-200 hover:bg-red-50 text-xs font-bold text-red-600 transition" title="Hapus">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 font-medium">Belum ada API token yang dibuat. Klik tombol di atas untuk generate token pertama.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- API Request Logs -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900">Log Permintaan API (Realtime)</h3>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Catatan request masuk dari aplikasi eksternal yang menggunakan token.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Token Client</th>
                        <th class="p-4">Endpoint</th>
                        <th class="p-4">Method</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Duration</th>
                        <th class="p-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($apiLogs as $log)
                        <tr class="text-slate-600 font-medium">
                            <td class="p-4 text-xs font-mono">{{ Carbon\Carbon::parse($log->created_at)->translatedFormat('d-m-Y H:i:s') }}</td>
                            <td class="p-4 text-slate-950 font-bold">{{ $log->apiClient->name ?? '-' }}</td>
                            <td class="p-4 font-mono text-xs text-slate-800">/{{ $log->endpoint }}</td>
                            <td class="p-4"><span class="font-bold text-xs uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">{{ $log->method }}</span></td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded font-mono text-xs font-semibold {{ $log->response_code >= 200 && $log->response_code < 300 ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50' }}">
                                    {{ $log->response_code }}
                                </span>
                            </td>
                            <td class="p-4 font-mono text-xs">{{ $log->duration_ms }} ms</td>
                            <td class="p-4 font-mono text-xs">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 font-medium">Belum ada aktivitas request API.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($apiLogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $apiLogs->links() }}
            </div>
        @endif
    </div>

    <!-- Registration / Generate Modal -->
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60" 
         x-show="openCreateModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        
        <div class="w-full max-w-lg bg-white rounded-2xl border border-slate-200 p-6 shadow-xl relative" @click.away="openCreateModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Generate Token API Baru</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Buat token akses langsung untuk aplikasi luar atau website.</p>
                </div>
                <button type="button" @click="openCreateModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Token / Aplikasi</label>
                    <input type="text" id="name" name="name" required placeholder="Contoh: Web Portofolio RPL, Absensi Siswa, Bot Discord" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none text-sm font-medium transition" />
                </div>

                <div>
                    <label for="rate_limit" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Rate Limit (Request per menit)</label>
                    <input type="number" id="rate_limit" name="rate_limit" value="60" min="1" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none text-sm font-medium transition" />
                    <p class="text-[11px] text-slate-400 mt-1">Batas maksimum request per menit dari satu token.</p>
                </div>

                <div>
                    <label for="webhook_url" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">URL Webhook (Opsional)</label>
                    <input type="url" id="webhook_url" name="webhook_url" placeholder="https://external-app.id/webhook" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 outline-none text-sm font-medium transition" />
                </div>

                <div>
                    <span class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Hak Akses Data (Scopes)</span>
                    <div class="grid grid-cols-2 gap-2 text-sm text-slate-700 font-semibold mt-1">
                        <label class="flex items-center p-2 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                            <input type="checkbox" name="scopes[]" value="students.read" checked class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 mr-2 cursor-pointer">
                            <span class="text-xs">students.read (Publik)</span>
                        </label>
                        <label class="flex items-center p-2 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                            <input type="checkbox" name="scopes[]" value="students.private" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 mr-2 cursor-pointer">
                            <span class="text-xs">students.private (Sensitif)</span>
                        </label>
                        <label class="flex items-center p-2 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                            <input type="checkbox" name="scopes[]" value="classes.read" checked class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 mr-2 cursor-pointer">
                            <span class="text-xs">classes.read (Rombel)</span>
                        </label>
                        <label class="flex items-center p-2 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                            <input type="checkbox" name="scopes[]" value="teachers.read" checked class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 mr-2 cursor-pointer">
                            <span class="text-xs">teachers.read (Guru)</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Generate Token Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
