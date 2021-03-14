<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtlDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etlmonitor_etl__etl_definitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');

            $table->string('etl_id')->unique();
            $table->string('name');

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
        Schema::dropIfExists('etlmonitor_etl__etl_definitions');
    }
}
