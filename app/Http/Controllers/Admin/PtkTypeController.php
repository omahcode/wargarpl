<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PtkType;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class PtkTypeController extends Controller
{
    public function index()
    {
        $ptkTypes = PtkType::all()->map(function ($type) {
            $type->employees_count = Employee::where('ptk_type', $type->name)->count();
            return $type;
        });

        return view('admin.ptk_types.index', compact('ptkTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:ptk_types,name|max:255',
        ], [
            'name.required' => 'Nama jenis PTK wajib diisi.',
            'name.unique' => 'Nama jenis PTK sudah terdaftar.',
            'name.max' => 'Nama jenis PTK terlalu panjang.',
        ]);

        DB::beginTransaction();
        try {
            $ptkType = PtkType::create($validated);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Create PTK Type',
                'description' => "Admin menambahkan jenis PTK baru: {$ptkType->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan jenis PTK: ' . $e->getMessage());
        }

        return back()->with('success', 'Jenis PTK berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $ptkType = PtkType::findOrFail($id);
        $oldName = $ptkType->name;

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ptk_types,name,' . $id,
        ], [
            'name.required' => 'Nama jenis PTK wajib diisi.',
            'name.unique' => 'Nama jenis PTK sudah terdaftar.',
            'name.max' => 'Nama jenis PTK terlalu panjang.',
        ]);

        DB::beginTransaction();
        try {
            $ptkType->update($validated);

            // Update matching employee ptk_type values
            Employee::where('ptk_type', $oldName)->update([
                'ptk_type' => $validated['name']
            ]);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Update PTK Type',
                'description' => "Admin memperbarui jenis PTK dari '{$oldName}' menjadi '{$validated['name']}'",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui jenis PTK: ' . $e->getMessage());
        }

        return back()->with('success', 'Jenis PTK berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $ptkType = PtkType::findOrFail($id);
        $name = $ptkType->name;

        DB::beginTransaction();
        try {
            $ptkType->delete();

            // Set matching employee ptk_type to null
            Employee::where('ptk_type', $name)->update([
                'ptk_type' => null
            ]);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Delete PTK Type',
                'description' => "Admin menghapus jenis PTK: {$name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus jenis PTK: ' . $e->getMessage());
        }

        return back()->with('success', 'Jenis PTK berhasil dihapus.');
    }
}
