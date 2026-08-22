<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = ['api_client_id', 'endpoint', 'method', 'ip_address', 'response_code', 'duration_ms'];

    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class);
    }
}
