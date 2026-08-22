<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    protected $fillable = ['name', 'client_id', 'secret_token', 'scopes', 'rate_limit', 'status'];

    protected function casts(): array
    {
        return [
            'scopes' => 'array',
        ];
    }

    public function apiLogs()
    {
        return $this->hasMany(ApiLog::class);
    }

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }
}
