<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_verifications', function (Blueprint $table) {
            // Column to store when admin validated a student's correction submission
            $table->timestamp('admin_validated_at')->nullable()->after('edit_unlocked_at');
            // Admin who approved/rejected the submitted corrections
            $table->foreignId('admin_validated_by')->nullable()->constrained('users')->onDelete('set null')->after('admin_validated_at');
            // Optional admin note (e.g. reason for rejection)
            $table->text('admin_notes')->nullable()->after('admin_validated_by');
        });
    }

    public function down(): void
    {
        Schema::table('student_verifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_validated_by');
            $table->dropColumn(['admin_validated_at', 'admin_notes']);
        });
    }
};
