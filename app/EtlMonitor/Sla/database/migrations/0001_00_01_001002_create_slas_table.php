<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etlmonitor_sla__slas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sla_definition_id')->unsigned();
            $table->string('type');

            $table->timestamp('range_start');
            $table->timestamp('range_end');
            $table->timestamp('achieved_at')->nullable();

            $table->integer('error_margin_minutes');

            $table->double('statistics_average_duration_minutes_lower')->nullable();
            $table->double('statistics_average_duration_minutes_upper')->nullable();

            $table->boolean('is_open')->nullable()->default(true);
            $table->string('status')->nullable()->default('waiting');

            $table->double('target_percent')->nullable()->default(100);
            $table->double('achieved_progress_percent')->nullable();
            $table->double('last_progress_percent')->nullable();

            $table->integer('progress_last_intime_id')->nullable();
            $table->integer('progress_first_intime_achieved_id')->nullable();
            $table->integer('progress_last_late_id')->nullable();
            $table->integer('progress_first_late_achieved_id')->nullable();

            $table->timestamps();
        });

        Schema::table('etlmonitor_sla__slas', function (Blueprint $table) {
            $table->foreign('sla_definition_id', 'sla__definition_foreign')->references('id')->on('etlmonitor_sla__sla_definitions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etlmonitor_sla__slas');
    }
}
