{{--
    Partial: admin/partials/api-endpoint.blade.php
    Props:
      $method  - 'GET'|'POST'|'DELETE'
      $path    - e.g. '/api/v1/students'
      $desc    - Short description
      $params  - array of ['name', 'type', 'desc'] (optional)
      $response- JSON string example (optional)
--}}
@php
    $methodColors = [
        'GET'    => 'bg-emerald-500',
        'POST'   => 'bg-blue-600',
        'PUT'    => 'bg-amber-500',
        'DELETE' => 'bg-red-600',
    ];
    $mc = $methodColors[$method] ?? 'bg-slate-500';
    $uid = 'ep-' . md5($path . $method);
@endphp

<div class="border border-slate-200 rounded-xl overflow-hidden" x-data="{ open: false }">
    <button @click="open = !open"
        class="w-full flex items-center gap-3 px-4 py-3.5 bg-slate-50 hover:bg-slate-100 transition text-left">
        <span class="px-2 py-0.5 rounded text-[10px] font-black text-white {{ $mc }} shrink-0">{{ $method }}</span>
        <code class="text-sm font-mono text-slate-800 font-semibold flex-1">{{ $path }}</code>
        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-slate-400 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" x-collapse class="border-t border-slate-100">
        <div class="p-4 space-y-4">
            <p class="text-sm text-slate-600">{{ $desc }}</p>

            @if(!empty($params))
            <div>
                <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Parameter Query</h5>
                <div class="overflow-x-auto rounded-lg border border-slate-100">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">Tipe</th>
                                <th class="px-3 py-2">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($params as $p)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 font-mono font-bold text-violet-700">{{ $p['name'] }}</td>
                                <td class="px-3 py-2 text-slate-500">{{ $p['type'] }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $p['desc'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if(!empty($response))
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <h5 class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Contoh Response</h5>
                    <button onclick="navigator.clipboard.writeText(this.previousElementSibling.parentElement.nextElementSibling.querySelector('code').innerText)"
                        class="text-[10px] text-slate-400 hover:text-slate-700 font-semibold transition">Salin</button>
                </div>
                <div class="bg-slate-900 rounded-lg p-3 overflow-x-auto">
                    <code class="text-xs font-mono text-emerald-300 whitespace-pre-wrap">{{ $response }}</code>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
