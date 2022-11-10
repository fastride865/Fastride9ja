<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferCommissionFareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('refer_commission_fare')) {
            Schema::create('refer_commission_fare', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('merchant_id');
                $table->string('name')->nullable();
                $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('CASCADE');
                $table->double('start_range');
                $table->double('end_range');
                $table->double('commission');
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
        Schema::dropIfExists('refer_commission_fare');
    }
}
