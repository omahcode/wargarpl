<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\VerificationCampaign;
use App\Models\StudentVerification;
use App\Models\StudentChangeLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        // Get student's class history
        $classHistory = DB::table('class_members')
            ->join('classes', 'class_members.class_id', '=', 'classes.id')
            ->join('school_years', 'class_members.school_year_id', '=', 'school_years.id')
            ->join('semesters', 'class_members.semester_id', '=', 'semesters.id')
            ->where('class_members.student_id', $student->id)
            ->select('classes.name as class_name', 'school_years.name as year_name', 'semesters.semester')
            ->get();

        // Get active campaign
        $campaign = VerificationCampaign::where('is_active', true)->first();
        $verification = null;

        if ($campaign) {
            $verification = StudentVerification::firstOrCreate(
                ['student_id' => $student->id, 'campaign_id' => $campaign->id],
                ['status' => 'Belum diperiksa']
            );
        }

        // Change logs
        $changeLogs = StudentChangeLog::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.dashboard', compact('student', 'classHistory', 'campaign', 'verification', 'changeLogs'));
    }

    public function verifyCorrect(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        $campaign = VerificationCampaign::where('is_active', true)->first();
        if (!$campaign) {
            return back()->with('error', 'Tidak ada campaign verifikasi yang aktif.');
        }

        $verification = StudentVerification::where('student_id', $student->id)
            ->where('campaign_id', $campaign->id)
            ->first();

        if ($verification && $verification->edit_used_at !== null) {
            return back()->with('error', 'Hak edit Anda untuk semester ini sudah digunakan.');
        }

        if (!$verification) {
            $verification = new StudentVerification();
            $verification->student_id = $student->id;
            $verification->campaign_id = $campaign->id;
        }

        $verification->status = 'Sudah diperiksa tanpa perubahan';
        $verification->edit_used_at = Carbon::now();
        $verification->save();

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'event' => 'Verify Data Correct',
            'description' => "Siswa {$student->full_name} menyatakan data sudah benar tanpa perubahan.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Terima kasih, Anda telah memverifikasi bahwa data Anda sudah benar.');
    }

    public function showEdit()
    {
        $user = Auth::user();
        $student = $user->student;

        $campaign = VerificationCampaign::where('is_active', true)->first();
        if (!$campaign) {
            return redirect()->route('student.dashboard')->with('error', 'Tidak ada campaign verifikasi yang aktif.');
        }

        $verification = StudentVerification::where('student_id', $student->id)
            ->where('campaign_id', $campaign->id)
            ->first();

        if ($verification && $verification->edit_used_at !== null) {
            return redirect()->route('student.dashboard')->with('error', 'Hak edit Anda untuk semester ini sudah digunakan.');
        }

        return view('student.edit', compact('student', 'campaign', 'verification'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        $campaign = VerificationCampaign::where('is_active', true)->first();
        if (!$campaign) {
            return redirect()->route('student.dashboard')->with('error', 'Tidak ada campaign verifikasi yang aktif.');
        }

        $verification = StudentVerification::where('student_id', $student->id)
            ->where('campaign_id', $campaign->id)
            ->first();

        if ($verification && $verification->edit_used_at !== null) {
            return redirect()->route('student.dashboard')->with('error', 'Hak edit Anda untuk semester ini sudah digunakan.');
        }

        // Validate form input
        $validated = $request->validate([
            // Identitas
            'nickname' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            
            // Kontak
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',

            // Alamat
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',

            // Ayah
            'father_name' => 'nullable|string|max:100',
            'father_nik' => 'nullable|string|max:30',
            'father_birth_year' => 'nullable|integer|min:1900|max:'.date('Y'),
            'father_education' => 'nullable|string|max:50',
            'father_occupation' => 'nullable|string|max:100',
            'father_income' => 'nullable|string|max:50',
            'father_phone' => 'nullable|string|max:20',

            // Ibu
            'mother_name' => 'nullable|string|max:100',
            'mother_nik' => 'nullable|string|max:30',
            'mother_birth_year' => 'nullable|integer|min:1900|max:'.date('Y'),
            'mother_education' => 'nullable|string|max:50',
            'mother_occupation' => 'nullable|string|max:100',
            'mother_income' => 'nullable|string|max:50',
            'mother_phone' => 'nullable|string|max:20',

            // Wali
            'visitor_wali_name' => 'nullable|string|max:100', // wali_name
            'wali_nik' => 'nullable|string|max:30',
            'wali_relationship' => 'nullable|string|max:50',
            'wali_occupation' => 'nullable|string|max:100',
            'wali_income' => 'nullable|string|max:50',
            'wali_phone' => 'nullable|string|max:20',

            // Tambahan
            'child_number' => 'nullable|integer|min:1',
            'sibling_count' => 'nullable|integer|min:0',
            'height_cm' => 'nullable|integer|min:30|max:250',
            'weight_kg' => 'nullable|integer|min:5|max:200',
            'transportation' => 'nullable|string|max:100',
            'home_distance_km' => 'nullable|string|max:10',
            'travel_time_minutes' => 'nullable|string|max:10',
            'special_needs' => 'nullable|string|max:250',
        ]);

        $originalData = $student->only(array_keys($validated));
        $changedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($validated as $key => $value) {
                $oldValue = $originalData[$key] ?? null;
                // Handle year comparison or generic string values
                if (trim((string)$oldValue) !== trim((string)$value)) {
                    // Save to student change logs
                    StudentChangeLog::create([
                        'student_id' => $student->id,
                        'campaign_id' => $campaign->id,
                        'modified_by_id' => $user->id,
                        'field_name' => $key,
                        'old_value' => $oldValue,
                        'new_value' => $value,
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    $student->$key = $value;
                    $changedCount++;
                }
            }

            if ($changedCount > 0) {
                $student->save();

                // Dispatch Webhook Event (Custom implementation later)
                try {
                    $this->triggerWebhookEvent('student.updated', $student);
                } catch (\Exception $ex) {
                    // Silently ignore or log webhook dispatch issues to keep transaction safe
                }
            }

            if (!$verification) {
                $verification = new StudentVerification();
                $verification->student_id = $student->id;
                $verification->campaign_id = $campaign->id;
            }

            if ($changedCount > 0) {
                // Changes submitted → must wait for admin to validate
                $verification->status = 'Menunggu Validasi Admin';
            } else {
                // No changes → immediately verified as correct
                $verification->status = 'Sudah diperiksa tanpa perubahan';
            }
            $verification->edit_used_at = Carbon::now();
            $verification->save();

            // Audit Log
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'event' => 'Edit Student Profile',
                'description' => $changedCount > 0
                    ? "Siswa {$student->full_name} mengajukan perbaikan {$changedCount} data profil — menunggu validasi admin."
                    : "Siswa {$student->full_name} menyatakan tidak ada perubahan (data sudah benar).",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('student.dashboard')->with('error', 'Terjadi kesalahan saat menyimpan perubahan: ' . $e->getMessage());
        }

        if ($changedCount > 0) {
            return redirect()->route('student.dashboard')->with('success', 'Perbaikan data Anda telah dikirimkan dan sedang menunggu validasi dari admin. Anda akan mendapat notifikasi setelah diproses.');
        }

        return redirect()->route('student.dashboard')->with('success', 'Profil Anda telah dikonfirmasi benar tanpa perubahan.');
    }

    private function triggerWebhookEvent($event, $student)
    {
        // Enqueue or dispatch webhook jobs
        $webhooks = \App\Models\Webhook::where('status', 'active')
            ->where(function ($q) use ($event) {
                $q->whereJsonContains('events', $event)
                  ->orWhereJsonContains('events', '*');
            })
            ->get();

        foreach ($webhooks as $webhook) {
            // Dispatch a background job to post the update
            \App\Jobs\DispatchWebhookJob::dispatch($webhook->url, $event, $student->toArray());
        }
    }
}
