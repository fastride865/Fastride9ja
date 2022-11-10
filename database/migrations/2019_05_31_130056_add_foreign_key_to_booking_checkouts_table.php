<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToBookingCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_checkouts', function (Blueprint $table) {
            //
            $table->integer('merchant_id')->unsigned()->nullable()->change();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('segment_id')->unsigned()->nullable()->change();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('user_id')->unsigned()->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('country_area_id')->unsigned()->nullable()->change();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('service_type_id')->unsigned()->nullable()->change();
            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('vehicle_type_id')->unsigned()->nullable()->change();
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('price_card_id')->unsigned()->nullable()->change();
            $table->foreign('price_card_id')->references('id')->on('price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('payment_method_id')->unsigned()->nullable()->change();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('card_id')->unsigned()->nullable()->change();
            $table->foreign('card_id')->references('id')->on('user_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_checkouts', function (Blueprint $table) {
            $table->dropForeign('merchant_id');
            $table->dropForeign('user_id');
            $table->dropForeign('country_area_id');
            $table->dropForeign('service_type_id');
            $table->dropForeign('vehicle_type_id');
            $table->dropForeign('service_package_id');
            $table->dropForeign('price_card_id');
            $table->dropForeign('payment_method_id');
            $table->dropForeign('card_id');
        });
    }
}
