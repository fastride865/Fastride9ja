<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_addresses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('driver_id');
			$table->integer('segment_id');
			$table->string('address_name', 191);
			$table->string('location', 191);
			$table->string('latitude', 191);
			$table->string('longitude', 191);
			$table->tinyInteger('address_type')->nullable()->comment("1:WORKSHOP_ADDRESS","2:HOME_ADDRESS");
            $table->integer('radius')->nullable()->comment("radius from this store address");
			$table->tinyInteger('address_status');
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
		Schema::drop('driver_addresses');
	}

}
