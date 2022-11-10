<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToFailBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fail_bookings', function (Blueprint $table) {
            $table->unsignedInteger('corporate_id')->nullable()->after('merchant_id');
            $table->foreign('corporate_id')->references('id')->on('corporates')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fail_bookings', function (Blueprint $table) {
            //
        });
    }
}
