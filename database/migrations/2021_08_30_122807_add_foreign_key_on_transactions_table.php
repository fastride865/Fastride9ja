<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyOnTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {

            $table->integer('user_id')->unsigned()->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');

//            $table->integer('card_id')->unsigned()->nullable()->change();
//            $table->foreign('card_id')->references('id')->on('cards')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('order_id')->unsigned()->nullable()->change();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('handyman_order_id')->unsigned()->nullable()->change();
            $table->foreign('handyman_order_id')->references('id')->on('handyman_orders')->onUpdate('RESTRICT')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
            $table->dropForeign('merchant_id');
        });
    }
}
