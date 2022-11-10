<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplicationMerchantStringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('application_merchant_strings')){
            Schema::create('application_merchant_strings', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id');
                $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
                $table->unsignedInteger('application_string_id');
                $table->foreign('application_string_id')->references('id')->on('application_strings')->onDelete('cascade');
                $table->string('string_value');
                $table->string('locale');
                $table->string('version');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_merchant_strings');
    }
}
