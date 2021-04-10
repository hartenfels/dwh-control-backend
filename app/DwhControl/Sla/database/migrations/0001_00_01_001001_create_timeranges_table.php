<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimerangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_sla__timeranges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sla_definition_id');
            $table->string('type');

            $table->integer('anchor')->default(0);
            $table->string('range_start');
            $table->string('range_end');

            $table->integer('error_margin_minutes');

            $table->timestamps();
        });

        Schema::table('dwh_control_sla__timeranges', function (Blueprint $table) {
            $table->index(['sla_definition_id']);
            $table->index(['sla_definition_id', 'range_start', 'range_end'], 'dwh_control_sla__timeranges_sla_definition_id_range_index');
            $table->unique(['sla_definition_id', 'type', 'anchor']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dwh_control_sla__timeranges');
    }
}
