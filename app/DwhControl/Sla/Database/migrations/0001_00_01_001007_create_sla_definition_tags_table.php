<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlaDefinitionTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_sla__sla_definition_tags', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->boolean('hide_name')->nullable()->default(false);
            $table->string('color')->nullable()->default('default');
            $table->string('icon')->nullable()->default(null);

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
        Schema::dropIfExists('dwh_control_sla__sla_definition_tags');
    }
}
