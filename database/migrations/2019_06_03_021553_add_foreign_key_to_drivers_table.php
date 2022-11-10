<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            //
            $table->integer('merchant_id')->unsigned()->nullable()->change();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('taxi_company_id')->unsigned()->nullable()->change();
            $table->foreign('taxi_company_id')->references('id')->on('taxi_companies')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('country_area_id')->unsigned()->nullable()->change();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('account_type_id')->unsigned()->nullable()->change();
            $table->foreign('account_type_id')->references('id')->on('account_types')->onUpdate('RESTRICT');

            $table->integer('segment_group_id')->unsigned()->nullable();
            $table->foreign('segment_group_id')->references('id')->on('segment_groups')->onUpdate('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            //
            $table->dropForeign('merchant_id');
            $table->dropForeign('taxi_company_id');
            $table->dropForeign('country_area_id');
            $table->dropForeign('accounty_type_id');
        });
    }
}
