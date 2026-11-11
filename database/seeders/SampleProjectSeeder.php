<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Project;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentPhoto;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SampleProjectSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tahun Pelajaran
        $academicYear = AcademicYear::firstOrCreate(
            ['year_name' => '2024/2025'],
            [
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-30',
                'semester' => 'ganjil',
                'is_active' => true,
            ]
        );

        // 2. Sekolah
        $sekolahUser = User::where('username', 'sekolah')->first();
        $school = School::firstOrCreate(
            ['npsn' => '20101234'],
            [
                'name' => 'SMA Negeri 1 Masamuda',
                'jenjang' => 'sma',
                'status' => 'negeri',
                'address' => 'Jl. Pendidikan No. 10',
                'district' => 'Kecamatan Kota',
                'city' => 'Kota Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '10110',
                'user_id' => $sekolahUser?->id,
                'is_verified' => true,
            ]
        );

        if ($sekolahUser && $school->user_id !== $sekolahUser->id) {
            $school->update(['user_id' => $sekolahUser->id]);
        }

        // 3. Guru
        $guruUser = User::where('username', 'guru')->first();
        if ($guruUser) {
            Teacher::firstOrCreate(
                ['user_id' => $guruUser->id],
                [
                    'school_id' => $school->id,
                    'nip' => '198501012010011001',
                    'whatsapp' => '081234567890',
                    'is_activated' => true,
                ]
            );
        }

        // 4. Project
        $project = Project::firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => 'Buku Tahunan Siswa 2024/2025',
            ],
            [
                'academic_year_id' => $academicYear->id,
                'type' => 'Pas Photo',
                'target' => 'siswa',
                'description' => 'Projek foto ijazah dan buku tahunan angkatan 2024/2025.',
                'date' => '2024-10-15',
                'status' => 'active',
            ]
        );

        // Ensure storage directory
        $storageDir = "student-photos/{$school->id}";
        Storage::disk('public')->makeDirectory($storageDir);

        // 5. Data Siswa
        $studentsData = [
            [
                'name' => 'Ahmad Fauzi',
                'nis' => '10201',
                'nisn' => 'siswa',
                'grade' => '12',
                'class_name' => 'XII IPA 1',
                'major' => 'IPA',
                'bg_color' => [211, 47, 47],
            ],
            [
                'name' => 'Siti Nurhaliza',
                'nis' => '10202',
                'nisn' => '0061234562',
                'grade' => '12',
                'class_name' => 'XII IPA 1',
                'major' => 'IPA',
                'bg_color' => [25, 118, 210],
            ],
            [
                'name' => 'Budi Santoso',
                'nis' => '10203',
                'nisn' => '0061234563',
                'grade' => '12',
                'class_name' => 'XII IPS 1',
                'major' => 'IPS',
                'bg_color' => [211, 47, 47],
            ],
            [
                'name' => 'Dewi Lestari',
                'nis' => '10204',
                'nisn' => '0061234564',
                'grade' => '12',
                'class_name' => 'XII IPS 1',
                'major' => 'IPS',
                'bg_color' => [25, 118, 210],
            ],
            [
                'name' => 'Rizky Pratama',
                'nis' => '10205',
                'nisn' => '0061234565',
                'grade' => '12',
                'class_name' => 'XII IPA 2',
                'major' => 'IPA',
                'bg_color' => [211, 47, 47],
            ],
        ];

        foreach ($studentsData as $data) {
            $student = Student::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'project_id' => $project->id,
                    'nis' => $data['nis'],
                ],
                [
                    'nisn' => $data['nisn'],
                    'name' => $data['name'],
                    'grade' => $data['grade'],
                    'class_name' => $data['class_name'],
                    'major' => $data['major'],
                    'birth_place' => 'Jakarta',
                    'birth_date' => '2007-05-12',
                    'address' => 'Jl. Merdeka No. 1',
                    'whatsapp' => '081298765432',
                    'email' => Str::slug($data['name']) . '@example.com',
                ]
            );

            // Generate Formal Photo
            $formalFilename = "{$storageDir}/{$student->id}_formal.jpg";
            $this->generateSamplePhoto(
                storage_path("app/public/{$formalFilename}"),
                $data['name'],
                $data['nis'],
                'PAS FOTO RESMI',
                $data['bg_color']
            );

            // Generate Bebas Photo
            $bebasFilename = "{$storageDir}/{$student->id}_bebas.jpg";
            $this->generateSamplePhoto(
                storage_path("app/public/{$bebasFilename}"),
                $data['name'],
                $data['nis'],
                'FOTO BEBAS',
                [46, 125, 50]
            );

            $student->update(['photo' => $formalFilename]);

            StudentPhoto::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'project_id' => $project->id,
                    'photo_type' => 'Formal',
                ],
                [
                    'file_path' => $formalFilename,
                ]
            );

            StudentPhoto::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'project_id' => $project->id,
                    'photo_type' => 'Bebas',
                ],
                [
                    'file_path' => $bebasFilename,
                ]
            );
        }
    }

    private function generateSamplePhoto(string $path, string $name, string $nis, string $label, array $bgColor): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $w = 400;
        $h = 500;
        $im = imagecreatetruecolor($w, $h);

        $bg = imagecolorallocate($im, $bgColor[0], $bgColor[1], $bgColor[2]);
        $white = imagecolorallocate($im, 255, 255, 255);
        $dark = imagecolorallocate($im, 33, 33, 33);
        $shadow = imagecolorallocate($im, 200, 200, 200);

        imagefill($im, 0, 0, $bg);

        // Portrait silhouette
        imagefilledellipse($im, 200, 420, 280, 240, $white);
        imagefilledrectangle($im, 175, 250, 225, 320, $shadow);
        imagefilledellipse($im, 200, 190, 140, 170, $white);

        // Badge banner
        imagefilledrectangle($im, 20, 430, 380, 485, $white);
        imagerectangle($im, 20, 430, 380, 485, $dark);

        $text1 = strtoupper($name);
        $text2 = "NIS: {$nis} | {$label}";

        $font = 4;
        $fontW = imagefontwidth($font);
        $x1 = max(25, (int)(($w - (strlen($text1) * $fontW)) / 2));
        $x2 = max(25, (int)(($w - (strlen($text2) * imagefontwidth(2))) / 2));

        imagestring($im, $font, $x1, 440, $text1, $dark);
        imagestring($im, 2, $x2, 462, $text2, $dark);

        imagejpeg($im, $path, 90);
        imagedestroy($im);
    }
}
