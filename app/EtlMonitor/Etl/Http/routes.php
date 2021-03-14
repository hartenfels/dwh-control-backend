<?php

use App\EtlMonitor\Api\Http\Route;

Route::middleware('api')->prefix('api/v1/etl')->group(function () {
    Route::resource('automic_etl_definitions', \App\EtlMonitor\Etl\Http\Controllers\AutomicEtlDefinitionController::class);
    Route::resource('automic_etl_executions', \App\EtlMonitor\Etl\Http\Controllers\AutomicEtlExecutionController::class);
    Route::get('automic_etl_executions/in_range/{start}/{end}', '\App\EtlMonitor\Etl\Http\Controllers\AutomicEtlExecutionController@inRange');
});
