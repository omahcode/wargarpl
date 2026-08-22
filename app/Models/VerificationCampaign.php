<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationCampaign extends Model
{
    protected $fillable = ['semester_id', 'title', 'start_date', 'end_date', 'is_active'];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function verifications()
    {
        return $this->hasMany(StudentVerification::class, 'campaign_id');
    }
}
