<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToVehicleOwnerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('vehicle_owner_details', function (Blueprint $table) {
            //
//            $table->integer('driver_vehicle_id')->unsigned()->nullable()->change();
//            $table->foreign('driver_vehicle_id')->references('id')->on('driver_vehicles')->onUpdate('RESTRICT')->onDelete('CASCADE');

//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_owner_details', function (Blueprint $table) {
            //
            $table->dropForeign('driver_vehicle_id');
        });
    }
}
