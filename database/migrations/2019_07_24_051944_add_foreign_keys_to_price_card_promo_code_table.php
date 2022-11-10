<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToPriceCardPromoCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_card_promo_code', function (Blueprint $table) {
            //
            $table->integer('price_card_id')->unsigned()->change();
            $table->foreign('price_card_id')->references('id')->on('price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('promo_code_id')->unsigned()->change();
            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_card_promo_code', function (Blueprint $table) {
            //
            $table->dropForeign('price_card_id');
            $table->dropForeign('promo_code_id');
        });
    }
}
