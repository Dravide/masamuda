<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'project_id',
        'nis',
        'nisn',
        'name',
        'address',
        'whatsapp',
        'email',
        'birth_place',
        'birth_date',
        'major',
        'grade',
        'class_name',
        'magic_token',
        'photo',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            $student->magic_token = Str::random(32);
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function photos()
    {
        return $this->hasMany(StudentPhoto::class);
    }
}

