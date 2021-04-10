<?php


use App\DwhControl\Api\Http\Route;

Route::middleware('api')->prefix('api/v1/auth')->group(function () {
    Route::get('check', 'App\DwhControl\Api\Http\Controllers\AuthController@check__show');
});
