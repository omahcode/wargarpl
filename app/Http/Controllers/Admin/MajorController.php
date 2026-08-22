<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Major;

class MajorController extends Controller
{
    public function index()
    {
        $majors = Major::withCount('classes')->get();
        return view('admin.majors.index', compact('majors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:majors,code|max:50',
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode kompetensi keahlian wajib diisi.',
            'code.unique' => 'Kode kompetensi keahlian sudah digunakan.',
            'name.required' => 'Nama kompetensi keahlian wajib diisi.',
        ]);

        \DB::beginTransaction();
        try {
            $major = Major::create($validated);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Create Major',
                'description' => "Menambahkan kompetensi keahlian baru: {$major->name} ({$major->code})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal menambahkan jurusan: ' . $e->getMessage());
        }

        return back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $major = Major::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:majors,code,' . $id,
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'Kode kompetensi keahlian wajib diisi.',
            'code.unique' => 'Kode kompetensi keahlian sudah digunakan.',
            'name.required' => 'Nama kompetensi keahlian wajib diisi.',
        ]);

        \DB::beginTransaction();
        try {
            $major->update($validated);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Update Major',
                'description' => "Memperbarui kompetensi keahlian: {$major->name} ({$major->code})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal memperbarui jurusan: ' . $e->getMessage());
        }

        return back()->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $major = Major::findOrFail($id);

        \DB::beginTransaction();
        try {
            $majorName = $major->name;
            $major->delete(); // This cascades and deletes related classes and class members in database

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Delete Major',
                'description' => "Menghapus kompetensi keahlian: {$majorName}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal menghapus jurusan: ' . $e->getMessage());
        }

        return back()->with('success', 'Jurusan berhasil dihapus.');
    }

    public function deleteAll(Request $request)
    {
        \DB::beginTransaction();
        try {
            Major::query()->delete(); // DML safe delete

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Clear All Majors',
                'description' => "Mengosongkan seluruh data kompetensi keahlian (jurusan) dari database.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal mengosongkan data jurusan: ' . $e->getMessage());
        }

        return back()->with('success', 'Seluruh data kompetensi keahlian berhasil dikosongkan.');
    }
}
