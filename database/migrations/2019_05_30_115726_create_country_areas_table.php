<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountryAreasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('country_areas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned();
			$table->integer('country_id')->unsigned();
			$table->tinyInteger('is_geofence')->default(2)->comment('1 - Geofence Area, 2 - Service Area');
			$table->integer('auto_upgradetion')->default(2);
			$table->string('timezone')->nullable();
			$table->string('minimum_wallet_amount', 191)->nullable()->comment('for driver');
			$table->integer('pool_postion')->nullable()->default(1);
			$table->integer('status')->nullable()->default(1);
			$table->integer('driver_earning_duration')->nullable()->default(1);
			$table->string('manual_toll_price')->nullable();

//            $table->integer('bill_period_id')->unsigned()->nullable();
//            $table->foreign('bill_period_id')->references('id')->on('bill_periods')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('bill_period_start')->nullable();
            $table->longText('AreaCoordinates');
            $table->integer('driver_cash_limit_amount')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('country_areas');
	}

}
