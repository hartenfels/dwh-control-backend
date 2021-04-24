<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlaDefinitionTagsTagsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_sla__sla_definition_tags__tags_pivot', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('sla_definition_id');
            $table->unsignedBigInteger('tag_id');

            $table->timestamps();
        });

        Schema::table('dwh_control_sla__sla_definition_tags__tags_pivot', function (Blueprint $table) {
            $table->foreign('sla_definition_id', 'sla_definition_tag__definition_foreign')->references('id')->on('dwh_control_sla__sla_definitions');
            $table->foreign('tag_id', 'sla_definition_tag__tag_foreign')->references('id')->on('dwh_control_sla__sla_definition_tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dwh_control_sla__sla_definition_tags__tags_pivot');
    }
}
