<?php

use App\EtlMonitor\Api\Http\Route;

Route::middleware('api')->prefix('api/v1/sla')->group(function () {
    Route::resource('deliverable_slas', \App\EtlMonitor\Sla\Http\Controllers\DeliverableSlaController::class);
    Route::get('deliverable_slas/in_range/{start}/{end}', '\App\EtlMonitor\Sla\Http\Controllers\DeliverableSlaController@inRange');
    Route::post('ingest/progress', 'App\EtlMonitor\Sla\Http\Controllers\IngestController@progress');
});
