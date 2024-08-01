<?php

namespace Ghostscypher\Mpesa\Http;

use Illuminate\Support\Facades\Route;

class Routes extends Route
{
    public static function mpesaRoutes()
    {
        Route::group(
            ['namespace' => 'Ghostscypher\Mpesa\Http\Controllers', 'prefix' => 'mpwh'],
            function () {
                Route::post('status/result', 'MpesaController@statusResult');
                Route::post('status/timeout', 'MpesaController@statusTimeout');
                Route::post('reversal/result', 'MpesaController@reversalResult');
                Route::post('reversal/timeout', 'MpesaController@reversalTimeout');
                Route::post('balance/result', 'MpesaController@balanceResult');
                Route::post('balance/timeout', 'MpesaController@balanceTimeout');
            }
        );
    }
}
