<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverVehiclesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_vehicles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->nullable();
			$table->integer('driver_id')->nullable();
			$table->integer('owner_id')->nullable();
			$table->integer('ownerType')->nullable()->default(1)->comment('1 own vehicle, 2: other person vehicle');
			$table->integer('vehicle_type_id');
			$table->string('shareCode', 191)->nullable();
			$table->integer('vehicle_make_id');
			$table->integer('vehicle_model_id');
			$table->string('vehicle_number', 191);
			$table->date('vehicle_register_date')->nullable();
			$table->date('vehicle_expire_date')->nullable();
			$table->string('vehicle_color', 191);
			$table->string('vehicle_image')->default('');
			$table->string('vehicle_number_plate_image')->default('');
			// no need of this column
            //$table->integer('vehicle_active_status')->default(2)->comment('1: Active,2: Deactive ');
			$table->integer('vehicle_verification_status')->default(2)->comment('1: Pending,2: Verified,3:rejected,4: Expired');
			$table->integer('reject_reason_id')->nullable();
			$table->integer('ac_nonac')->nullable();
			$table->integer('baby_seat')->nullable();
			$table->integer('wheel_chair')->nullable();
			$table->integer('vehicle_delete')->nullable();
			$table->timestamps();
			$table->integer('total_expire_document')->nullable();
			$table->text('vehicle_additional_data')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_vehicles');
	}

}
