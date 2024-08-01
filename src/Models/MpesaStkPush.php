<?php

namespace Ghostscypher\Mpesa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaStkPush extends Model
{
    use HasFactory;

    protected $table = 'mpesa_stk_pushes';

    /**
     *
     * @var array
     */
    protected $fillable = [
        'phone_number',
        'amount',
        'account_reference',
        'original_request',

        'checkout_request_id',
        'merchant_request_id',
        'response_code',
        'request_response',
        'status',

        'result_code',
        'mpesa_receipt_number',
        'callback_response',
    ];

    protected $casts = [
        'original_request' => 'json',
        'request_response' => 'json',
        'callback_response' => 'json',
    ];

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
