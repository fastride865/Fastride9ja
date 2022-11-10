<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVehicleOwnerDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
//		Schema::create('vehicle_owner_details', function(Blueprint $table)
//		{
//			$table->integer('id', true);
//			$table->integer('driver_vehicle_id');
//			$table->string('owner_name', 191)->nullable();
//			$table->string('owner_phone', 191)->nullable();
//			$table->string('owner_email', 191)->nullable();
//			$table->string('owner_bank_name', 191)->nullable();
//			$table->string('owner_bank_code', 191)->nullable();
//			$table->integer('owner_account_number')->nullable();
//			$table->string('bank_cheque_image', 191)->nullable();
//			$table->text('owner_additional_details')->nullable();
//			$table->timestamps();
//		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vehicle_owner_details');
	}

}
