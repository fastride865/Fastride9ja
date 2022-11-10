<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToDriverVehicleDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_vehicle_documents', function (Blueprint $table) {
            //
            $table->integer('document_id')->unsigned()->nullable()->change();
            $table->foreign('document_id')->references('id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_vehicle_id')->unsigned()->nullable()->change();
            $table->foreign('driver_vehicle_id')->references('id')->on('driver_vehicles')->onUpdate('RESTRICT')->onDelete('CASCADE');

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
        Schema::table('driver_vehicle_documents', function (Blueprint $table) {
            //
            $table->dropForeign('document_id');
            $table->dropForeign('driver_vehicle_id');
            $table->dropForeign('reject_reason_id');
        });
    }
}
