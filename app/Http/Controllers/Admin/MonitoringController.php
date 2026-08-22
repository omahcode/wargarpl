<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\VerificationCampaign;

class MonitoringController extends Controller
{
    public function index()
    {
        $campaign = VerificationCampaign::where('is_active', true)->first();
        $classStats = [];

        if ($campaign) {
            $classStats = DB::table('classes')
                ->join('class_members', 'classes.id', '=', 'class_members.class_id')
                ->join('school_years', 'class_members.school_year_id', '=', 'school_years.id')
                ->leftJoin('student_verifications', function ($join) use ($campaign) {
                    $join->on('class_members.student_id', '=', 'student_verifications.student_id')
                        ->where('student_verifications.campaign_id', '=', $campaign->id);
                })
                ->where('school_years.is_active', true)
                ->select(
                    'classes.id',
                    'classes.name',
                    DB::raw('count(distinct class_members.student_id) as total_students'),
                    DB::raw("sum(case when student_verifications.status in ('Sudah diperiksa tanpa perubahan', 'Sudah diperbaiki') then 1 else 0 end) as verified_count"),
                    DB::raw("sum(case when student_verifications.status = 'Belum diperiksa' or student_verifications.status is null then 1 else 0 end) as unverified_count")
                )
                ->groupBy('classes.id', 'classes.name')
                ->orderBy('classes.name')
                ->get();
        }

        return view('admin.monitoring', compact('campaign', 'classStats'));
    }
}
