<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\EmployeeChangeLog;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(404, 'Data guru/pegawai tidak ditemukan.');
        }

        // Get homeroom classes
        $homeroomClasses = $employee->homeroomClasses()->get();

        // Get change logs
        $changeLogs = EmployeeChangeLog::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.dashboard', compact('employee', 'homeroomClasses', 'changeLogs'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(404, 'Data guru/pegawai tidak ditemukan.');
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
        ]);

        $originalData = $employee->only(array_keys($validated));
        $changedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($validated as $key => $value) {
                $oldValue = $originalData[$key] ?? null;
                if (trim((string)$oldValue) !== trim((string)$value)) {
                    EmployeeChangeLog::create([
                        'employee_id' => $employee->id,
                        'modified_by_id' => $user->id,
                        'field_name' => $key,
                        'old_value' => $oldValue,
                        'new_value' => $value,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    $employee->$key = $value;
                    $changedCount++;
                }
            }

            if ($changedCount > 0) {
                $employee->save();

                // Audit Log
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'event' => 'Edit Employee Profile',
                    'description' => "Pegawai/Guru {$employee->full_name} memperbarui {$changedCount} data kontak.",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan perubahan: ' . $e->getMessage());
        }

        return back()->with('success', 'Profil kepegawaian Anda berhasil diperbarui.');
    }
}
