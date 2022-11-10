<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToDriverCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_cards', function (Blueprint $table) {
            //
            $table->foreign('payment_option_id')->references('id')->on('payment_options')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_cards', function (Blueprint $table) {
            //
            $table->dropForeign('payment_option_id');
            $table->dropForeign('driver_id');
        });
    }
}
