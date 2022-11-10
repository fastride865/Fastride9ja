<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToBookingCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_checkouts', function (Blueprint $table) {
            $table->unsignedInteger('corporate_id')->nullable()->after('merchant_id');
            $table->foreign('corporate_id')->references('id')->on('corporates')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->longText('additional_information')->after('additional_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_checkouts', function (Blueprint $table) {
            //
        });
    }
}
