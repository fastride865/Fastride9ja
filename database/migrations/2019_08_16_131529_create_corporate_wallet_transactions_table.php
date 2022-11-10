<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorporateWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_wallet_transactions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('merchant_id')->unsigned();
            $table->integer('corporate_id')->unsigned();
            $table->text('narration');
            $table->integer('transaction_type');
            $table->string('payment_method', 191);
            $table->string('amount', 191);
            $table->integer('platform');
            $table->integer('subscription_package_id')->nullable();
            $table->integer('booking_id')->nullable();
            $table->text('description')->nullable();
            $table->string('receipt_number', 191);
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
        Schema::dropIfExists('corporate_wallet_transactions');
    }
}
