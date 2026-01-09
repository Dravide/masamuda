<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProjectStudentsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function query()
    {
        return Student::query()
            ->where('project_id', $this->projectId)
            ->with('photos') // Eager load photos for status check
            ->orderBy('grade')
            ->orderBy('class_name')
            ->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'ID',
            'NIS',
            'NISN',
            'Nama Lengkap',
            'Kelas',
            'Jurusan',
            'WhatsApp',
            'Ada Foto?',
        ];
    }

    public function map($student): array
    {
        $hasPhoto = $student->photos()->where('project_id', $this->projectId)->exists() ? 'Sudah' : 'Belum';

        return [
            $student->id,
            $student->nis,
            $student->nisn,
            $student->name,
            $student->grade . ' ' . $student->class_name,
            $student->major,
            $student->whatsapp,
            $hasPhoto,
        ];
    }
}
