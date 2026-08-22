<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $eventFilter = $request->input('event');

        $query = AuditLog::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($eventFilter) {
            $query->where('event', $eventFilter);
        }

        $auditLogs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        // Get unique events for filter dropdown
        $events = AuditLog::select('event')->distinct()->pluck('event')->toArray();

        return view('admin.audit-logs', compact('auditLogs', 'search', 'eventFilter', 'events'));
    }
}
