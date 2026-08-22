<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. School Years
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. "2026/2027"
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 2. Semesters
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('school_years')->onDelete('cascade');
            $table->string('semester'); // e.g. "Ganjil", "Genap"
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 3. Majors (Kompetensi Keahlian)
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. "PPLG"
            $table->string('name'); // e.g. "Pengembangan Perangkat Lunak dan Gim"
            $table->timestamps();
        });

        // 4. Employees (Guru & Tendik)
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nip')->nullable()->unique();
            $table->string('nik')->nullable()->unique();
            $table->string('nuptk')->nullable()->unique();
            $table->string('full_name');
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->string('employment_type')->nullable(); // PNS, PPPK, Non ASN
            $table->string('ptk_type')->nullable(); // Guru, Tenaga Administrasi, Teknisi, etc.
            $table->string('status')->default('Active'); // Active, Inactive
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            
            // Akademik
            $table->string('last_education')->nullable();
            $table->string('study_program')->nullable();
            $table->string('university')->nullable();
            $table->year('graduation_year')->nullable();
            $table->string('primary_subject')->nullable();
            $table->string('major_expertise')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Classes
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('school_years')->onDelete('cascade');
            $table->foreignId('major_id')->constrained('majors')->onDelete('cascade');
            $table->integer('grade_level'); // 10, 11, 12
            $table->string('name'); // e.g. "XI PPLG 1"
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 6. Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nis')->unique();
            $table->string('nisn')->nullable()->unique();
            $table->text('nik')->nullable(); // stored encrypted
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->string('nationality')->default('WNI');
            
            // Akademik
            $table->year('entry_year');
            $table->string('status')->default('Aktif'); // Aktif, Lulus, Keluar, Pindah, Meninggal, Alumni
            $table->string('admission_path')->nullable(); // Jalur masuk
            $table->string('previous_school')->nullable(); // Asal sekolah
            
            // Kontak
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();

            // Alamat
            $table->text('address')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('village')->nullable(); // Desa/Kelurahan
            $table->string('district')->nullable(); // Kecamatan
            $table->string('city')->nullable(); // Kabupaten/Kota
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();

            // Ayah
            $table->string('father_name')->nullable();
            $table->string('father_nik')->nullable();
            $table->year('father_birth_year')->nullable();
            $table->string('father_education')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_income')->nullable();
            $table->string('father_phone')->nullable();

            // Ibu
            $table->string('mother_name')->nullable();
            $table->string('mother_nik')->nullable();
            $table->year('mother_birth_year')->nullable();
            $table->string('mother_education')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_income')->nullable();
            $table->string('mother_phone')->nullable();

            // Wali
            $table->string('visitor_wali_name')->nullable(); // wali_name
            $table->string('wali_nik')->nullable();
            $table->string('wali_relationship')->nullable();
            $table->string('wali_occupation')->nullable();
            $table->string('wali_income')->nullable();
            $table->string('wali_phone')->nullable();

            // Tambahan
            $table->integer('child_number')->nullable();
            $table->integer('sibling_count')->nullable();
            $table->integer('height_cm')->nullable();
            $table->integer('weight_kg')->nullable();
            $table->string('transportation')->nullable();
            $table->string('home_distance_km')->nullable();
            $table->string('travel_time_minutes')->nullable();
            $table->string('special_needs')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Class Members (History of class assignments)
        Schema::create('class_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('school_year_id')->constrained('school_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->integer('attendance_number')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive
            $table->timestamps();
        });

        // 8. Verification Campaigns
        Schema::create('verification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. Student Verifications
        Schema::create('student_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained('verification_campaigns')->onDelete('cascade');
            $table->string('status')->default('Belum diperiksa'); // Belum diperiksa, Sudah diperiksa tanpa perubahan, Sudah diperbaiki
            $table->timestamp('edit_used_at')->nullable();
            $table->timestamp('edit_unlocked_at')->nullable();
            $table->text('unlock_reason')->nullable();
            $table->foreignId('unlocked_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['student_id', 'campaign_id']);
        });

        // 10. Student Change Logs
        Schema::create('student_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained('verification_campaigns')->onDelete('set null');
            $table->foreignId('modified_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // 11. Employee Change Logs
        Schema::create('employee_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('modified_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // 12. Audit Logs (System wide action logging)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('event');
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        // 13. API Clients (Applications)
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. E-Rapor
            $table->string('client_id')->unique();
            $table->string('secret_token');
            $table->text('scopes')->nullable(); // JSON array
            $table->integer('rate_limit')->default(60);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        // 14. API Logs
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_client_id')->nullable()->constrained('api_clients')->onDelete('set null');
            $table->string('endpoint');
            $table->string('method');
            $table->string('ip_address')->nullable();
            $table->integer('response_code');
            $table->integer('duration_ms');
            $table->timestamps();
        });

        // 15. Webhooks
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_client_id')->constrained('api_clients')->onDelete('cascade');
            $table->string('url');
            $table->text('events')->nullable(); // JSON array
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('api_clients');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('employee_change_logs');
        Schema::dropIfExists('student_change_logs');
        Schema::dropIfExists('student_verifications');
        Schema::dropIfExists('verification_campaigns');
        Schema::dropIfExists('class_members');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('majors');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('school_years');
    }
};
