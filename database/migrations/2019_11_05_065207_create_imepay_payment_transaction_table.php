<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImepayPaymentTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imepay_payment_transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token_id',191)->nullable();
            $table->string('amount',191)->nullable();
            $table->string('reference_id',191)->nullable();
            $table->string('merchant_code',191)->nullable();
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
        Schema::dropIfExists('imepay_payment_transaction');
    }
}
