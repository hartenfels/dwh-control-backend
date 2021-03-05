<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlaStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etlmonitor_sla__sla_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sla_id');
            $table->string('type');

            $table->double('average_duration_minutes_lower')->nullable();
            $table->double('average_duration_minutes_upper')->nullable();

            $table->json('achievement_history')->nullable();

            $table->timestamps();
        });

        Schema::table('etlmonitor_sla__sla_statistics', function (Blueprint $table) {
            $table->foreign('sla_id', 'sla_statistics__sla_foreign')->references('id')->on('etlmonitor_sla__slas');
            $table->index(['sla_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etlmonitor_sla__sla_statistics');
    }
}
