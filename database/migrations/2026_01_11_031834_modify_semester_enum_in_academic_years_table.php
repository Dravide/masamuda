<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            DB::statement("ALTER TABLE academic_years MODIFY COLUMN semester ENUM('ganjil', 'genap', 'full') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            DB::statement("ALTER TABLE academic_years MODIFY COLUMN semester ENUM('ganjil', 'genap') NOT NULL");
        });
    }
};
