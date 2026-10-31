<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('student_verifications')->truncate();
        DB::table('verification_campaigns')->truncate();
        DB::table('student_change_logs')->truncate();
        DB::table('class_members')->truncate();
        DB::table('students')->truncate();
        DB::table('classes')->truncate();
        DB::table('employees')->truncate();
        DB::table('majors')->truncate();
        DB::table('semesters')->truncate();
        DB::table('school_years')->truncate();
        DB::table('api_clients')->truncate();
        DB::table('api_logs')->truncate();
        DB::table('webhooks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Seed School Years
        $syId = DB::table('school_years')->insertGetId([
            'name' => '2026/2027',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Seed Semesters
        $semGanjilId = DB::table('semesters')->insertGetId([
            'school_year_id' => $syId,
            'semester' => 'Ganjil',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('semesters')->insert([
            'school_year_id' => $syId,
            'semester' => 'Genap',
            'is_active' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Seed Majors (Khusus Siswa Siswi RPL & PPLG)
        $majors = [
            ['code' => 'PPLG', 'name' => 'Pengembangan Perangkat Lunak dan Gim'],
            ['code' => 'RPL',  'name' => 'Rekayasa Perangkat Lunak'],
        ];
        $majorIds = [];
        foreach ($majors as $m) {
            $majorIds[$m['code']] = DB::table('majors')->insertGetId(array_merge($m, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        // 4. Empty Employees (Teachers & Staff)
        $teacherIds = [];

        // 5. Seed Rombel Khusus RPL & PPLG
        $rplClasses = [
            ['name' => 'X PPLG 1',  'grade_level' => 10, 'major_code' => 'PPLG'],
            ['name' => 'X PPLG 2',  'grade_level' => 10, 'major_code' => 'PPLG'],
            ['name' => 'XI PPLG 1', 'grade_level' => 11, 'major_code' => 'PPLG'],
            ['name' => 'XI PPLG 2', 'grade_level' => 11, 'major_code' => 'PPLG'],
            ['name' => 'XII RPL 1', 'grade_level' => 12, 'major_code' => 'RPL'],
            ['name' => 'XII RPL 2', 'grade_level' => 12, 'major_code' => 'RPL'],
        ];
        foreach ($rplClasses as $sc) {
            DB::table('classes')->insert([
                'name' => $sc['name'],
                'grade_level' => $sc['grade_level'],
                'school_year_id' => $syId,
                'major_id' => $majorIds[$sc['major_code']],
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // 6. Empty Students - Siap diimpor melalui file excel Dapodik RPL/PPLG
        $studentList = [];

        // 7. Seed Admin Accounts
        $admins = [
            [
                'name' => 'Super Admin WargaRPL',
                'email' => 'admin@wargarpl.id',
                'username' => 'admin',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin Jurusan RPL',
                'email' => 'rpl@wargarpl.id',
                'username' => 'adminrpl',
                'role' => 'admin_tu',
            ],
            [
                'name' => 'Admin Kurikulum RPL',
                'email' => 'kurikulum@wargarpl.id',
                'username' => 'admin_kurikulum',
                'role' => 'admin_kurikulum',
            ],
            [
                'name' => 'Admin Kesiswaan RPL',
                'email' => 'kesiswaan@wargarpl.id',
                'username' => 'admin_kesiswaan',
                'role' => 'admin_kesiswaan',
            ],
        ];

        foreach ($admins as $admin) {
            DB::table('users')->insert(array_merge($admin, [
                'password' => Hash::make('Password123!'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        // 8. Seed Active Verification Campaign
        $campaignId = DB::table('verification_campaigns')->insertGetId([
            'semester_id' => $semGanjilId,
            'title' => 'Verifikasi Data Mandiri Semester Ganjil 2026/2027',
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->addDays(25),
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 10. Seed API Token
        $defaultToken = 'wgr_live_' . Str::random(40);
        DB::table('api_clients')->insert([
            'name' => 'WargaRPL API Integration',
            'token' => $defaultToken,
            'client_id' => 'wgr_' . Str::lower(Str::random(8)),
            'secret_token' => Hash::make($defaultToken),
            'scopes' => json_encode(['students.read', 'students.private', 'classes.read', 'teachers.read']),
            'rate_limit' => 300,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // Log the API Token to the console during seed
        $this->command->info("==============================================");
        $this->command->info("WargaRPL API Token generated:");
        $this->command->info("Bearer Token: " . $defaultToken);
        $this->command->info("Use Header: Authorization: Bearer " . $defaultToken);
        $this->command->info("==============================================");
    }
}
