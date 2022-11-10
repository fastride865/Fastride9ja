<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('booking_id')->unsigned();
			$table->string('start_meter_image', 191)->nullable();
			$table->string('start_meter_value', 191)->nullable();
			$table->string('end_meter_value', 191)->nullable();
			$table->string('end_meter_image', 191)->nullable();
			$table->string('accept_timestamp', 191);
			$table->string('accept_latitude', 191)->nullable();
			$table->string('accept_longitude', 191)->nullable();
			$table->string('accuracy_at_accept', 191)->nullable();
			$table->string('arrive_timestamp', 191)->nullable();
			$table->string('arrive_latitude', 191)->nullable();
			$table->string('arrive_longitude', 191)->nullable();
			$table->string('accuracy_at_arrive', 191)->nullable();
			$table->string('start_timestamp', 191)->nullable();
			$table->string('start_latitude', 191)->nullable();
			$table->string('start_longitude', 191)->nullable();
			$table->string('start_location', 191)->nullable();
			$table->string('accuracy_at_start', 191)->nullable();
			$table->string('dead_milage_distance')->nullable();
			$table->string('end_timestamp')->default('');
			$table->string('end_latitude')->default('');
			$table->string('end_longitude')->default('');
			$table->string('end_location', 191)->nullable();
			$table->string('accuracy_at_end')->default('');
			$table->string('wait_time', 191)->nullable();
			$table->text('bill_details')->nullable();
			$table->string('total_amount', 191)->default('0.00');
			$table->string('promo_discount', 191)->default('0.00');
			$table->string('wallet_deduct_money', 191)->nullable()->default('0.00');
			$table->string('pending_amount', 191)->nullable()->default('0.00');
			$table->string('tip_amount', 191)->nullable();
			$table->integer('payment_failure')->nullable()->comment('1 for  payment success, 2 payment failed');
			$table->text('product_loaded_images')->nullable();
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
		Schema::drop('booking_details');
	}

}
