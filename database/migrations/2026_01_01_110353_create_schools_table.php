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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->char('npsn', 8)->unique();
            $table->string('name');
            $table->enum('status', ['negeri', 'swasta']);
            $table->string('logo')->nullable();
            
            // Address Components
            $table->string('address'); // Jalan/Desa
            $table->string('district'); // Kecamatan
            $table->string('city'); // Kabupaten/Kota
            $table->string('province'); // Provinsi
            $table->char('postal_code', 5);
            $table->string('rt_rw', 10)->nullable(); // XX/XX format
            
            // Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Contact & Status
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_verified')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
