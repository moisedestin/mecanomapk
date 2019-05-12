<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMechanicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mechanics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned();
            $table->string('services')->nullable();
            $table->string('specialisation')->nullable();
            $table->timestamp('birthday')->nullable();
            $table->string('picture')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('availability')->nullable(); //tranche de disponibilité
            $table->smallInteger('sexe')->nullable();
            $table->smallInteger('haveScanner')->nullable()->default(\App\Models\Mechanic::HAVENOt_SCANNER);
            $table->smallInteger('haveTug')->nullable()->default(\App\Models\Mechanic::HAVENOt_TUG);
            $table->float('rating')->default(5.0);
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
        Schema::dropIfExists('mechanics');
    }
}
