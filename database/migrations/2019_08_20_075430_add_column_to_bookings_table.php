<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedInteger('corporate_id')->nullable()->after('merchant_booking_id');
            $table->foreign('corporate_id')->references('id')->on('corporates')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->longText('additional_information')->after('additional_notes')->nullable();
            $table->integer('additional_movers')->nullable()->after('additional_information');
            $table->string('receiver_details')->nullable()->after('additional_movers');

//            $table->string('product_images')->nullable()->after('receiver_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
}
