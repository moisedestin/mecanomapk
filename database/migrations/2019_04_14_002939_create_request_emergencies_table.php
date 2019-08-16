<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestEmergenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_emergencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trouble')->nullable();
            $table->Integer('vehicule_id')->unsigned()->nullable();
            $table->Integer('location_id')->unsigned()->nullable();
            $table->Integer('mechanic_user_id')->unsigned()->nullable();
            $table->Integer('driver_user_id')->unsigned()->nullable();
            $table->Integer('is_rate')->unsigned()->default(0);
            $table->boolean('is_mechanic_agree')->default(false);
            $table->boolean('is_mechanic_arrived')->default(false);
            $table->boolean('driver_check_arrived')->default(false);
            $table->boolean('driver_check_notarrived')->default(false);
            $table->boolean('driver_decline')->default(false);
            $table->boolean('mechanic_decline')->default(false);
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
        Schema::dropIfExists('request_emergencies');
    }
}
