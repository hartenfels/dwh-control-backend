<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtlDefinitionsDependsonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_etl__etl_definitions_dependson', function (Blueprint $table) {
            $table->bigInteger('etl_definition_id');
            $table->bigInteger('dependson_etl_definition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dwh_control_etl__etl_definitions_dependson');
    }
}
