<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\EmployeeChangeLog;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\ClassMember;
use App\Models\Major;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(404, 'Data guru/pegawai tidak ditemukan.');
        }

        // Active period
        $activeYear = SchoolYear::where('is_active', true)->first();
        $activeSemester = $activeYear ? Semester::where('school_year_id', $activeYear->id)->where('is_active', true)->first() : null;

        // Metrik: Total keseluruhan siswa, L/P, per grade
        $totalStudents = 0;
        $maleCount = 0;
        $femaleCount = 0;
        $gradeX = 0;
        $gradeXI = 0;
        $gradeXII = 0;

        if ($activeYear) {
            // Total students
            $totalStudents = ClassMember::where('school_year_id', $activeYear->id)
                ->where('status', 'Active')
                ->count();

            // Gender stats
            $genderStats = DB::table('class_members')
                ->join('students', 'class_members.student_id', '=', 'students.id')
                ->where('class_members.school_year_id', $activeYear->id)
                ->where('class_members.status', 'Active')
                ->whereNull('students.deleted_at')
                ->select('students.gender', DB::raw('count(distinct class_members.student_id) as total'))
                ->groupBy('students.gender')
                ->get()
                ->pluck('total', 'gender')
                ->toArray();
            
            $maleCount = $genderStats['L'] ?? 0;
            $femaleCount = $genderStats['P'] ?? 0;

            // Grade stats
            $gradeStats = DB::table('class_members')
                ->join('classes', 'class_members.class_id', '=', 'classes.id')
                ->where('class_members.school_year_id', $activeYear->id)
                ->where('class_members.status', 'Active')
                ->select('classes.grade_level', DB::raw('count(distinct class_members.student_id) as total'))
                ->groupBy('classes.grade_level')
                ->get()
                ->pluck('total', 'grade_level')
                ->toArray();

            $gradeX = $gradeStats[10] ?? 0;
            $gradeXI = $gradeStats[11] ?? 0;
            $gradeXII = $gradeStats[12] ?? 0;
        } else {
            $totalStudents = Student::count();
            $maleCount = Student::where('gender', 'L')->count();
            $femaleCount = Student::where('gender', 'P')->count();
        }

        // Ringkasan Rombel
        $rombelSummary = collect([
            10 => ['classes' => 0, 'students' => 0],
            11 => ['classes' => 0, 'students' => 0],
            12 => ['classes' => 0, 'students' => 0],
        ]);
        
        if ($activeYear) {
            $classesCountPerGrade = SchoolClass::where('school_year_id', $activeYear->id)
                ->groupBy('grade_level')
                ->select('grade_level', DB::raw('count(*) as total'))
                ->pluck('total', 'grade_level')
                ->toArray();
                
            $studentsCountPerGrade = DB::table('class_members')
                ->join('classes', 'class_members.class_id', '=', 'classes.id')
                ->where('class_members.school_year_id', $activeYear->id)
                ->where('class_members.status', 'Active')
                ->groupBy('classes.grade_level')
                ->select('classes.grade_level', DB::raw('count(distinct class_members.student_id) as total'))
                ->pluck('total', 'grade_level')
                ->toArray();
                
            foreach ([10, 11, 12] as $g) {
                $rombelSummary[$g] = [
                    'classes' => $classesCountPerGrade[$g] ?? 0,
                    'students' => $studentsCountPerGrade[$g] ?? 0,
                ];
            }
        }

        // Recent Audit Logs
        $recentLogs = \App\Models\AuditLog::orderBy('created_at', 'desc')->limit(3)->get();

        return view('employee.dashboard', compact(
            'employee',
            'activeYear', 'activeSemester',
            'totalStudents', 'maleCount', 'femaleCount',
            'gradeX', 'gradeXI', 'gradeXII',
            'rombelSummary', 'recentLogs'
        ));
    }

    public function classes(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(404, 'Data guru/pegawai tidak ditemukan.');
        }

        $activeYear = SchoolYear::where('is_active', true)->first();
        $activeSemester = $activeYear ? Semester::where('school_year_id', $activeYear->id)->where('is_active', true)->first() : null;

        $grade = $request->input('grade');
        $majorId = $request->input('major_id');
        $searchClass = $request->input('search_class');

        $classesQuery = SchoolClass::query();
        if ($activeYear) {
            $classesQuery->where('school_year_id', $activeYear->id);
        }

        // Filters
        if ($grade) {
            $classesQuery->where('grade_level', $grade);
        }
        if ($majorId) {
            $classesQuery->where('major_id', $majorId);
        }
        if ($searchClass) {
            $classesQuery->where('name', 'like', "%{$searchClass}%");
        }

        $classesWithCount = $classesQuery->with(['major', 'homeroomTeacher'])
            ->withCount(['members as student_count' => function ($q) use ($activeYear) {
                if ($activeYear) {
                    $q->where('school_year_id', $activeYear->id);
                }
                $q->where('status', 'Active');
            }])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $majors = Major::all();

        // Selected Class Detail
        $selectedClass = null;
        $classMembers = collect();
        $selectedClassId = $request->input('class_id');
        if ($selectedClassId) {
            $selectedClass = SchoolClass::with(['major', 'homeroomTeacher', 'schoolYear'])
                ->find($selectedClassId);
            if ($selectedClass) {
                $classMembers = $selectedClass->members()
                    ->join('students', 'class_members.student_id', '=', 'students.id')
                    ->whereNull('students.deleted_at')
                    ->where('class_members.status', 'Active')
                    ->select('class_members.*')
                    ->with('student')
                    ->orderBy('students.full_name', 'asc')
                    ->get();
            }
        }

        return view('employee.classes', compact(
            'employee', 'activeYear', 'activeSemester',
            'classesWithCount', 'majors', 'grade', 'majorId', 'searchClass',
            'selectedClass', 'classMembers'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(404, 'Data guru/pegawai tidak ditemukan.');
        }

        // Get change logs
        $changeLogs = EmployeeChangeLog::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $ptkTypes = \App\Models\PtkType::all();

        return view('employee.profile', compact('employee', 'changeLogs', 'ptkTypes'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(404, 'Data guru/pegawai tidak ditemukan.');
        }

        $validated = $request->validate([
            // Data Diri
            'full_name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'nik' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'required|in:L,P',
            'religion' => 'nullable|string|max:50',
            'employment_type' => 'nullable|string|max:50',
            'ptk_type' => 'nullable|string|max:100',
            
            // Riwayat Akademik
            'last_education' => 'nullable|string|max:100',
            'study_program' => 'nullable|string|max:150',
            'university' => 'nullable|string|max:150',
            'graduation_year' => 'nullable|integer',
            'primary_subject' => 'nullable|string|max:150',
            'major_expertise' => 'nullable|string|max:150',

            // Kontak & Alamat
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

                // Sync name & email with User model if updated
                if (isset($validated['full_name'])) {
                    $user->name = $validated['full_name'];
                }
                if (isset($validated['email'])) {
                    $user->email = $validated['email'];
                }
                $user->save();

                // Audit Log
                \App\Models\AuditLog::create([
                    'user_id' => $user->id,
                    'event' => 'Edit Employee Profile',
                    'description' => "Pegawai/Guru {$employee->full_name} memperbarui {$changedCount} data profil.",
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

    public function searchApi(Request $request)
    {
        $search = $request->input('search');
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $activeYear = SchoolYear::where('is_active', true)->first();

        $students = Student::where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            })
            ->with(['classMembers' => function ($q) use ($activeYear) {
                if ($activeYear) {
                    $q->where('school_year_id', $activeYear->id);
                }
                $q->with('schoolClass');
            }])
            ->limit(10)
            ->get()
            ->map(function ($student) {
                $activeMember = $student->classMembers->first();
                $className = $activeMember && $activeMember->schoolClass ? $activeMember->schoolClass->name : 'Tidak terdaftar kelas';
                $classId = $activeMember && $activeMember->schoolClass ? $activeMember->schoolClass->id : null;
                
                return [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'full_name' => $student->full_name,
                    'gender' => $student->gender,
                    'status' => $student->status,
                    'class_name' => $className,
                    'class_id' => $classId
                ];
            });

        return response()->json($students);
    }
}
