<?php

namespace Ghostscypher\Mpesa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaLog extends Model
{
    use HasFactory;

    protected $table = 'mpesa_logs';

    /**
     *
     * @var array
     */
    protected $fillable = [
        'x_reference_id',
        'environment',
        'endpoint',
        'request_headers',
        'request_body',
        'request_method',
        'response_status',
        'response_headers',
        'response_body',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'response_headers' => 'array',
        'response_status' => 'integer',
    ];

}
