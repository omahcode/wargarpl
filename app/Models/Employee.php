<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'nip', 'nik', 'nuptk', 'full_name', 'gender', 'birth_place', 'birth_date', 'religion',
        'employment_type', 'ptk_type', 'status', 'phone', 'whatsapp', 'email', 'address',
        'last_education', 'study_program', 'university', 'graduation_year', 'primary_subject', 'major_expertise'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id');
    }

    public function homeroomClasses()
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }
}
