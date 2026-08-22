<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['school_year_id', 'major_id', 'grade_level', 'name', 'homeroom_teacher_id', 'status'];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function homeroomTeacher()
    {
        return $this->belongsTo(Employee::class, 'homeroom_teacher_id');
    }

    public function members()
    {
        return $this->hasMany(ClassMember::class, 'class_id');
    }
}
