<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\ApiClient;
use App\Models\ApiLog;
use App\Models\Webhook;

class ApiClientController extends Controller
{
    public function index()
    {
        $clients = ApiClient::withCount('apiLogs')->get();
        
        // API Logs for chart/dashboard
        $apiLogs = ApiLog::with('apiClient')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.integration.index', compact('clients', 'apiLogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'scopes' => 'required|array',
            'rate_limit' => 'required|integer|min:1',
            'webhook_url' => 'nullable|url|max:255',
        ], [
            'name.required' => 'Nama aplikasi wajib diisi.',
            'scopes.required' => 'Izin akses (scopes) wajib dipilih.',
            'rate_limit.required' => 'Batas rate-limit wajib ditentukan.',
            'webhook_url.url' => 'Format URL Webhook tidak valid.',
        ]);

        $clientId = strtoupper(str_replace(' ', '_', $request->input('name'))) . '_' . Str::upper(Str::random(4));
        $plainSecret = Str::random(32);

        $client = new ApiClient();
        $client->name = $request->input('name');
        $client->client_id = $clientId;
        $client->secret_token = Hash::make($plainSecret);
        $client->scopes = $request->input('scopes');
        $client->rate_limit = $request->input('rate_limit');
        $client->status = 'active';
        $client->save();

        if ($request->filled('webhook_url')) {
            $webhook = new Webhook();
            $webhook->api_client_id = $client->id;
            $webhook->url = $request->input('webhook_url');
            $webhook->events = ['student.updated', 'student.created'];
            $webhook->status = 'active';
            $webhook->save();
        }

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'Generate API Token',
            'description' => "Membuat API Client baru: {$client->name} (Client ID: {$clientId})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.clients.index')->with([
            'success' => 'Kredensial API berhasil dibuat.',
            'new_client_name' => $client->name,
            'new_client_id' => $clientId,
            'new_client_secret' => $plainSecret
        ]);
    }

    public function toggleStatus($id)
    {
        $client = ApiClient::findOrFail($id);
        $client->status = $client->status === 'active' ? 'inactive' : 'active';
        $client->save();

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'event' => $client->status === 'active' ? 'Activate API Client' : 'Revoke API Client',
            'description' => "Mengubah status API Client {$client->name} menjadi {$client->status}.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Status API Client {$client->name} berhasil diperbarui.");
    }
}
