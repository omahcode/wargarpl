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

        // 4. Seed Employees (Teachers & Staff)
        $teachersData = [
            [
                'nip' => '198001012005011001',
                'nik' => '3274010101800001',
                'nuptk' => '1234567890123456',
                'full_name' => 'Ahmad Sobirin, S.Pd., M.T.',
                'gender' => 'L',
                'employment_type' => 'PNS',
                'ptk_type' => 'Guru',
                'primary_subject' => 'Pemrograman Web',
            ],
            [
                'nip' => '198505052010012002',
                'nik' => '3274010505850002',
                'nuptk' => '2345678901234567',
                'full_name' => 'Siti Aminah, S.Kom.',
                'gender' => 'P',
                'employment_type' => 'PPPK',
                'ptk_type' => 'Guru',
                'primary_subject' => 'Dasar Desain Grafis',
            ],
            [
                'nip' => '199008122018011003',
                'nik' => '3274011208900003',
                'nuptk' => '3456789012345678',
                'full_name' => 'Budi Santoso, S.T.',
                'gender' => 'L',
                'employment_type' => 'PNS',
                'ptk_type' => 'Guru',
                'primary_subject' => 'Jaringan Komputer',
            ],
            [
                'nip' => '199211302022012004',
                'nik' => '3274013011920004',
                'nuptk' => '4567890123456789',
                'full_name' => 'Dewi Lestari, S.Pd.',
                'gender' => 'P',
                'employment_type' => 'Non ASN',
                'ptk_type' => 'Guru',
                'primary_subject' => 'Bahasa Inggris',
            ],
            [
                'nip' => '197803152003011005',
                'nik' => '3274011503780005',
                'nuptk' => '5678901234567890',
                'full_name' => 'Eko Prasetyo, S.E.',
                'gender' => 'L',
                'employment_type' => 'PNS',
                'ptk_type' => 'Tenaga Administrasi',
                'primary_subject' => null,
            ],
        ];

        $teacherIds = [];
        foreach ($teachersData as $t) {
            $uuid = Str::uuid()->toString();
            $empId = DB::table('employees')->insertGetId(array_merge($t, [
                'uuid' => $uuid,
                'birth_place' => 'Cirebon',
                'birth_date' => '1980-05-15',
                'religion' => 'Islam',
                'status' => 'Active',
                'phone' => '08123456789',
                'whatsapp' => '08123456789',
                'email' => Str::slug($t['full_name'], '') . '@gmail.com',
                'address' => 'Jl. Perjuangan No. 1, Cirebon',
                'last_education' => 'S1',
                'study_program' => 'Pendidikan Teknik Informatika',
                'university' => 'Universitas Negeri Yogyakarta',
                'graduation_year' => 2002,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));
            $teacherIds[] = $empId;

            // Create User Account for Employee
            DB::table('users')->insert([
                'name' => $t['full_name'],
                'email' => Str::slug($t['full_name'], '') . '@smkn1-cirebon.sch.id',
                'username' => $t['nip'],
                'role' => 'employee',
                'employee_id' => $empId,
                'password' => Hash::make('Password123!'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // 5. Seed Classes
        $classesData = [
            ['name' => 'X PPLG 1', 'grade' => 10, 'major' => 'PPLG', 'teacher' => $teacherIds[0]],
            ['name' => 'X PPLG 2', 'grade' => 10, 'major' => 'PPLG', 'teacher' => $teacherIds[1]],
            ['name' => 'XI PPLG 1', 'grade' => 11, 'major' => 'PPLG', 'teacher' => $teacherIds[2]],
            ['name' => 'XI PPLG 2', 'grade' => 11, 'major' => 'PPLG', 'teacher' => $teacherIds[3]],
            ['name' => 'XII PPLG 1', 'grade' => 12, 'major' => 'PPLG', 'teacher' => $teacherIds[0]],
            ['name' => 'XI DPIB 1', 'grade' => 11, 'major' => 'DPIB', 'teacher' => $teacherIds[1]],
            ['name' => 'XI TJKT 1', 'grade' => 11, 'major' => 'TJKT', 'teacher' => $teacherIds[2]],
        ];
        $classIds = [];
        foreach ($classesData as $c) {
            $classIds[$c['name']] = DB::table('classes')->insertGetId([
                'school_year_id' => $syId,
                'major_id' => $majorIds[$c['major']],
                'grade_level' => $c['grade'],
                'name' => $c['name'],
                'homeroom_teacher_id' => $c['teacher'],
                'status' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // 6. Seed Students (30 Dummy Students distributed across classes)
        $firstNames = ['Muhammad', 'Ahmad', 'Diki', 'Adi', 'Agung', 'Vian', 'Budi', 'Rizky', 'Rudi', 'Fajar', 'Siti', 'Dewi', 'Indah', 'Fitri', 'Lestari', 'Amalia', 'Putri', 'Intan', 'Wulan', 'Dian'];
        $lastNames = ['Pratama', 'Hidayat', 'Saputra', 'Kurniawan', 'Ramadhan', 'Wijaya', 'Santoso', 'Gunawan', 'Nugroho', 'Ariyanto', 'Rahmawati', 'Lestari', 'Sari', 'Indah', 'Permata', 'Wahyuni', 'Utami', 'Kartika', 'Setiawati', 'Hapsari'];

        $studentList = [];
        $nisBase = 24251000;
        $nisnBase = 1000000000;
        $classNames = array_keys($classIds);

        for ($i = 1; $i <= 35; $i++) {
            $gender = $i % 2 == 0 ? 'P' : 'L';
            $firstName = $firstNames[($i * 3) % count($firstNames)];
            $lastName = $lastNames[($i * 7) % count($lastNames)];
            $fullName = $firstName . ' ' . $lastName;
            $nickname = $firstName;
            $nis = (string)($nisBase + $i);
            $nisn = (string)($nisnBase + $i * 123);
            $uuid = Str::uuid()->toString();

            // Insert Student
            $studentId = DB::table('students')->insertGetId([
                'uuid' => $uuid,
                'nis' => $nis,
                'nisn' => $nisn,
                'nik' => '32740' . ($i + 10) . '0101000' . $i,
                'full_name' => $fullName,
                'nickname' => $nickname,
                'gender' => $gender,
                'birth_place' => 'Cirebon',
                'birth_date' => '2009-08-15',
                'religion' => 'Islam',
                'nationality' => 'WNI',
                'entry_year' => 2024,
                'status' => 'Aktif',
                'admission_path' => 'Zonasi',
                'previous_school' => 'SMP Negeri 1 Cirebon',
                'phone' => '08571234' . sprintf("%03d", $i),
                'whatsapp' => '08571234' . sprintf("%03d", $i),
                'email' => Str::slug($fullName, '') . '@gmail.com',
                'address' => 'Jl. Cirebon No. ' . $i,
                'rt' => '01',
                'rw' => '02',
                'village' => 'Sunyaragi',
                'district' => 'Kesambi',
                'city' => 'Kota Cirebon',
                'province' => 'Jawa Barat',
                'postal_code' => '45132',
                'father_name' => 'Father of ' . $nickname,
                'father_phone' => '08111222' . sprintf("%03d", $i),
                'mother_name' => 'Mother of ' . $nickname,
                'mother_phone' => '08222333' . sprintf("%03d", $i),
                'child_number' => 2,
                'sibling_count' => 3,
                'height_cm' => 165 + ($i % 5),
                'weight_kg' => 55 + ($i % 10),
                'transportation' => 'Sepeda Motor',
                'home_distance_km' => '2.5',
                'travel_time_minutes' => '15',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Assign Class
            // Distribute students evenly into classes
            $className = $classNames[$i % count($classNames)];
            $classId = $classIds[$className];

            DB::table('class_members')->insert([
                'student_id' => $studentId,
                'class_id' => $classId,
                'school_year_id' => $syId,
                'semester_id' => $semGanjilId,
                'attendance_number' => ($i % 36) + 1,
                'status' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Create User Account for Student
            DB::table('users')->insert([
                'name' => $fullName,
                'email' => $nis . '@smkn1-cirebon.sch.id',
                'username' => $nis,
                'role' => 'siswa',
                'student_id' => $studentId,
                'password' => Hash::make('Password123!'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $studentList[] = $studentId;
        }

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

        // 9. Seed Student Verifications (Default status: 'Belum diperiksa')
        // Set a few as "Sudah diperiksa tanpa perubahan" and "Sudah diperbaiki" to populate charts
        foreach ($studentList as $idx => $studentId) {
            $status = 'Belum diperiksa';
            $editUsedAt = null;
            
            if ($idx % 5 == 1) {
                $status = 'Sudah diperiksa tanpa perubahan';
                $editUsedAt = Carbon::now()->subDays(2);
            } elseif ($idx % 5 == 2) {
                $status = 'Sudah diperbaiki';
                $editUsedAt = Carbon::now()->subDays(1);
            }

            DB::table('student_verifications')->insert([
                'student_id' => $studentId,
                'campaign_id' => $campaignId,
                'status' => $status,
                'edit_used_at' => $editUsedAt,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

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
