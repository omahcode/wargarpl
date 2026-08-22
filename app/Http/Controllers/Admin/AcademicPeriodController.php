<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicPeriodController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYear::with('semesters')->orderBy('name', 'desc')->get();
        return view('admin.periods.index', compact('schoolYears'));
    }

    public function storeSchoolYear(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:school_years,name|max:20',
        ], [
            'name.required' => 'Nama tahun ajaran wajib diisi.',
            'name.unique' => 'Tahun ajaran ini sudah terdaftar.',
        ]);

        DB::beginTransaction();
        try {
            // Create School Year
            $schoolYear = SchoolYear::create([
                'name' => $request->input('name'),
                'is_active' => false,
            ]);

            // Auto-create Semesters (Ganjil & Genap)
            Semester::create([
                'school_year_id' => $schoolYear->id,
                'semester' => 'Ganjil',
                'is_active' => false,
            ]);

            Semester::create([
                'school_year_id' => $schoolYear->id,
                'semester' => 'Genap',
                'is_active' => false,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Create School Year',
                'description' => "Admin membuat tahun ajaran baru: {$schoolYear->name} beserta semester Ganjil & Genap.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', "Tahun ajaran {$schoolYear->name} berhasil dibuat beserta Semesternya.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal membuat tahun ajaran: " . $e->getMessage());
        }
    }

    public function activateSchoolYear(Request $request, $id)
    {
        $schoolYear = SchoolYear::findOrFail($id);

        DB::beginTransaction();
        try {
            // Deactivate all other school years
            SchoolYear::where('id', '!=', $schoolYear->id)->update(['is_active' => false]);
            
            // Activate this school year
            $schoolYear->is_active = true;
            $schoolYear->save();

            // Auto-activate the Ganjil semester of this school year if no semester of this year is active
            $hasActiveSemester = Semester::where('school_year_id', $schoolYear->id)
                ->where('is_active', true)
                ->exists();

            if (!$hasActiveSemester) {
                // Deactivate all semesters first
                Semester::query()->update(['is_active' => false]);
                
                // Activate the Ganjil semester
                $ganjil = Semester::where('school_year_id', $schoolYear->id)
                    ->where('semester', 'Ganjil')
                    ->first();
                if ($ganjil) {
                    $ganjil->is_active = true;
                    $ganjil->save();
                }
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Activate School Year',
                'description' => "Admin mengaktifkan tahun ajaran: {$schoolYear->name}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', "Tahun ajaran {$schoolYear->name} sekarang menjadi aktif.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal mengaktifkan tahun ajaran: " . $e->getMessage());
        }
    }

    public function activateSemester(Request $request, $id)
    {
        $semester = Semester::with('schoolYear')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Activate the parent school year as well
            $schoolYear = $semester->schoolYear;
            SchoolYear::where('id', '!=', $schoolYear->id)->update(['is_active' => false]);
            $schoolYear->is_active = true;
            $schoolYear->save();

            // Deactivate all other semesters in the system
            Semester::where('id', '!=', $semester->id)->update(['is_active' => false]);
            
            // Activate this semester
            $semester->is_active = true;
            $semester->save();

            AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Activate Semester',
                'description' => "Admin mengaktifkan semester: {$semester->semester} pada Tahun Ajaran {$schoolYear->name}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', "Semester {$semester->semester} Tahun Ajaran {$schoolYear->name} sekarang menjadi aktif.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal mengaktifkan semester: " . $e->getMessage());
        }
    }

    public function destroySchoolYear(Request $request, $id)
    {
        $schoolYear = SchoolYear::findOrFail($id);

        if ($schoolYear->is_active) {
            return back()->with('error', 'Tidak bisa menghapus tahun ajaran yang sedang aktif.');
        }

        DB::beginTransaction();
        try {
            // Delete dependent semesters
            Semester::where('school_year_id', $schoolYear->id)->delete();
            
            // Delete school year
            $schoolYear->delete();

            AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Delete School Year',
                'description' => "Admin menghapus tahun ajaran: {$schoolYear->name}.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
            return back()->with('success', "Tahun ajaran {$schoolYear->name} berhasil dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal menghapus tahun ajaran: " . $e->getMessage());
        }
    }
}
