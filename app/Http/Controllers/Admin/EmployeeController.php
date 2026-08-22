<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\User;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $typeFilter = $request->input('ptk_type');

        $query = Employee::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($typeFilter) {
            $query->where('ptk_type', $typeFilter);
        }

        $employees = $query->paginate(15)->withQueryString();

        return view('admin.employees.index', compact('employees', 'search', 'typeFilter'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|numeric|unique:employees,nip',
            'nuptk' => 'nullable|numeric|unique:employees,nuptk',
            'nik' => 'nullable|numeric|unique:employees,nik',
            'full_name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'ptk_type' => 'required|string',
            'employment_type' => 'required|string',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'religion' => 'required|string',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nik.numeric' => 'NIK harus berupa angka.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis Kelamin wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
        ]);

        DB::beginTransaction();
        try {
            $employee = Employee::create([
                'uuid' => Str::uuid()->toString(),
                'nip' => $request->input('nip'),
                'nuptk' => $request->input('nuptk'),
                'nik' => $request->input('nik'),
                'full_name' => $request->input('full_name'),
                'gender' => $request->input('gender'),
                'ptk_type' => $request->input('ptk_type'),
                'employment_type' => $request->input('employment_type'),
                'birth_place' => $request->input('birth_place'),
                'birth_date' => $request->input('birth_date'),
                'religion' => $request->input('religion'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'whatsapp' => $request->input('phone'),
                'address' => $request->input('address') ?? '-',
                'status' => 'Aktif',
            ]);

            // User login account (Password default: Tanggal Lahir DDMMYYYY)
            $birthDate = $employee->birth_date;
            $defaultPassword = $birthDate ? date('dmY', strtotime($birthDate)) : 'Password123!';

            User::create([
                'name' => $employee->full_name,
                'email' => $employee->nip . '@smkn1-cirebon.sch.id',
                'username' => $employee->nip,
                'role' => 'employee',
                'employee_id' => $employee->id,
                'password' => Hash::make($defaultPassword),
            ]);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Create Employee',
                'description' => "Admin mendaftarkan guru/tendik baru: {$employee->full_name} (NIP: {$employee->nip})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mendaftarkan guru/tendik: ' . $e->getMessage());
        }

        return redirect()->route('admin.employees.index')->with('success', 'Guru/Tendik berhasil didaftarkan.');
    }

    public function show($uuid)
    {
        $employee = Employee::where('uuid', $uuid)->firstOrFail();
        
        $homeroomClasses = $employee->homeroomClasses()->get();

        $changeLogs = \App\Models\EmployeeChangeLog::where('employee_id', $employee->id)
            ->with('modifiedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.employees.show', compact('employee', 'homeroomClasses', 'changeLogs'));
    }

    public function edit($uuid)
    {
        $employee = Employee::where('uuid', $uuid)->firstOrFail();
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $uuid)
    {
        $employee = Employee::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'nip' => 'required|numeric|unique:employees,nip,' . $employee->id,
            'nuptk' => 'nullable|numeric|unique:employees,nuptk,' . $employee->id,
            'nik' => 'nullable|numeric|unique:employees,nik,' . $employee->id,
            'full_name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'ptk_type' => 'required|string',
            'employment_type' => 'required|string',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'religion' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $employee->update([
                'nip' => $request->input('nip'),
                'nuptk' => $request->input('nuptk'),
                'nik' => $request->input('nik'),
                'full_name' => $request->input('full_name'),
                'gender' => $request->input('gender'),
                'ptk_type' => $request->input('ptk_type'),
                'employment_type' => $request->input('employment_type'),
                'birth_place' => $request->input('birth_place'),
                'birth_date' => $request->input('birth_date'),
                'religion' => $request->input('religion'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'address' => $request->input('address') ?? '-',
            ]);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Update Employee',
                'description' => "Admin memperbarui data guru/tendik: {$employee->full_name} (NIP: {$employee->nip})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data guru/tendik: ' . $e->getMessage());
        }

        return redirect()->route('admin.employees.show', $employee->uuid)->with('success', 'Data guru/tendik berhasil diperbarui.');
    }

    public function destroy(Request $request, $uuid)
    {
        $employee = Employee::where('uuid', $uuid)->firstOrFail();

        DB::beginTransaction();
        try {
            // Delete User Account
            User::where('employee_id', $employee->id)->delete();

            // Soft Delete Employee
            $employee->delete();

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Delete Employee',
                'description' => "Admin menghapus guru/tendik: {$employee->full_name} (NIP: {$employee->nip})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data guru/tendik: ' . $e->getMessage());
        }

        return redirect()->route('admin.employees.index')->with('success', 'Data guru/tendik berhasil dihapus.');
    }

    /**
     * Reset employee's user account password to default (birthdate formatted as dmY)
     */
    public function resetPassword(Request $request, $uuid)
    {
        $employee = Employee::where('uuid', $uuid)->firstOrFail();
        $user = $employee->user;

        if (!$user) {
            return back()->with('error', 'Akun user untuk guru/tendik ini tidak ditemukan.');
        }

        $birthDate = $employee->birth_date;
        if (!$birthDate) {
            return back()->with('error', 'Guru/tendik tidak memiliki tanggal lahir terdaftar. Tidak dapat menghasilkan password default.');
        }

        $defaultPassword = date('dmY', strtotime($birthDate));
        $user->password = Hash::make($defaultPassword);
        $user->save();

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'event' => 'Reset Employee Password',
            'description' => "Admin mereset password guru/tendik {$employee->full_name} (NIP: {$employee->nip}) kembali ke bawaan.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Password untuk guru/tendik {$employee->full_name} berhasil direset ke password default bawaan ({$defaultPassword}).");
    }

    public function deleteAll(Request $request)
    {
        DB::beginTransaction();
        try {
            // Delete Employee user accounts
            User::where('role', 'employee')->delete();
            
            // Delete dependent child tables (using DML delete() instead of truncate() to avoid implicit commits)
            DB::table('employee_change_logs')->delete();
            
            // Force delete employees
            Employee::query()->forceDelete();

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Truncate Employees Table',
                'description' => 'Admin membersihkan/menghapus secara keseluruhan seluruh data guru/tendik dan akun login terkait.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data guru/tendik keseluruhan: ' . $e->getMessage());
        }

        return redirect()->route('admin.employees.index')->with('success', 'Seluruh data guru/tendik berhasil dihapus secara permanen.');
    }
}
