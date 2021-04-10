<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlaDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_sla__sla_definitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lifecycle_id');
            $table->string('type');

            $table->string('name');
            $table->double('target_percent')->nullable()->default(100);

            $table->string('source')->nullable()->default('external');
            $table->json('rules')->nullable();

            $table->timestamps();
        });

        Schema::table('dwh_control_sla__sla_definitions', function (Blueprint $table) {
            $table->foreign('lifecycle_id', 'sla_definition__lifecycle_foreign')->references('id')->on('dwh_control_sla__sla_definition_lifecycles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dwh_control_sla__sla_definitions');
    }
}
