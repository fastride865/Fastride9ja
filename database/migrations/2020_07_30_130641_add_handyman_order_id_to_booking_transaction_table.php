<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHandymanOrderIdToBookingTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
//            $table->unsignedInteger('handyman_order_id')->nullable()->after('order_id');
//            $table->foreign('handyman_order_id')->references('id')->on('handyman_orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->dropForeign('booking_transactions_handyman_order_id_foreign');
        });
    }
}
