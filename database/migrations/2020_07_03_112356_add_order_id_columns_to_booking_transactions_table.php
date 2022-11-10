<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdColumnsToBookingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('handyman_order_id')->references('id')->on('handyman_orders')->onDelete('cascade');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    /**
     * ALTER TABLE `driver_accounts` CHANGE `amount` `amount` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'calculated_amount', CHANGE `settle_type` `settle_type` INT(11) NULL DEFAULT NULL COMMENT '1: Cash 2: NonCash', CHANGE `status` `status` INT(11) NOT NULL DEFAULT '1' COMMENT '1:Generated Only 2:Settled';
     *
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->dropForeign('booking_transactions_order_id_foreign');
        });
    }
}
