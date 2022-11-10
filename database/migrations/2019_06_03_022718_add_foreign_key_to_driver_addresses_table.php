<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToDriverAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_addresses', function (Blueprint $table) {

            $table->integer('driver_id')->unsigned()->nullable()->change();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('segment_id')->unsigned()->nullable()->change();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_addresses', function (Blueprint $table) {
            //
            $table->dropForeign('driver_id');
        });
    }
}
