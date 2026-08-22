<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'nis', 'nisn', 'nik', 'full_name', 'nickname', 'gender', 'birth_place', 'birth_date', 'religion', 'nationality',
        'entry_year', 'status', 'admission_path', 'previous_school', 'phone', 'whatsapp', 'email', 'address', 'rt', 'rw',
        'village', 'district', 'city', 'province', 'postal_code',
        'father_name', 'father_nik', 'father_birth_year', 'father_education', 'father_occupation', 'father_income', 'father_phone',
        'mother_name', 'mother_nik', 'mother_birth_year', 'mother_education', 'mother_occupation', 'mother_income', 'mother_phone',
        'visitor_wali_name', 'wali_nik', 'wali_relationship', 'wali_occupation', 'wali_income', 'wali_phone',
        'child_number', 'sibling_count', 'height_cm', 'weight_kg', 'transportation', 'home_distance_km', 'travel_time_minutes', 'special_needs'
    ];

    protected function casts(): array
    {
        return [
            'nik' => 'encrypted',
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'student_id');
    }

    public function classMembers()
    {
        return $this->hasMany(ClassMember::class, 'student_id');
    }

    public function currentClassMember()
    {
        return $this->hasOne(ClassMember::class, 'student_id')
            ->whereHas('schoolYear', function ($q) {
                $q->where('is_active', true);
            })
            ->whereHas('semester', function ($q) {
                $q->where('is_active', true);
            });
    }
}
