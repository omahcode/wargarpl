<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\VerificationCampaign;
use App\Models\StudentVerification;
use App\Models\SchoolYear;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalEmployees = Employee::count();
        $totalClasses = SchoolClass::count();
        $totalMajors = Major::count();

        // Active academic year
        $activeYear = SchoolYear::where('is_active', true)->first();

        // Grade Statistics (from active year class members)
        $gradeStats = DB::table('class_members')
            ->join('classes', 'class_members.class_id', '=', 'classes.id')
            ->join('school_years', 'class_members.school_year_id', '=', 'school_years.id')
            ->where('school_years.is_active', true)
            ->select('classes.grade_level', DB::raw('count(distinct class_members.student_id) as total'))
            ->groupBy('classes.grade_level')
            ->get()
            ->pluck('total', 'grade_level')
            ->toArray();

        $gradeX   = $gradeStats[10] ?? 0;
        $gradeXI  = $gradeStats[11] ?? 0;
        $gradeXII = $gradeStats[12] ?? 0;

        // Gender Statistics
        $genderStats = Student::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->pluck('total', 'gender')
            ->toArray();

        $maleStudents   = $genderStats['L'] ?? 0;
        $femaleStudents = $genderStats['P'] ?? 0;

        // Major statistics (grouped by major with student count)
        $majorStats = DB::table('class_members')
            ->join('classes', 'class_members.class_id', '=', 'classes.id')
            ->join('majors', 'classes.major_id', '=', 'majors.id')
            ->join('school_years', 'class_members.school_year_id', '=', 'school_years.id')
            ->where('school_years.is_active', true)
            ->select('majors.id', 'majors.code', 'majors.name', DB::raw('count(distinct class_members.student_id) as total'))
            ->groupBy('majors.id', 'majors.code', 'majors.name')
            ->orderByDesc('total')
            ->get();

        // Per-class breakdown with student count (active year)
        $classBreakdown = SchoolClass::with(['major', 'homeroomTeacher'])
            ->withCount(['members as student_count' => function ($q) {
                $q->whereHas('schoolYear', fn($y) => $y->where('is_active', true));
            }])
            ->when($activeYear, fn($q) => $q->where('school_year_id', $activeYear->id))
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        // Campaign monitoring
        $campaign = VerificationCampaign::where('is_active', true)->first();
        $verificationStats = [
            'total'           => 0,
            'belum'           => 0,
            'tanpa_perubahan' => 0,
            'diperbaiki'      => 0,
            'persen'          => 0
        ];

        if ($campaign) {
            $totalCampaignVerifications = StudentVerification::where('campaign_id', $campaign->id)->count();
            $belumDiperiksa             = StudentVerification::where('campaign_id', $campaign->id)->where('status', 'Belum diperiksa')->count();
            $tanpaPerubahan             = StudentVerification::where('campaign_id', $campaign->id)->where('status', 'Sudah diperiksa tanpa perubahan')->count();
            $diperbaiki                 = StudentVerification::where('campaign_id', $campaign->id)->where('status', 'Sudah diperbaiki')->count();

            $sudah  = $tanpaPerubahan + $diperbaiki;
            $persen = $totalCampaignVerifications > 0 ? round(($sudah / $totalCampaignVerifications) * 100) : 0;

            $verificationStats = [
                'total'           => $totalCampaignVerifications,
                'belum'           => $belumDiperiksa,
                'tanpa_perubahan' => $tanpaPerubahan,
                'diperbaiki'      => $diperbaiki,
                'persen'          => $persen
            ];
        }

        // Recent Audit logs
        $recentAuditLogs = \App\Models\AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents', 'totalEmployees', 'totalClasses', 'totalMajors',
            'activeYear',
            'gradeX', 'gradeXI', 'gradeXII', 'maleStudents', 'femaleStudents',
            'majorStats', 'classBreakdown',
            'campaign', 'verificationStats', 'recentAuditLogs'
        ));
    }
}
