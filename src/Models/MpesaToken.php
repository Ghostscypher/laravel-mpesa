<?php

namespace Ghostscypher\Mpesa\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MpesaToken extends Model
{
    use HasFactory;

    protected $table = 'mpesa_tokens';

    protected $fillable = [
        'token',
        'expires_at',
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
