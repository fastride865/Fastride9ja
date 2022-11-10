<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToVehicleRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            //
            $table->integer('driver_vehicle_id')->unsigned()->nullable()->change();
            $table->foreign('driver_vehicle_id')->references('id')->on('driver_vehicles')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_id')->unsigned()->nullable()->change();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_requests', function (Blueprint $table) {
            //
            $table->dropForeign('driver_vehicle_id');
            $table->dropForeign('driver_id');
        });
    }
}
