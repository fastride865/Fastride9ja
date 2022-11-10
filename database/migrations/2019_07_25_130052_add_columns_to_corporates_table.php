<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCorporatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporates', function (Blueprint $table) {
            $table->unsignedInteger('country_id')->after('merchant_id');
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('restrict')->onDelete('cascade');
            $table->string('password')->after('email');
            $table->string('corporate_logo')->after('password');
            $table->string('remember_token')->nullable()->after('corporate_logo');
            $table->string('alias_name')->after('corporate_name');
            $table->string('status')->default(1)->after('corporate_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporates', function (Blueprint $table) {
            //
        });
    }
}
