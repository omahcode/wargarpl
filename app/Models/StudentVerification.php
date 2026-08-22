<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentVerification extends Model
{
    protected $fillable = [
        'student_id', 'campaign_id', 'status',
        'edit_used_at', 'edit_unlocked_at', 'unlock_reason', 'unlocked_by_admin_id',
        'admin_validated_at', 'admin_validated_by', 'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'edit_used_at'       => 'datetime',
            'edit_unlocked_at'   => 'datetime',
            'admin_validated_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function campaign()
    {
        return $this->belongsTo(VerificationCampaign::class, 'campaign_id');
    }

    public function unlockedByAdmin()
    {
        return $this->belongsTo(User::class, 'unlocked_by_admin_id');
    }

    public function adminValidatedByUser()
    {
        return $this->belongsTo(User::class, 'admin_validated_by');
    }

    // Helper: is pending admin validation
    public function isPendingValidation(): bool
    {
        return $this->status === 'Menunggu Validasi Admin';
    }
}
