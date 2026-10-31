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
        $clients = ApiClient::withCount('apiLogs')
            ->orderBy('created_at', 'desc')
            ->get();
        
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
            'name.required' => 'Nama aplikasi/token wajib diisi.',
            'scopes.required' => 'Izin akses (scopes) wajib dipilih.',
            'rate_limit.required' => 'Batas rate-limit wajib ditentukan.',
            'webhook_url.url' => 'Format URL Webhook tidak valid.',
        ]);

        // Generate Token WargaRPL
        $plainToken = 'wgr_live_' . Str::random(40);
        $clientId = 'wgr_' . Str::lower(Str::random(10));

        $client = new ApiClient();
        $client->name = $request->input('name');
        $client->token = $plainToken;
        $client->client_id = $clientId;
        $client->secret_token = Hash::make($plainToken);
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
            'description' => "Membuat API Token baru: {$client->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.clients.index')->with([
            'success' => 'API Token berhasil dibuat.',
            'new_client_name' => $client->name,
            'new_token' => $plainToken,
            'new_client_id' => $clientId,
        ]);
    }

    public function regenerateToken($id)
    {
        $client = ApiClient::findOrFail($id);
        $newToken = 'wgr_live_' . Str::random(40);

        $client->token = $newToken;
        $client->secret_token = Hash::make($newToken);
        $client->save();

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'Regenerate API Token',
            'description' => "Memperbarui token untuk API Client: {$client->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route('admin.clients.index')->with([
            'success' => 'API Token baru berhasil dibuat ulang.',
            'new_client_name' => $client->name,
            'new_token' => $newToken,
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
            'event' => $client->status === 'active' ? 'Activate API Token' : 'Revoke API Token',
            'description' => "Mengubah status API Token {$client->name} menjadi {$client->status}.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "Status API Token {$client->name} berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $client = ApiClient::findOrFail($id);
        $name = $client->name;
        $client->delete();

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'event' => 'Delete API Token',
            'description' => "Menghapus API Token: {$name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "API Token {$name} berhasil dihapus.");
    }
}
