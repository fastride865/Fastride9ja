<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessSegmentWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_segment_wallet_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('business_segment_id');
            $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->text('narration')->comment('transaction details text');
            $table->integer('transaction_type')->comment('1-Credit, 2-Debit');
            $table->string('payment_method', 191)->comment('1-Cash,2-NonCash,3-Cashback');
            $table->string('amount', 191);
            $table->integer('platform')->comment('1-Admin,2-Application,3-Web');
            $table->text('description')->nullable();
            $table->string('receipt_number', 191);
            $table->string('transaction_id', 191)->nullable();

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
        Schema::dropIfExists('business_segment_wallet_transactions');
    }
}
