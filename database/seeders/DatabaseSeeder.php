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

        // 3. Seed Majors
        $majors = [
            ['code' => 'PPLG', 'name' => 'Pengembangan Perangkat Lunak dan Gim'],
            ['code' => 'DPIB', 'name' => 'Desain Pemodelan dan Informasi Bangunan'],
            ['code' => 'TJKT', 'name' => 'Teknik Jaringan Komputer dan Telekomunikasi'],
            ['code' => 'TE', 'name' => 'Teknik Elektronika'],
            ['code' => 'TO', 'name' => 'Teknik Otomotif'],
        ];
        $majorIds = [];
        foreach ($majors as $m) {
            $majorIds[$m['code']] = DB::table('majors')->insertGetId(array_merge($m, [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
        }

        // 4. Empty Employees (Teachers & Staff) - Removed mock data for hosting preparation
        $teacherIds = [];

        // 5. Empty Classes - Removed mock data for hosting preparation
        $classIds = [];

        // 6. Empty Students - Removed mock data for hosting preparation
        $studentList = [];

        // 7. Seed Admin Accounts
        $admins = [
            [
                'name' => 'Super Admin Bank Data',
                'email' => 'admin@smkn1-cirebon.sch.id',
                'username' => 'admin',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin Tata Usaha',
                'email' => 'tu@smkn1-cirebon.sch.id',
                'username' => 'admintu',
                'role' => 'admin_tu',
            ],
            [
                'name' => 'Admin Kurikulum',
                'email' => 'kurikulum@smkn1-cirebon.sch.id',
                'username' => 'admin_kurikulum',
                'role' => 'admin_kurikulum',
            ],
            [
                'name' => 'Admin Kesiswaan',
                'email' => 'kesiswaan@smkn1-cirebon.sch.id',
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

        // 10. Seed API Clients
        $clientSecret = 'c7e9bb765b2a3d0fce7839faec78a101'; // dummy key MD5-like
        DB::table('api_clients')->insert([
            'name' => 'E-Rapor SMK Negeri 1 Cirebon',
            'client_id' => 'ERAPOR_NEPer',
            'secret_token' => Hash::make($clientSecret),
            'scopes' => json_encode(['students.read', 'classes.read', 'teachers.read']),
            'rate_limit' => 300,
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // Log the API client key to the console during seed
        $this->command->info("==============================================");
        $this->command->info("API Client created:");
        $this->command->info("Client ID: ERAPOR_NEPer");
        $this->command->info("Secret Token: " . $clientSecret);
        $this->command->info("==============================================");
    }
}
