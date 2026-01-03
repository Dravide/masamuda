<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Sekolah User',
            'username' => 'sekolah',
            'role' => 'sekolah',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Siswa User',
            'username' => 'siswa',
            'role' => 'siswa',
            'password' => bcrypt('password'),
        ]);
    }
}
