<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToDriverVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_vehicles', function (Blueprint $table) {
            //
            $table->integer('merchant_id')->unsigned()->nullable()->change();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_id')->unsigned()->nullable()->change();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('vehicle_type_id')->unsigned()->nullable()->change();
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('vehicle_make_id')->unsigned()->nullable()->change();
            $table->foreign('vehicle_make_id')->references('id')->on('vehicle_makes')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('vehicle_model_id')->unsigned()->nullable()->change();
            $table->foreign('vehicle_model_id')->references('id')->on('vehicle_models')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('reject_reason_id')->unsigned()->nullable()->change();
            $table->foreign('reject_reason_id')->references('id')->on('reject_reasons')->onUpdate('RESTRICT')->onDelete('CASCADE');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_vehicles', function (Blueprint $table) {
            //
            $table->dropForeign('merchant_id');
            $table->dropForeign('driver_id');
            $table->dropForeign('owner_id');
            $table->dropForeign('driver_type_id');
            $table->dropForeign('driver_make_id');
            $table->dropForeign('driver_model_id');
            $table->dropForeign('reject_reason_id');
        });
    }
}
