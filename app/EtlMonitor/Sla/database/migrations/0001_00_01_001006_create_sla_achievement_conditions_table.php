<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlaAchievementConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etlmonitor_sla__sla_achievement_conditions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sla_id');

            $table->string('condition')->index('condition_condition_index');

            $table->timestamps();
        });

        Schema::table('etlmonitor_sla__sla_achievement_conditions', function (Blueprint $table) {
            $table->foreign('sla_id', 'sla_achievement_conditions__sla_foreign')->references('id')->on('etlmonitor_sla__slas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etlmonitor_sla__sla_achievement_conditions');
    }
}
