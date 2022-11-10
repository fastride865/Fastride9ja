<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxiCompaniesWalletTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxi_companies_wallet_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('CASCADE');
            $table->unsignedInteger('taxi_company_id');
            $table->foreign('taxi_company_id')->references('id')->on('taxi_companies')->onDelete('CASCADE');
            $table->text('narration')->comment('3:RideCommission, 4:SubscripyionPack, 5:Cashback');
            $table->integer('transaction_type')->comment('1 - Credit, 2 - Debit');
            $table->string('payment_method', 191);
            $table->string('amount', 191);
            $table->tinyInteger('platform')->comment('1 - Web, 2 - App');
            $table->text('description')->nullable();
            $table->string('receipt_number', 191);
            $table->unsignedInteger('booking_id')->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxi_companies_wallet_transaction');
    }
}
