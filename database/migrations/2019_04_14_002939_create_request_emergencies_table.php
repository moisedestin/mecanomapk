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
            $table->Integer('process_success')->unsigned()->default(0);
            $table->Integer('process_fail')->unsigned()->default(0);
            $table->Integer('mechanic_user_id')->unsigned()->nullable();
            $table->Integer('driver_user_id')->unsigned()->nullable();
            $table->Integer('is_rate')->unsigned()->default(0);
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
