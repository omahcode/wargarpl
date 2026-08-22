<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Major;
use App\Models\Employee;

class SchoolClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with('major', 'homeroomTeacher')->withCount('members')->get();
        $majors = Major::all();
        $employees = Employee::where('status', 'Active')->get();
        return view('admin.classes.index', compact('classes', 'majors', 'employees'));
    }

    public function show($id)
    {
        $class = SchoolClass::with(['major', 'homeroomTeacher', 'schoolYear'])
            ->findOrFail($id);

        $members = $class->members()
            ->join('students', 'class_members.student_id', '=', 'students.id')
            ->whereNull('students.deleted_at')
            ->select('class_members.*')
            ->with('student')
            ->orderBy('students.full_name', 'asc')
            ->get();

        $majors = Major::all();
        $employees = Employee::where('status', 'Active')->get();

        return view('admin.classes.show', compact('class', 'members', 'majors', 'employees'));
    }

    public function store(Request $request)
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        if (!$activeYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif. Silakan buat atau aktifkan tahun ajaran terlebih dahulu.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'major_id' => 'required|exists:majors,id',
            'grade_level' => 'required|integer|in:10,11,12',
            'homeroom_teacher_id' => 'nullable|exists:employees,id',
        ], [
            'name.required' => 'Nama rombel/kelas wajib diisi.',
            'major_id.required' => 'Pilihan jurusan wajib diisi.',
            'grade_level.required' => 'Tingkat kelas wajib diisi.',
        ]);

        $validated['school_year_id'] = $activeYear->id;
        $validated['status'] = true;

        \DB::beginTransaction();
        try {
            $class = SchoolClass::create($validated);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Create Class',
                'description' => "Menambahkan rombel/kelas baru: {$class->name} (Tingkat: {$class->grade_level})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage());
        }

        return back()->with('success', 'Rombel/Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'major_id' => 'required|exists:majors,id',
            'grade_level' => 'required|integer|in:10,11,12',
            'homeroom_teacher_id' => 'nullable|exists:employees,id',
        ], [
            'name.required' => 'Nama rombel/kelas wajib diisi.',
            'major_id.required' => 'Pilihan jurusan wajib diisi.',
            'grade_level.required' => 'Tingkat kelas wajib diisi.',
        ]);

        \DB::beginTransaction();
        try {
            $class->update($validated);

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Update Class',
                'description' => "Memperbarui rombel/kelas: {$class->name}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal memperbarui kelas: ' . $e->getMessage());
        }

        return back()->with('success', 'Rombel/Kelas berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);

        \DB::beginTransaction();
        try {
            $className = $class->name;
            $class->delete(); // Cascades in DB to class_members

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Delete Class',
                'description' => "Menghapus rombel/kelas: {$className}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }

        return back()->with('success', 'Rombel/Kelas berhasil dihapus.');
    }

    public function deleteAll(Request $request)
    {
        \DB::beginTransaction();
        try {
            SchoolClass::query()->delete(); // DML safe delete

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'Clear All Classes',
                'description' => "Mengosongkan seluruh data rombel/kelas dari database.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Gagal mengosongkan data kelas: ' . $e->getMessage());
        }

        return back()->with('success', 'Seluruh data rombel/kelas berhasil dikosongkan.');
    }
}
