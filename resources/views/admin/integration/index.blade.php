@extends('layouts.app')

@section('title', 'API Integration Management')

@section('content')
<div class="space-y-8" x-data="{ openCreateModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Integrasi REST API</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Kelola token akses client, batas rate-limit, dan log request pihak ketiga.</p>
        </div>
        <button @click="openCreateModal = true" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition active:scale-[0.98]">
            Daftarkan Aplikasi Baru
        </button>
    </div>

    <!-- Alert: Token Generated Display -->
    @if (session('new_client_secret'))
        <div class="bg-indigo-50 border-2 border-indigo-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-indigo-900 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                TOKEN AKSES API DIHASILKAN
            </h3>
            <p class="text-xs text-indigo-700 mt-2">
                Simpan Client ID dan Secret Token ini sekarang. Anda tidak akan dapat melihat Secret Token ini kembali demi keamanan.
            </p>
            <div class="mt-4 p-4 bg-white border border-indigo-100 rounded-xl space-y-3 font-mono text-xs text-slate-800 select-all">
                <p><strong>Aplikasi:</strong> {{ session('new_client_name') }}</p>
                <p><strong>X-Client-ID:</strong> {{ session('new_client_id') }}</p>
                <p><strong>X-Client-Secret:</strong> {{ session('new_client_secret') }}</p>
            </div>
        </div>
    @endif

    <!-- API Clients List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900">Aplikasi Klien Terdaftar</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">Nama Aplikasi</th>
                        <th class="p-4">Client ID</th>
                        <th class="p-4">Scopes</th>
                        <th class="p-4">Rate Limit</th>
                        <th class="p-4">Total Request</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        <tr class="text-slate-600 font-medium">
                            <td class="p-4 text-slate-950 font-bold">{{ $client->name }}</td>
                            <td class="p-4 font-mono text-xs">{{ $client->client_id }}</td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($client->scopes ?? [] as $sc)
                                        <span class="text-[10px] font-semibold bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200">{{ $sc }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-4">{{ $client->rate_limit }} / m</td>
                            <td class="p-4 font-bold">{{ $client->api_logs_count }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $client->status === 'active' ? 'text-green-700 bg-green-50 border border-green-200' : 'text-red-700 bg-red-50 border border-red-200' }}">
                                    {{ $client->status === 'active' ? 'Aktif' : 'Revoked' }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <form method="POST" action="{{ route('admin.clients.toggle', $client->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded border text-xs font-bold transition {{ $client->status === 'active' ? 'border-red-200 hover:bg-red-50 text-red-600' : 'border-green-200 hover:bg-green-50 text-green-600' }}">
                                        {{ $client->status === 'active' ? 'Revoke' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 font-medium">Belum ada API client yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- API Request Logs -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-base font-bold text-slate-900">API Access logs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Client</th>
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
                            <td class="p-4 text-xs">{{ Carbon\Carbon::parse($log->created_at)->translatedFormat('d-m-Y H:i:s') }}</td>
                            <td class="p-4 text-slate-950 font-bold">{{ $log->apiClient->name ?? '-' }}</td>
                            <td class="p-4 font-mono text-xs text-slate-800">/{{ $log->endpoint }}</td>
                            <td class="p-4"><span class="font-bold text-xs uppercase">{{ $log->method }}</span></td>
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

    <!-- Registration Modal -->
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
            <h3 class="text-lg font-bold text-slate-900 mb-4">Daftarkan Klien Integrasi</h3>
            
            <form method="POST" action="{{ route('admin.clients.store') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Klien / Aplikasi</label>
                    <input type="text" id="name" name="name" required placeholder="Contoh: E-Rapor, Absensi Sekolah" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                </div>

                <div>
                    <label for="rate_limit" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Rate Limit (Request per menit)</label>
                    <input type="number" id="rate_limit" name="rate_limit" value="60" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                </div>

                <div>
                    <label for="webhook_url" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">URL Webhook (Opsional)</label>
                    <input type="url" id="webhook_url" name="webhook_url" placeholder="https://api-erapor.smkn1.sch.id/webhook" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition" />
                </div>

                <div>
                    <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hak Akses Data (Scopes)</span>
                    <div class="grid grid-cols-2 gap-2 text-sm text-slate-600 font-semibold mt-1">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="scopes[]" value="students.read" checked class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900 mr-2 cursor-pointer">
                            students.read
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="scopes[]" value="students.private" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900 mr-2 cursor-pointer">
                            students.private
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="scopes[]" value="teachers.read" checked class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900 mr-2 cursor-pointer">
                            teachers.read
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="scopes[]" value="classes.read" checked class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900 mr-2 cursor-pointer">
                            classes.read
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        Simpan & Dapatkan Kredensial
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
