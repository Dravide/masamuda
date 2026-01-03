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
        Schema::table('students', function (Blueprint $table) {
            $table->string('email')->nullable()->after('whatsapp');
            $table->string('grade')->nullable()->after('major'); // Tingkat (1, 2, ..., 12)
            $table->string('class_name')->nullable()->after('grade'); // Nama Kelas/Rombel (A, B, 1, 2)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['email', 'grade', 'class_name']);
        });
    }
};
