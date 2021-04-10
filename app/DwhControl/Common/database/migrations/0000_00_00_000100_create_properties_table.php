<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dwh_control_common__properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('belongsToModel_id');
            $table->string('belongsToModel_type');
            $table->string('type');
            $table->string('name');
            $table->string('datatype')->default('string');
            $table->string('value_string')->nullable();
            $table->bigInteger('value_bigint')->nullable();
            $table->double('value_float')->nullable();
            $table->boolean('value_boolean')->nullable();
            $table->json('value_json_array')->nullable();
            $table->json('value_json_object')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->timestamps();

            $table->unique(['type', 'name', 'belongsToModel_type', 'belongsToModel_id'], 'unique_property_identifier');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dwh_control_common__properties');
    }
}
