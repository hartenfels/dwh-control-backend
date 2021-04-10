<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlaProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_sla__sla_progress', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sla_id');
            $table->string('type');

            $table->timestamp('time');
            $table->float('progress_percent');
            $table->string('source');

            $table->boolean('is_override')->nullable()->default(false);

            $table->timestamps();
        });

        Schema::table('dwh_control_sla__sla_progress', function (Blueprint $table) {
            $table->foreign('sla_id', 'sla_progress__sla_foreign')->references('id')->on('dwh_control_sla__slas');
            $table->index(['sla_id', 'type']);
            $table->index(['sla_id', 'type', 'is_override']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dwh_control_sla__sla_progress');
    }
}
