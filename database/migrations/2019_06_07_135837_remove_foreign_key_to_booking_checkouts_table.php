<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveForeignKeyToBookingCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_checkouts', function (Blueprint $table) {
            //
//            $table->dropForeign('booking_checkouts_card_id_foreign');
            $table->dropForeign('booking_checkouts_payment_method_id_foreign');
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
