<?php

namespace Ghostscypher\Mpesa\Models;

use Ghostscypher\Mpesa\Concerns\UsesMpesaEnv;
use Illuminate\Database\Eloquent\Model;

class MpesaLog extends Model
{
    use UsesMpesaEnv;

    protected $table = 'mpesa_logs';

    /**
     * @var array
     */
    protected $fillable = [
        'x_reference_id',
        'endpoint',
        'request_headers',
        'request_body',
        'request_method',
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
    ];
}
