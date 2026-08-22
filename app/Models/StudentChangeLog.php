<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentChangeLog extends Model
{
    protected $fillable = ['student_id', 'campaign_id', 'modified_by_id', 'field_name', 'old_value', 'new_value', 'ip_address', 'user_agent'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function campaign()
    {
        return $this->belongsTo(VerificationCampaign::class, 'campaign_id');
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }
}
