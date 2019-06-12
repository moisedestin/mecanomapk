<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('request_emergency_id')->unsigned()->nullable();
            $table->String('body')->nullable();
            $table->string('title')->nullable();
            $table->string('date')->nullable();
            $table->String('delay')->nullable();
            $table->Integer('read')->default(0);
            $table->Integer('status')->default(0);
            $table->Integer('recipient_id')->unsigned()->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
