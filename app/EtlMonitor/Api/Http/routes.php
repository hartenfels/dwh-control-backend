<?php


use App\EtlMonitor\Api\Http\Route;

Route::middleware('api')->prefix('api/v1/auth')->group(function () {
    Route::get('check', 'App\EtlMonitor\Api\Http\Controllers\AuthController@check__show');
});
