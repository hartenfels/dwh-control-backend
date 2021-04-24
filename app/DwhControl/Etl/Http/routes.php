<?php

use App\DwhControl\Api\Http\Route;

Route::middleware('api')->prefix('api/v1/etl')->group(function () {
    Route::resource('etl_definitions', \App\DwhControl\Etl\Http\Controllers\EtlDefinitionController::class);
    Route::resource('automic_etl_definitions', \App\DwhControl\Etl\Http\Controllers\AutomicEtlDefinitionController::class);
    Route::resource('automic_etl_executions', \App\DwhControl\Etl\Http\Controllers\AutomicEtlExecutionController::class);
    Route::get('automic_etl_executions/in_range/{start}/{end}', '\App\DwhControl\Etl\Http\Controllers\AutomicEtlExecutionController@inRange');
});
