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
        // Modify the enum role to include 'guru'
        // Since ALTER TABLE MODIFY COLUMN for ENUM is somewhat intricate in raw SQL across drivers,
        // and Laravel doesn't natively support changing enum options via Schema builder easily without doctrine/dbal and even then it's tricky.
        // We will try a raw statement for MySQL which is likely the DB being used.

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'sekolah', 'siswa', 'guru') DEFAULT 'siswa'");

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('nip')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');

        // Revert role enum - careful if 'guru' rows exist
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'sekolah', 'siswa') DEFAULT 'siswa'");
    }
};
