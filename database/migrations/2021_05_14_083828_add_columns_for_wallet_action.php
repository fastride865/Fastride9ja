<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsForWalletAction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_wallet_transactions', function (Blueprint $table) {
            //
            $table->unsignedInteger('action_merchant_id')->nullable();
            $table->foreign('action_merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });

        Schema::table('driver_wallet_transactions', function (Blueprint $table) {
            //
            $table->unsignedInteger('action_merchant_id')->nullable();
            $table->foreign('action_merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });

        Schema::table('business_segment_wallet_transactions', function (Blueprint $table) {
            //
            $table->unsignedInteger('action_merchant_id')->nullable();
            $table->foreign('action_merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
