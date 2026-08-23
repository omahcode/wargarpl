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
        Schema::create('ptk_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed default values
        \Illuminate\Support\Facades\DB::table('ptk_types')->insert([
            ['name' => 'Guru', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tenaga Administrasi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Teknisi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Laboran', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ptk_types');
    }
};
