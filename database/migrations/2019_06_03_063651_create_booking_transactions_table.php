<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('booking_id')->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');

            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('handyman_order_id')->nullable();

            $table->unsignedInteger('merchant_id')->nullable();

            $table->string('date_time_details')->nullable();
            $table->string('sub_total_before_discount')->nullable();
            $table->string('surge_amount')->nullable();
            $table->string('extra_charges')->nullable()->comment('Night Time/Peak Time');
            $table->string('discount_amount')->nullable();
            $table->string('tax_amount')->nullable();
            $table->string('tip')->nullable();
            $table->string('referral_amount')->default(0)->nullable();
            $table->string('insurance_amount')->default(0)->nullable();
            $table->string('toll_amount')->nullable();
            $table->string('cash_payment')->nullable();
            $table->string('online_payment')->nullable();
            $table->string('customer_paid_amount')->comment('Independent of Cash/Non-cash')->nullable();
            $table->string('cancellation_charge_applied')->nullable();
            $table->string('cancellation_charge_received')->nullable();
            $table->string('company_earning')->nullable()->comment("total commission of merchant from any request of ride,booking,order");
            $table->string('driver_earning')->nullable()->comment("total commission of driver from any request of ride,booking,order");
            $table->string('business_segment_earning')->nullable()->comment("total commission of bs from any request of ride,booking,order");
            $table->string('hotel_earning')->nullable();
            $table->string('amount_deducted_from_driver_wallet')->nullable();
            $table->string('trip_outstanding_amount')->nullable();
            $table->float('rounded_amount',8,2)->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('driver_total_payout_amount')->nullable()->comment("total earning of driver from any request of ride,booking,order");
            $table->string('company_gross_total')->nullable()->comment("total earning of merchant from any request of ride,booking,order");
            $table->string('business_segment_total_payout_amount')->nullable()->comment("total earning of business segment from any request of ride,booking,order");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_transactions');
    }
}
