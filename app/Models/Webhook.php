<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $fillable = ['api_client_id', 'url', 'events', 'status'];

    protected function casts(): array
    {
        return [
            'events' => 'array',
        ];
    }

    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class);
    }
}
