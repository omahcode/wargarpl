<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\SchoolYear;
use App\Models\Semester;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * GET /api/v1/students
     */
    public function students(Request $request)
    {
        $query = Student::query();

        // Join current class assignment for details & filtering
        $query->leftJoin('class_members', function ($join) {
            $join->on('students.id', '=', 'class_members.student_id')
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('school_years')
                      ->whereColumn('school_years.id', 'class_members.school_year_id')
                      ->where('school_years.is_active', true);
                })
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('semesters')
                      ->whereColumn('semesters.id', 'class_members.semester_id')
                      ->where('semesters.is_active', true);
                });
        })
        ->leftJoin('classes', 'class_members.class_id', '=', 'classes.id')
        ->leftJoin('majors', 'classes.major_id', '=', 'majors.id')
        ->select('students.*', 'classes.name as class_name', 'majors.code as major_code');

        // Scope specifically to RPL & PPLG students (WargaRPL)
        $query->where(function ($q) {
            $q->whereIn('majors.code', ['PPLG', 'RPL'])
              ->orWhereNull('majors.code');
        });

        // Apply filters
        if ($request->has('nis')) {
            $query->where('students.nis', $request->input('nis'));
        }

        if ($request->has('gender')) {
            $query->where('students.gender', $request->input('gender'));
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            // Support English & Indonesian status mappings
            $statusMap = [
                'active' => 'Aktif',
                'graduated' => 'Lulus',
                'alumni' => 'Alumni',
                'moved' => 'Pindah',
                'left' => 'Keluar',
            ];
            $query->where('students.status', $statusMap[strtolower($status)] ?? $status);
        }

        if ($request->has('grade')) {
            $query->where('classes.grade_level', $request->input('grade'));
        }

        if ($request->has('major')) {
            $query->where('majors.code', $request->input('major'));
        }

        if ($request->has('class')) {
            $classParam = str_replace('-', ' ', $request->input('class'));
            $query->where('classes.name', $classParam);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('students.full_name', 'like', "%{$search}%")
                  ->orWhere('students.nis', 'like', "%{$search}%")
                  ->orWhere('students.nisn', 'like', "%{$search}%");
            });
        }

        // Delta Sync (updated_since)
        if ($request->has('updated_since')) {
            try {
                $since = Carbon::parse($request->input('updated_since'));
                $query->where('students.updated_at', '>=', $since);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format parameter updated_since tidak valid. Gunakan format ISO 8601.'
                ], 400);
            }
        }

        $students = $query->paginate(50);

        // Map output structure (excluding sensitive data unless client is admin scope)
        $client = $request->attributes->get('api_client');
        $clientScopes = $client ? ($client->scopes ?? []) : [];
        $hasPrivateAccess = in_array('students.private', $clientScopes) || in_array('*', $clientScopes);

        $mappedData = collect($students->items())->map(function ($student) use ($hasPrivateAccess) {
            $record = [
                'uuid' => $student->uuid,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'full_name' => $student->full_name,
                'gender' => $student->gender,
                'birth_place' => $student->birth_place,
                'birth_date' => $student->birth_date,
                'religion' => $student->religion,
                'nationality' => $student->nationality,
                'status' => $student->status,
                'class' => $student->class_name ? ['name' => $student->class_name, 'major' => $student->major_code] : null,
                'updated_at' => $student->updated_at,
            ];

            if ($hasPrivateAccess) {
                $record['nik'] = $student->nik;
                $record['phone'] = $student->phone;
                $record['whatsapp'] = $student->whatsapp;
                $record['email'] = $student->email;
                $record['address'] = [
                    'street' => $student->address,
                    'rt' => $student->rt,
                    'rw' => $student->rw,
                    'village' => $student->village,
                    'district' => $student->district,
                    'city' => $student->city,
                    'province' => $student->province,
                    'postal_code' => $student->postal_code,
                ];
                $record['father'] = [
                    'name' => $student->father_name,
                    'phone' => $student->father_phone,
                ];
                $record['mother'] = [
                    'name' => $student->mother_name,
                    'phone' => $student->mother_phone,
                ];
            }

            return $record;
        });

        return response()->json([
            'success' => true,
            'data' => $mappedData,
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ]
        ]);
    }

    /**
     * GET /api/v1/students/{uuid}
     */
    public function student(Request $request, $uuid)
    {
        $student = Student::where('uuid', $uuid)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan.'
            ], 404);
        }

        $client = $request->attributes->get('api_client');
        $clientScopes = $client ? ($client->scopes ?? []) : [];
        $hasPrivateAccess = in_array('students.private', $clientScopes) || in_array('*', $clientScopes);

        // Fetch current class
        $currentClass = $student->currentClassMember()->first();

        $data = [
            'uuid' => $student->uuid,
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'full_name' => $student->full_name,
            'gender' => $student->gender,
            'birth_place' => $student->birth_place,
            'birth_date' => $student->birth_date,
            'religion' => $student->religion,
            'status' => $student->status,
            'class' => $currentClass ? [
                'name' => $currentClass->schoolClass->name,
                'major' => $currentClass->schoolClass->major->code,
                'grade' => $currentClass->schoolClass->grade_level
            ] : null,
        ];

        if ($hasPrivateAccess) {
            $data['nik'] = $student->nik;
            $data['phone'] = $student->phone;
            $data['whatsapp'] = $student->whatsapp;
            $data['email'] = $student->email;
            $data['address'] = [
                'street' => $student->address,
                'rt' => $student->rt,
                'rw' => $student->rw,
                'village' => $student->village,
                'district' => $student->district,
                'city' => $student->city,
                'province' => $student->province,
                'postal_code' => $student->postal_code,
            ];
            $data['parents'] = [
                'father' => [
                    'name' => $student->father_name,
                    'phone' => $student->father_phone,
                    'occupation' => $student->father_occupation,
                ],
                'mother' => [
                    'name' => $student->mother_name,
                    'phone' => $student->mother_phone,
                    'occupation' => $student->mother_occupation,
                ]
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * GET /api/v1/students/{uuid}/class-history
     */
    public function studentClassHistory($uuid)
    {
        $student = Student::where('uuid', $uuid)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);
        }

        $history = DB::table('class_members')
            ->join('classes', 'class_members.class_id', '=', 'classes.id')
            ->join('school_years', 'class_members.school_year_id', '=', 'school_years.id')
            ->join('semesters', 'class_members.semester_id', '=', 'semesters.id')
            ->where('class_members.student_id', $student->id)
            ->select('classes.name as class_name', 'classes.grade_level', 'school_years.name as year_name', 'semesters.semester', 'class_members.attendance_number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * GET /api/v1/employees
     */
    public function employees(Request $request)
    {
        $query = Employee::query();

        if ($request->has('type')) {
            $query->where('ptk_type', $request->input('type'));
        }
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        $employees = $query->paginate(50);

        $mapped = collect($employees->items())->map(function ($emp) {
            return [
                'uuid' => $emp->uuid,
                'nip' => $emp->nip,
                'full_name' => $emp->full_name,
                'gender' => $emp->gender,
                'ptk_type' => $emp->ptk_type,
                'employment_type' => $emp->employment_type,
                'status' => $emp->status,
                'primary_subject' => $emp->primary_subject,
                'phone' => $emp->phone,
                'whatsapp' => $emp->whatsapp,
                'email' => $emp->email,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $mapped,
            'meta' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'total' => $employees->total(),
            ]
        ]);
    }

    /**
     * GET /api/v1/employees/{uuid}
     */
    public function employee($uuid)
    {
        $emp = Employee::where('uuid', $uuid)->first();

        if (!$emp) {
            return response()->json(['success' => false, 'message' => 'Guru/Pegawai tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $emp->uuid,
                'nip' => $emp->nip,
                'nik' => $emp->nik,
                'nuptk' => $emp->nuptk,
                'full_name' => $emp->full_name,
                'gender' => $emp->gender,
                'birth_place' => $emp->birth_place,
                'birth_date' => $emp->birth_date,
                'religion' => $emp->religion,
                'ptk_type' => $emp->ptk_type,
                'employment_type' => $emp->employment_type,
                'status' => $emp->status,
                'address' => $emp->address,
                'contacts' => [
                    'phone' => $emp->phone,
                    'whatsapp' => $emp->whatsapp,
                    'email' => $emp->email,
                ],
                'academic' => [
                    'degree' => $emp->last_education,
                    'study_program' => $emp->study_program,
                    'university' => $emp->university,
                    'graduation_year' => $emp->graduation_year,
                    'primary_subject' => $emp->primary_subject,
                    'major_expertise' => $emp->major_expertise,
                ]
            ]
        ]);
    }

    /**
     * GET /api/v1/classes
     */
    public function classes()
    {
        $classes = SchoolClass::with('major', 'homeroomTeacher')->where('status', true)->get();

        $mapped = $classes->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'grade' => $c->grade_level,
                'major' => [
                    'code' => $c->major->code,
                    'name' => $c->major->name,
                ],
                'homeroom_teacher' => $c->homeroomTeacher ? [
                    'uuid' => $c->homeroomTeacher->uuid,
                    'name' => $c->homeroomTeacher->full_name,
                    'nip' => $c->homeroomTeacher->nip
                ] : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $mapped
        ]);
    }

    /**
     * GET /api/v1/classes/{classIdOrName}/students
     */
    public function classStudents($classIdOrName)
    {
        $class = null;
        if (is_numeric($classIdOrName)) {
            $class = SchoolClass::find($classIdOrName);
        } else {
            $className = str_replace('-', ' ', $classIdOrName);
            $class = SchoolClass::where('name', $className)->first();
        }

        if (!$class) {
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        $students = DB::table('class_members')
            ->join('students', 'class_members.student_id', '=', 'students.id')
            ->where('class_members.class_id', $class->id)
            ->where('class_members.status', 'Active')
            ->select('students.uuid', 'students.nis', 'students.nisn', 'students.full_name', 'students.gender', 'class_members.attendance_number')
            ->orderBy('class_members.attendance_number')
            ->get();

        return response()->json([
            'success' => true,
            'class' => $class->name,
            'total' => $students->count(),
            'data' => $students
        ]);
    }

    /**
     * GET /api/v1/statistics/students
     */
    public function statistics()
    {
        $total = Student::count();
        $male = Student::where('gender', 'L')->count();
        $female = Student::where('gender', 'P')->count();

        $gradeStats = DB::table('class_members')
            ->join('classes', 'class_members.class_id', '=', 'classes.id')
            ->join('school_years', 'class_members.school_year_id', '=', 'school_years.id')
            ->where('school_years.is_active', true)
            ->select('classes.grade_level', DB::raw('count(distinct class_members.student_id) as total'))
            ->groupBy('classes.grade_level')
            ->get()
            ->pluck('total', 'grade_level')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'male' => $male,
                'female' => $female,
                'grade' => [
                    '10' => $gradeStats[10] ?? 0,
                    '11' => $gradeStats[11] ?? 0,
                    '12' => $gradeStats[12] ?? 0,
                ]
            ]
        ]);
    }

    /**
     * GET /api/v1/school-years
     */
    public function schoolYears()
    {
        $years = SchoolYear::all();
        return response()->json(['success' => true, 'data' => $years]);
    }

    /**
     * GET /api/v1/semesters
     */
    public function semesters()
    {
        $semesters = Semester::with('schoolYear')->get();
        $mapped = $semesters->map(function ($s) {
            return [
                'id' => $s->id,
                'school_year' => $s->schoolYear->name,
                'semester' => $s->semester,
                'is_active' => $s->is_active,
            ];
        });
        return response()->json(['success' => true, 'data' => $mapped]);
    }

    /**
     * GET /api/health
     */
    public function health()
    {
        $dbStatus = 'disconnected';
        try {
            DB::connection()->getPdo();
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'disconnected';
        }

        return response()->json([
            'status' => 'ok',
            'database' => $dbStatus,
            'api' => 'running',
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
