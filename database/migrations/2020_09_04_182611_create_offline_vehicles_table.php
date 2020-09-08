<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfflineVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('driver_id');
            $table->string('mark')->nullable();
            $table->string('model')->nullable();
            $table->string('country')->nullable();
            $table->integer('year')->nullable();
            $table->string('transmission')->nullable();
            $table->string('color')->nullable();

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
        Schema::dropIfExists('offline_vehicles');
    }
}
