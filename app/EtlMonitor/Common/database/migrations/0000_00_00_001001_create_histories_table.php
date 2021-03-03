<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etlmonitor_common__histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('belongsToModel_id');
            $table->string('belongsToModel_type');

            $table->string('event');
            $table->string('params');

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
        Schema::dropIfExists('etlmonitor_common__histories');
    }
}
