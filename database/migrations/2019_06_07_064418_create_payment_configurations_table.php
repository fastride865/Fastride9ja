<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           if(!Schema::hasTable('payment_configurations')) {
            Schema::create('payment_configurations', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id');
                $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('CASCADE');
                $table->unsignedTinyInteger('outstanding_payment_to')->default(1);
                $table->unsignedTinyInteger('fare_table_based_refer')->nullable();
                $table->unsignedTinyInteger('fare_table_refer_type')->nullable();
                $table->unsignedInteger('fare_table_refer_pass_value')->nullable();
                $table->unsignedTinyInteger('wallet_withdrawal_enable')->nullable();
                $table->double('wallet_withdrawal_min_amount')->default(0);
                $table->unsignedTinyInteger('cancel_rate_table_enable')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_configurations');
    }
}
