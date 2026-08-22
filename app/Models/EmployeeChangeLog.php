<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeChangeLog extends Model
{
    protected $fillable = ['employee_id', 'modified_by_id', 'field_name', 'old_value', 'new_value', 'ip_address', 'user_agent'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function modifiedBy()
    {
        return $this->belongsTo(User::class, 'modified_by_id');
    }
}
