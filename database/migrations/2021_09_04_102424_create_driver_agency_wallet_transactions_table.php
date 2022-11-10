<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverAgencyWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_agency_wallet_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('CASCADE');
            $table->unsignedInteger('driver_agency_id');
            $table->foreign('driver_agency_id')->references('id')->on('driver_agencies')->onDelete('CASCADE');
            $table->text('narration')->comment('');
            $table->integer('transaction_type')->comment('1 - Credit, 2 - Debit');
            $table->string('payment_method', 191);
            $table->string('amount', 191);
            $table->tinyInteger('platform')->comment('1 - Web, 2 - App');
            $table->text('description')->nullable();
            $table->string('receipt_number', 191);
            $table->unsignedInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('CASCADE');
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
        Schema::dropIfExists('driver_agency_wallet_transactions');
    }
}
