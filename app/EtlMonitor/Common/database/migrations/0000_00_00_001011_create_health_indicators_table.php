<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etlmonitor_common__health_indicators', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('health_indicator_type_id');
            $table->unsignedBigInteger('belongsToModel_id');
            $table->string('belongsToModel_type');

            $table->string('name');
            $table->string('state');
            $table->string('state_text');
            $table->double('value');

            $table->timestamps();

            $table->unique(['health_indicator_type_id', 'name', 'belongsToModel_id', 'belongsToModel_type'], 'unique_health_indicator_identifier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etlmonitor_common__health_indicators');
    }
}
