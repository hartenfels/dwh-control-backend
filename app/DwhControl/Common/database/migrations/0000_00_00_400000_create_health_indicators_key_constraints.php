<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthIndicatorsKeyConstraints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dwh_control_common__health_indicators', function (Blueprint $table) {
            $table->foreign('health_indicator_type_id', 'health_indicator__health_indicator_type_foreign')
                ->references('id')
                ->on('dwh_control_common__health_indicator_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dwh_control_common__health_indicators', function (Blueprint $table) {
            $table->dropForeign('health_indicator_type_id');
        });
    }
}
