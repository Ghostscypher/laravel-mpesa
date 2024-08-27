<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mpesa callback Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Mpesa callback routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware(config('mpesa.middlewares'))->group(function () {
    Route::post(config('mpesa.stk_push_callback_url'), [config('mpesa.controller'), 'stkPushCallback']);
    Route::post(config('mpesa.c2b_validation_url'), [config('mpesa.controller'), 'c2bValidation']);
    Route::post(config('mpesa.c2b_confirmation_url'), [config('mpesa.controller'), 'c2bConfirmation']);
    Route::post(config('mpesa.b2c_result_url'), [config('mpesa.controller'), 'b2cResult']);
    Route::post(config('mpesa.b2c_timeout_url'), [config('mpesa.controller'), 'b2cTimeout']);
    Route::post(config('mpesa.b2b_result_url'), [config('mpesa.controller'), 'b2bResult']);
    Route::post(config('mpesa.b2b_timeout_url'), [config('mpesa.controller'), 'b2bTimeout']);
    Route::post(config('mpesa.b2b_stk_callback_url'), [config('mpesa.controller'), 'b2bStkCallback']);
    Route::post(config('mpesa.status_result_url'), [config('mpesa.controller'), 'statusResult']);
    Route::post(config('mpesa.status_timeout_url'), [config('mpesa.controller'), 'statusTimeout']);
    Route::post(config('mpesa.reversal_result_url'), [config('mpesa.controller'), 'reversalResult']);
    Route::post(config('mpesa.reversal_timeout_url'), [config('mpesa.controller'), 'reversalTimeout']);
    Route::post(config('mpesa.balance_result_url'), [config('mpesa.controller'), 'balanceResult']);
    Route::post(config('mpesa.balance_timeout_url'), [config('mpesa.controller'), 'balanceTimeout']);
    Route::post(config('mpesa.bill_manager_callback_url'), [config('mpesa.controller'), 'billManagerCallback']);
});
