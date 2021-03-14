<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtlDefinitionStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etlmonitor_etl__etl_definition_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('etl_definition_id')->unsigned();
            $table->string('type');

            $table->json('execution_history')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etlmonitor_etl__etl_definition_statistics');
    }
}
