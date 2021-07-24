<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlaDefinitionStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_sla__sla_definition_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sla_definition_id');
            $table->string('type');

            $table->json('achievement_history')->nullable();

            $table->timestamps();
        });

        Schema::table('dwh_control_sla__sla_definition_statistics', function (Blueprint $table) {
            $table->foreign('sla_definition_id', 'sla_def_stat__sla_definition_foreign')->references('id')->on('dwh_control_sla__sla_definitions');
            $table->index(['sla_definition_id', 'type'], 'dwh_control_sla__sla_def_stats_sla_definition_id_type_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dwh_control_sla__sla_definition_statistics');
    }
}
