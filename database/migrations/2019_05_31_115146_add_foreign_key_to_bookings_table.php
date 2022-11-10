<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('merchant_id')->unsigned()->nullable()->change();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('segment_id')->unsigned()->nullable()->change();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('taxi_company_id')->unsigned()->nullable()->change();
            $table->foreign('taxi_company_id')->references('id')->on('taxi_companies')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('hotel_id')->unsigned()->nullable()->change();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('franchise_id')->unsigned()->nullable()->change();
            $table->foreign('franchise_id')->references('id')->on('franchisees')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('user_id')->unsigned()->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('driver_id')->unsigned()->nullable()->change();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('country_area_id')->unsigned()->nullable()->change();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('service_type_id')->unsigned()->nullable()->change();
            $table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('vehicle_type_id')->unsigned()->nullable()->change();
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('price_card_id')->unsigned()->nullable()->change();
            $table->foreign('price_card_id')->references('id')->on('price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');


            $table->integer('family_member_id')->unsigned()->nullable()->change();
            $table->foreign('family_member_id')->references('id')->on('family_members')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('cancel_reason_id')->unsigned()->nullable()->change();
            $table->foreign('cancel_reason_id')->references('id')->on('cancel_reasons')->onUpdate('RESTRICT')->onDelete('CASCADE');

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
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('merchant_id');
            $table->dropForeign('taxi_company_id');
            $table->dropForeign('hotel_id');
            $table->dropForeign('franchise_id');
            $table->dropForeign('user_id');
            $table->dropForeign('driver_id');
            $table->dropForeign('country_area_id');
            $table->dropForeign('service_type_id');
            $table->dropForeign('vehicle_type_id');
            $table->dropForeign('service_package_id');
            $table->dropForeign('price_card_id');
            $table->dropForeign('driver_vehicle_id');
            $table->dropForeign('family_member_id');
            $table->dropForeign('cancel_reason_id');
            $table->dropForeign('payment_method_id');
            $table->dropForeign('card_id');
        });
    }
}
