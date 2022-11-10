<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToSosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sos', function (Blueprint $table) {
            //
            $table->integer('merchant_id')->unsigned()->nullable()->change();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

//	        $table->integer('user_id')->unsigned()->nullable()->change();
//            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
	    
	    $table->integer('country_id')->unsigned()->nullable()->change();
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sos', function (Blueprint $table) {
            //
            $table->dropForeign('merchant_id');
	    $table->dropForeign('user_id');
	    $table->dropForeign('country_id');
        });
    }
}
