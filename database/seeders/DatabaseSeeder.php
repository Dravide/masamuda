<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@masamuda.id',
                'role' => 'admin',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'sekolah'],
            [
                'name' => 'Sekolah User',
                'email' => 'sekolah@masamuda.id',
                'role' => 'sekolah',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'siswa'],
            [
                'name' => 'Siswa User',
                'email' => 'siswa@masamuda.id',
                'role' => 'siswa',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'guru'],
            [
                'name' => 'Guru User',
                'email' => 'guru@masamuda.id',
                'role' => 'guru',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
    }
}
