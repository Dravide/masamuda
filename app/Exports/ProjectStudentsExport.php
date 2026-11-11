<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ProjectStudentsExport extends StringValueBinder implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithCustomValueBinder,
    WithColumnFormatting,
    WithDefaultStyles
{
    // ponytail: binds every cell as explicit string and sets text format (@); upgrade to per-column types if formulas or numeric aggregation are needed.
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'numberFormat' => [
                'formatCode' => NumberFormat::FORMAT_TEXT,
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
        ];
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
