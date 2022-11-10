<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->integer('taxi_company_id')->unsigned()->nullable()->change();
            $table->foreign('taxi_company_id')->references('id')->on('taxi_companies')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('country_id')->unsigned()->nullable()->change();
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('country_area_id')->unsigned()->nullable()->change();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('corporate_id')->unsigned()->nullable()->change();
            $table->foreign('corporate_id')->references('id')->on('corporates')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('outstanding_booking_id')->unsigned()->nullable()->change();
            $table->foreign('outstanding_booking_id')->references('id')->on('bookings')->onUpdate('RESTRICT');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropForeign('taxi_company_id');
            $table->dropForeign('country_id');
            $table->dropForeign('country_area_id');
            $table->dropForeign('outstanding_booking_id');
        });
    }
}
