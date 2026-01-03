<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPhoto extends Model
{
    protected $fillable = ['student_id', 'project_id', 'photo_type', 'file_path'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
