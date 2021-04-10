<?php

use App\DwhControl\Api\Http\Route;

Route::middleware('api')->prefix('api/v1/common')->group(function () {
    Route::post('broadcast/authenticate', '\App\DwhControl\Common\Http\Controllers\BroadcastAuthController@authenticate');
    Route::resource('users', App\DwhControl\Common\Http\Controllers\UserController::class);
    Route::resource('files', App\DwhControl\Common\Http\Controllers\FileController::class);
    Route::get('files/{file}/display/{name?}', 'App\DwhControl\Common\Http\Controllers\\FileController@display');
    Route::put('alerts/acknowledge', 'App\DwhControl\Common\Http\Controllers\\AlertController@acknowledge');
});

Route::middleware('api')->prefix('api/v1')->group(function () {
    Route::get('_autocomplete/{text}', '\App\DwhControl\Common\Http\Controllers\SearchController@autocomplete');
});
