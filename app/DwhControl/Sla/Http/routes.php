<?php

use App\DwhControl\Api\Http\Route;
use App\DwhControl\Sla\Models\SlaDefinition;

Route::middleware('api')->prefix('api/v1/sla')->group(function () {
    Route::resource('slas', \App\DwhControl\Sla\Http\Controllers\SlaController::class);
    Route::resource('deliverable_sla_definitions', \App\DwhControl\Sla\Http\Controllers\DeliverableSlaDefinitionController::class);
    Route::resource('deliverable_slas', \App\DwhControl\Sla\Http\Controllers\DeliverableSlaController::class);
    Route::resource('availability_sla_definitions', \App\DwhControl\Sla\Http\Controllers\AvailabilitySlaDefinitionController::class);
    Route::resource('availability_slas', \App\DwhControl\Sla\Http\Controllers\AvailabilitySlaController::class);
    Route::get('sla_definitions/in_range/{start}/{end}', '\App\DwhControl\Sla\Http\Controllers\SlaDefinitionController@inRange');
    Route::get('slas/in_range/{start}/{end}', '\App\DwhControl\Sla\Http\Controllers\SlaController@inRange');
    Route::post('ingest/progress', 'App\DwhControl\Sla\Http\Controllers\IngestController@progress');

    Route::get('_debug', function () {
        SlaDefinition::find(1)->slas()->orderBy('range_start', 'desc')->first()->updateProgress();
    });
});
