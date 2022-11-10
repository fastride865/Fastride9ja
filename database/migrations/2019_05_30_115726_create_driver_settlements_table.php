<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverSettlementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_settlements', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('driver_id')->unsigned()->index('driver_settlements_driver_id_foreign');
			$table->string('booking_slot', 191)->nullable();
			$table->string('bill_from', 191);
			$table->string('bill_to', 191);
			$table->integer('total_trips');
			$table->string('total_trip_amount', 191);
			$table->string('company_cut', 191);
			$table->string('driver_cut', 191);
			$table->string('cash_collect', 191);
			$table->string('final_outstanding', 191);
			$table->integer('bill_method_type');
			$table->string('timezone', 191)->nullable();
			$table->string('settle_type')->nullable();
			$table->string('referance_number')->nullable();
			$table->integer('status')->nullable()->default(1);
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
		Schema::drop('driver_settlements');
	}

}
