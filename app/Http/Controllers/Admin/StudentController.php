<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Major;
use App\Models\User;
use App\Models\ClassMember;
use App\Models\SchoolYear;
use App\Models\Semester;
use App\Models\VerificationCampaign;
use App\Models\StudentVerification;
use App\Models\StudentChangeLog;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $classFilter = $request->input('class_id');
        $majorFilter = $request->input('major_id');
        $verificationFilter = $request->input('verification_status');

        $query = Student::query();

        // Join classes & class members for filtering
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
        ->select('students.*', 'classes.name as class_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('students.full_name', 'like', "%{$search}%")
                  ->orWhere('students.nis', 'like', "%{$search}%")
                  ->orWhere('students.nisn', 'like', "%{$search}%");
            });
        }

        if ($classFilter) {
            $query->where('class_members.class_id', $classFilter);
        }

        if ($majorFilter) {
            $query->where('classes.major_id', $majorFilter);
        }

        if ($verificationFilter) {
            $campaign = VerificationCampaign::where('is_active', true)->first();
            if ($campaign) {
                $query->whereExists(function ($q) use ($campaign, $verificationFilter) {
                    $q->select(DB::raw(1))
                      ->from('student_verifications')
                      ->whereColumn('student_verifications.student_id', 'students.id')
                      ->where('student_verifications.campaign_id', $campaign->id)
                      ->where('student_verifications.status', $verificationFilter);
                });
            }
        }

        $students = $query->paginate(15)->withQueryString();

        $classes = SchoolClass::where('status', true)->get();
        $majors = Major::all();

        return view('admin.students.index', compact('students', 'classes', 'majors', 'search', 'classFilter', 'majorFilter', 'verificationFilter'));
    }

    public function create()
    {
        $classes = SchoolClass::where('status', true)->get();
        return view('admin.students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|numeric|unique:students,nis',
            'nisn' => 'nullable|numeric|unique:students,nisn',
            'nik' => 'nullable|numeric',
            'full_name' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'religion' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'entry_year' => 'required|numeric|min:2000',
        ], [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis Kelamin wajib diisi.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'class_id.required' => 'Rombel wajib ditentukan.',
        ]);

        $activeYear = SchoolYear::where('is_active', true)->first();
        $activeSemester = Semester::where('is_active', true)->first();

        DB::beginTransaction();
        try {
            $student = Student::create([
                'uuid' => Str::uuid()->toString(),
                'nis' => $request->input('nis'),
                'nisn' => $request->input('nisn'),
                'nik' => $request->input('nik') ?: null,
                'full_name' => $request->input('full_name'),
                'nickname' => $request->input('nickname'),
                'gender' => $request->input('gender'),
                'birth_place' => $request->input('birth_place'),
                'birth_date' => $request->input('birth_date'),
                'religion' => $request->input('religion'),
                'nationality' => 'WNI',
                'entry_year' => $request->input('entry_year'),
                'status' => 'Aktif',
            ]);

            // User Login Account (Password default: Tanggal Lahir DDMMYYYY)
            $birthDate = $student->birth_date;
            $defaultPassword = $birthDate ? date('dmY', strtotime($birthDate)) : 'Password123!';

            User::create([
                'name' => $student->full_name,
                'email' => $student->nis . '@smkn1-cirebon.sch.id',
                'username' => $student->nis,
                'role' => 'siswa',
                'student_id' => $student->id,
                'password' => Hash::make($defaultPassword),
            ]);

            // Class Membership
            if ($activeYear && $activeSemester) {
                ClassMember::create([
                    'student_id' => $student->id,
                    'class_id' => $request->input('class_id'),
                    'school_year_id' => $activeYear->id,
                    'semester_id' => $activeSemester->id,
                    'status' => 'Active',
                    'attendance_number' => 1
                ]);
            }

            // Create initial campaign verification
            $campaign = VerificationCampaign::where('is_active', true)->first();
            if ($campaign) {
                StudentVerification::create([
                    'student_id' => $student->id,
                    'campaign_id' => $campaign->id,
                    'status' => 'Belum diperiksa',
                ]);
            }

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Create Student',
                'description' => "Admin mendaftarkan siswa baru: {$student->full_name} (NIS: {$student->nis})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data siswa: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show($uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();

        // Get class history
        $classHistory = DB::table('class_members')
            ->join('classes', 'class_members.class_id', '=', 'classes.id')
            ->join('school_years', 'class_members.school_year_id', '=', 'school_years.id')
            ->join('semesters', 'class_members.semester_id', '=', 'semesters.id')
            ->where('class_members.student_id', $student->id)
            ->select('classes.name as class_name', 'school_years.name as year_name', 'semesters.semester', 'class_members.attendance_number')
            ->get();

        // Get active verification status
        $campaign = VerificationCampaign::where('is_active', true)->first();
        $verification = null;
        if ($campaign) {
            $verification = StudentVerification::where('student_id', $student->id)
                ->where('campaign_id', $campaign->id)
                ->first();
        }

        // Change logs
        $changeLogs = StudentChangeLog::where('student_id', $student->id)
            ->with('modifiedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.students.show', compact('student', 'classHistory', 'campaign', 'verification', 'changeLogs'));
    }

    public function edit($uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();
        $classes = SchoolClass::where('status', true)->get();
        $currentClass = $student->currentClassMember()->first();

        return view('admin.students.edit', compact('student', 'classes', 'currentClass'));
    }

    public function update(Request $request, $uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'nis' => 'required|numeric|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|numeric|unique:students,nisn,' . $student->id,
            'nik' => 'nullable|numeric',
            'full_name' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'birth_place' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'religion' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'status' => 'required|in:Aktif,Lulus,Alumni,Pindah,Keluar',
        ]);

        DB::beginTransaction();
        try {
            // Update Student
            $student->update([
                'nis' => $request->input('nis'),
                'nisn' => $request->input('nisn'),
                'nik' => $request->input('nik') ?: null,
                'full_name' => $request->input('full_name'),
                'nickname' => $request->input('nickname'),
                'gender' => $request->input('gender'),
                'birth_place' => $request->input('birth_place'),
                'birth_date' => $request->input('birth_date'),
                'religion' => $request->input('religion'),
                'status' => $request->input('status'),
            ]);

            // Update Class Member if changed
            $activeYear = SchoolYear::where('is_active', true)->first();
            $activeSemester = Semester::where('is_active', true)->first();
            
            if ($activeYear && $activeSemester) {
                $member = ClassMember::where('student_id', $student->id)
                    ->where('school_year_id', $activeYear->id)
                    ->where('semester_id', $activeSemester->id)
                    ->first();
                
                if ($member) {
                    $member->update(['class_id' => $request->input('class_id')]);
                } else {
                    ClassMember::create([
                        'student_id' => $student->id,
                        'class_id' => $request->input('class_id'),
                        'school_year_id' => $activeYear->id,
                        'semester_id' => $activeSemester->id,
                        'status' => 'Active',
                        'attendance_number' => 1
                    ]);
                }
            }

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Update Student',
                'description' => "Admin memperbarui data siswa: {$student->full_name} (NIS: {$student->nis})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.show', $student->uuid)->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Request $request, $uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();

        DB::beginTransaction();
        try {
            // Delete User Account
            User::where('student_id', $student->id)->delete();

            // Soft Delete Student
            $student->delete();

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Delete Student',
                'description' => "Admin menghapus siswa: {$student->full_name} (NIS: {$student->nis})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function unlock(Request $request, $uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();
        
        $request->validate([
            'reason' => 'required|string|max:250',
        ], [
            'reason.required' => 'Alasan membuka kembali hak edit wajib diisi.',
        ]);

        $campaign = VerificationCampaign::where('is_active', true)->first();
        if (!$campaign) {
            return back()->with('error', 'Tidak ada campaign verifikasi yang aktif.');
        }

        $verification = StudentVerification::where('student_id', $student->id)
            ->where('campaign_id', $campaign->id)
            ->first();

        if (!$verification || $verification->edit_used_at === null) {
            return back()->with('error', 'Siswa ini masih memiliki hak edit atau belum pernah menggunakannya.');
        }

        // Unlock
        $verification->status = 'Belum diperiksa';
        $verification->edit_used_at = null;
        $verification->edit_unlocked_at = Carbon::now();
        $verification->unlock_reason = $request->input('reason');
        $verification->unlocked_by_admin_id = Auth::id();
        $verification->save();

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'event' => 'Unlock Edit Request',
            'description' => "Admin membuka kembali hak edit untuk siswa {$student->full_name} dengan alasan: " . $request->input('reason'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Hak edit siswa berhasil dibuka kembali.');
    }

    /**
     * Approve a student's submitted data corrections (status: Menunggu Validasi Admin → Sudah diperbaiki)
     */
    public function approveCorrection(Request $request, $uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();

        $campaign = VerificationCampaign::where('is_active', true)->first();
        if (!$campaign) {
            return back()->with('error', 'Tidak ada campaign verifikasi aktif.');
        }

        $verification = StudentVerification::where('student_id', $student->id)
            ->where('campaign_id', $campaign->id)
            ->where('status', 'Menunggu Validasi Admin')
            ->first();

        if (!$verification) {
            return back()->with('error', 'Tidak ada pengajuan perbaikan yang menunggu validasi untuk siswa ini.');
        }

        $verification->status               = 'Sudah diperbaiki';
        $verification->admin_validated_at   = \Carbon\Carbon::now();
        $verification->admin_validated_by   = Auth::id();
        $verification->admin_notes          = $request->input('notes');
        $verification->save();

        \App\Models\AuditLog::create([
            'user_id'     => Auth::id(),
            'event'       => 'Approve Student Correction',
            'description' => "Admin menyetujui perbaikan data siswa {$student->full_name}.",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()->with('success', 'Perbaikan data siswa berhasil disetujui.');
    }

    /**
     * Reject a student's submitted data corrections → reset data to original and reopen edit rights
     */
    public function rejectCorrection(Request $request, $uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'notes' => 'required|string|max:500',
        ], [
            'notes.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $campaign = VerificationCampaign::where('is_active', true)->first();
        if (!$campaign) {
            return back()->with('error', 'Tidak ada campaign verifikasi aktif.');
        }

        $verification = StudentVerification::where('student_id', $student->id)
            ->where('campaign_id', $campaign->id)
            ->where('status', 'Menunggu Validasi Admin')
            ->first();

        if (!$verification) {
            return back()->with('error', 'Tidak ada pengajuan perbaikan yang menunggu validasi untuk siswa ini.');
        }

        DB::beginTransaction();
        try {
            // Revert changes: rollback student_change_logs for this campaign
            $changes = \App\Models\StudentChangeLog::where('student_id', $student->id)
                ->where('campaign_id', $campaign->id)
                ->get();

            foreach ($changes as $log) {
                $field = $log->field_name;
                if (in_array($field, $student->getFillable())) {
                    $student->$field = $log->old_value;
                }
            }
            $student->save();

            // Reset verification status so student can edit again
            $verification->status             = 'Belum diperiksa';
            $verification->edit_used_at       = null;
            $verification->admin_validated_at = \Carbon\Carbon::now();
            $verification->admin_validated_by = Auth::id();
            $verification->admin_notes        = $request->input('notes');
            $verification->save();

            \App\Models\AuditLog::create([
                'user_id'     => Auth::id(),
                'event'       => 'Reject Student Correction',
                'description' => "Admin menolak perbaikan data siswa {$student->full_name}: " . $request->input('notes'),
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak perbaikan: ' . $e->getMessage());
        }

        return back()->with('success', 'Perbaikan data ditolak. Data dikembalikan ke kondisi sebelumnya dan siswa dapat mengedit ulang.');
    }

    /**
     * Reset student's user account password to default (birthdate formatted as dmY)
     */
    public function resetPassword(Request $request, $uuid)
    {
        $student = Student::where('uuid', $uuid)->firstOrFail();
        $user = $student->user;

        if (!$user) {
            return back()->with('error', 'Akun user untuk siswa ini tidak ditemukan.');
        }

        $birthDate = $student->birth_date;
        if (!$birthDate) {
            return back()->with('error', 'Siswa tidak memiliki tanggal lahir terdaftar. Tidak dapat menghasilkan password default.');
        }

        $defaultPassword = date('dmY', strtotime($birthDate));
        $user->password = Hash::make($defaultPassword);
        $user->save();

        \App\Models\AuditLog::create([
            'user_id' => Auth::id(),
            'event' => 'Reset Student Password',
            'description' => "Admin mereset password siswa {$student->full_name} (NIS: {$student->nis}) kembali ke bawaan.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "Password untuk siswa {$student->full_name} berhasil direset ke password default bawaan ({$defaultPassword}).");
    }

    public function deleteAll(Request $request)
    {
        DB::beginTransaction();
        try {
            // Delete Student user accounts
            User::where('role', 'siswa')->delete();
            
            // Delete dependent child tables (using DML delete() instead of truncate() to avoid implicit commits)
            DB::table('class_members')->delete();
            DB::table('student_verifications')->delete();
            DB::table('student_change_logs')->delete();
            
            // Force delete students
            Student::query()->forceDelete();

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => Auth::id(),
                'event' => 'Truncate Students Table',
                'description' => 'Admin membersihkan/menghapus secara keseluruhan seluruh data siswa dan akun login terkait.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data siswa keseluruhan: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.index')->with('success', 'Seluruh data siswa berhasil dihapus secara permanen.');
    }
}
