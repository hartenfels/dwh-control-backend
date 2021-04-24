<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtlDefinitionsDependsonPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_etl__etl_definitions_dependson_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('etl_definition_id');
            $table->unsignedBigInteger('dependson_etl_definition_id');
        });

        Schema::table('dwh_control_etl__etl_definitions_dependson_pivot', function (Blueprint $table) {
            $table->foreign('etl_definition_id', 'sla_definition_dependson__definition_foreign')->references('id')->on('dwh_control_etl__etl_definitions');
            $table->foreign('dependson_etl_definition_id', 'sla_definition_dependson__foreign_definition_foreign')->references('id')->on('dwh_control_etl__etl_definitions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dwh_control_etl__etl_definitions_dependson_pivot');
    }
}
