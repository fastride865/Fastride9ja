<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToMerchantNavDrawersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_nav_drawers', function (Blueprint $table) {
            //
            $table->integer('merchant_id')->unsigned()->change();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('app_navigation_drawer_id')->unsigned()->nullable()->change();
            $table->foreign('app_navigation_drawer_id')->references('id')->on('app_navigation_drawers')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_nav_drawers', function (Blueprint $table) {
            //
            $table->dropForeign('merchant_id');
            $table->dropForeign('app_navigation_drawer_id');
        });
    }
}
