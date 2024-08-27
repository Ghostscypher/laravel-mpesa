<?php

namespace Ghostscypher\Mpesa\Models;

use Ghostscypher\Mpesa\Concerns\UsesMpesaEnv;
use Illuminate\Database\Eloquent\Model;

class MpesaCallback extends Model
{
    use UsesMpesaEnv;

    protected $table = 'mpesa_callbacks';

    /**
     * @var array
     */
    protected $fillable = [
        'reference_id',
        'callback_type',
        'endpoint',
        'method',
        'ip',
        'user_agent',
        'request_headers',
        'request_body',
        'response_status',
        'response_headers',
        'response_body',

        'environment',
        'app_id',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'response_headers' => 'array',
        'response_status' => 'integer',
        'request_body' => 'json',
        'response_body' => 'json',
    ];

    public function scopeFailed($query)
    {
        return $query->where('response_status', '!=', 200);
    }

    public function scopeSuccess($query)
    {
        return $query->where('response_status', 200);
    }

    public function scopeType($query, string $type)
    {
        return $query->where('callback_type', $type);
    }
}
