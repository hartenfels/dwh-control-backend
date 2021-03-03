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
        Schema::create('etlmonitor_sla__sla_progress', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sla_id');
            $table->string('type');

            $table->timestamp('time');
            $table->float('progress_percent');
            $table->string('source');

            $table->boolean('is_override')->nullable()->default(false);

            $table->timestamps();
        });

        Schema::table('etlmonitor_sla__sla_progress', function (Blueprint $table) {
            $table->foreign('sla_id', 'sla_progress__sla_foreign')->references('id')->on('etlmonitor_sla__slas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etlmonitor_sla__sla_progress');
    }
}
