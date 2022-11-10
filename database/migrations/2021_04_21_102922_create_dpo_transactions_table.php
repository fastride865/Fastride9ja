<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDpoTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dpo_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('merchant_id')->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('type')->nullable()->comment('1 - user, 2 - driver');
            $table->string('amount',191)->nullable();
            $table->string('transaction_token',191)->nullable();
            $table->tinyInteger('payment_status')->default(0)->nullable();
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
        Schema::dropIfExists('dpo_transactions');
    }
}
