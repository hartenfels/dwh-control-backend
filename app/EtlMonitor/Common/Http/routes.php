<?php

use App\EtlMonitor\Api\Http\Route;

Route::middleware('api')->prefix('api/v1/common')->group(function () {
    Route::post('broadcast/authenticate', '\App\EtlMonitor\Common\Http\Controllers\BroadcastAuthController@authenticate');
    Route::resource('users', App\EtlMonitor\Common\Http\Controllers\UserController::class);
    Route::resource('files', App\EtlMonitor\Common\Http\Controllers\FileController::class);
    Route::get('files/{file}/display/{name?}', 'App\EtlMonitor\Common\Http\Controllers\\FileController@display');
    Route::put('alerts/acknowledge', 'App\EtlMonitor\Common\Http\Controllers\\AlertController@acknowledge');
});

Route::middleware('api')->prefix('api/v1')->group(function () {
    Route::get('_autocomplete/{text}', '\App\EtlMonitor\Common\Http\Controllers\SearchController@autocomplete');
});
