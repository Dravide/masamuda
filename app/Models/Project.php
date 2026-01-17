<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'school_id',
        'name',
        'academic_year_id',
        'type',
        'target',
        'description',
        'date',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public const TYPES = [
        'Pas Photo',
        'Foto Kegiatan',
    ];

    public const TARGETS = [
        'siswa' => 'Siswa',
        'guru' => 'Guru',
    ];

    public const STATUSES = [
        'draft' => 'Draft',
        'active' => 'Aktif',
        'completed' => 'Selesai',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
