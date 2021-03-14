<?php

use App\EtlMonitor\Api\Http\Route;
use App\EtlMonitor\Common\Models\User;

Route::middleware('api')->prefix('api/v1/sla')->group(function () {
    Route::resource('deliverable_sla_definitions', \App\EtlMonitor\Sla\Http\Controllers\DeliverableSlaDefinitionController::class);
    Route::resource('deliverable_slas', \App\EtlMonitor\Sla\Http\Controllers\DeliverableSlaController::class);
    Route::resource('availability_sla_definitions', \App\EtlMonitor\Sla\Http\Controllers\AvailabilitySlaDefinitionController::class);
    Route::resource('availability_slas', \App\EtlMonitor\Sla\Http\Controllers\AvailabilitySlaController::class);
    Route::get('slas/in_range/{start}/{end}', '\App\EtlMonitor\Sla\Http\Controllers\SlaController@inRange');
    Route::post('ingest/progress', 'App\EtlMonitor\Sla\Http\Controllers\IngestController@progress');

    Route::get('_debug', function () { User::find(1); });
});
