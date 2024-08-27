<?php

namespace Ghostscypher\Mpesa\Models;

use Ghostscypher\Mpesa\Concerns\UsesMpesaEnv;
use Illuminate\Database\Eloquent\Model;

class MpesaToken extends Model
{
    use UsesMpesaEnv;

    protected $table = 'mpesa_tokens';

    protected $fillable = [
        'token',
        'expires_at',
        'environment',
        'app_id',
    ];

    protected $hidden = [
        'token',
    ];

    protected $casts = [
        'expires_at' => 'timestamp',
    ];

    protected static function booted()
    {
        static::addGlobalScope('valid', function ($query) {
            $query->where('expires_at', '>', now()->addMinute());
        });

        static::addGlobalScope('environment', function ($query, $environment = null) {
            $query->where('environment', $environment ?? config('mpesa.env'));
        });

        static::addGlobalScope('app_id', function ($query, $app_id = null) {
            $query->where('app_id', $app_id ?? hash('sha256', sprintf('%s:%s', config('mpesa.consumer_key'), config('mpesa.consumer_secret'))));
        });
    }

    public function isExpired(): bool
    {
        return now()->diffInSeconds($this->expires_at, true) <= 60;
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now()->subMinute());
    }

    public function scopeToken($query, string $token)
    {
        return $query->where('token', $token);
    }
}
