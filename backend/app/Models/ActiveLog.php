<?php

namespace App\Models;

use Database\Factories\ActiveLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveLog extends Model
{
    /** @use HasFactory<ActiveLogFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'request_data',
        'response_data',
        'level',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
