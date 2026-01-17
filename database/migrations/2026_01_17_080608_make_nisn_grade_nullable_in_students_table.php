<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Make these columns nullable for guru projects
            $table->string('nisn', 50)->nullable()->change();
            $table->string('grade', 10)->nullable()->change();
            $table->string('class_name', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Note: Rolling back to NOT NULL may fail if there are null values
            $table->string('nisn', 50)->nullable(false)->change();
            $table->string('grade', 10)->nullable(false)->change();
            $table->string('class_name', 50)->nullable(false)->change();
        });
    }
};
