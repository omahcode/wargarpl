<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = ['school_year_id', 'semester', 'is_active'];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
