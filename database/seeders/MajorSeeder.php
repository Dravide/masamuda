<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            ['name' => 'IPA', 'code' => 'IPA', 'description' => 'Ilmu Pengetahuan Alam'],
            ['name' => 'IPS', 'code' => 'IPS', 'description' => 'Ilmu Pengetahuan Sosial'],
            ['name' => 'Bahasa', 'code' => 'BHS', 'description' => 'Bahasa dan Sastra'],
            ['name' => 'Teknik Komputer dan Jaringan', 'code' => 'TKJ', 'description' => 'Teknik Komputer dan Jaringan'],
            ['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL', 'description' => 'Rekayasa Perangkat Lunak'],
            ['name' => 'Multimedia', 'code' => 'MM', 'description' => 'Multimedia'],
            ['name' => 'Akuntansi', 'code' => 'AKT', 'description' => 'Akuntansi dan Keuangan Lembaga'],
            ['name' => 'Perkantoran', 'code' => 'OTK', 'description' => 'Otomatisasi dan Tata Kelola Perkantoran'],
            ['name' => 'Pemasaran', 'code' => 'PM', 'description' => 'Bisnis Daring dan Pemasaran'],
            ['name' => 'Perhotelan', 'code' => 'HTL', 'description' => 'Perhotelan'],
            ['name' => 'Tata Boga', 'code' => 'TB', 'description' => 'Tata Boga'],
            ['name' => 'Tata Busana', 'code' => 'TBS', 'description' => 'Tata Busana'],
            ['name' => 'Teknik Kendaraan Ringan', 'code' => 'TKR', 'description' => 'Teknik Kendaraan Ringan'],
            ['name' => 'Teknik Sepeda Motor', 'code' => 'TSM', 'description' => 'Teknik dan Bisnis Sepeda Motor'],
            ['name' => 'UMUM', 'code' => 'UMUM', 'description' => 'Jurusan Umum untuk SMP/MTs'],
        ];

        foreach ($majors as $major) {
            Major::firstOrCreate(
                ['name' => $major['name']],
                [
                    'code' => $major['code'],
                    'description' => $major['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
