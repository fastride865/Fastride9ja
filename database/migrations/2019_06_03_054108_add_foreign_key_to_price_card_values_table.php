<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToPriceCardValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_card_values', function (Blueprint $table) {
            //
            $table->integer('price_card_id')->unsigned()->nullable()->change();
            $table->foreign('price_card_id')->references('id')->on('price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('pricing_parameter_id')->unsigned()->nullable()->change();
            $table->foreign('pricing_parameter_id')->references('id')->on('pricing_parameters')->onUpdate('RESTRICT')->onDelete('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_card_values', function (Blueprint $table) {
            //
            $table->dropForeign('price_card_id');
            $table->dropForeign('pricing_parameter_id');
        });
    }
}
