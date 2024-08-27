<?php

namespace Ghostscypher\Mpesa\Concerns;

/**
 * Model trait to add environment to the model
 */
trait UsesMpesaEnv
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->environment = config('mpesa.env');
            $model->app_id = hash('sha256', sprintf('%s:%s', config('mpesa.consumer_key'), config('mpesa.consumer_secret')));
        });
    }
}
