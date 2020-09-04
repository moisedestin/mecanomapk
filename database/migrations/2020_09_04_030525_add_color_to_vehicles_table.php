<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorToVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->string('color')->nullable();
            });
        } catch (\Illuminate\Database\QueryException $exception) {
            echo "Warning: column color already exists in vehicles table \n";
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
}
