<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvailabilitySlaStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etlmonitor_sla__availability_sla_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sla_id');

            $table->json('progress_history')->nullable();

            $table->timestamps();
        });

        Schema::table('etlmonitor_sla__availability_sla_statistics', function (Blueprint $table) {
            $table->foreign('sla_id', 'availability_sla_statistics__sla_foreign')->references('id')->on('etlmonitor_sla__slas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etlmonitor_sla__availability_sla_statistics');
    }
}
