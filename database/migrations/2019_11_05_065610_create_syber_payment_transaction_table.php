<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyberPaymentTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syber_payment_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id')->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('type')->nullable()->comment('1 - user, 2 - driver');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('driver_id')->nullable();
            $table->string('order_id',191)->nullable();
            $table->string('amount',191)->nullable();
            $table->string('payment_status',191)->nullable();
            $table->longText('api_response')->nullable();
            $table->longText('request_data')->nullable();
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
        Schema::dropIfExists('syber_payment_transaction');
    }
}
