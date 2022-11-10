<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromotionSmsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promotion_sms', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id')->nullable();
			$table->integer('country_area_id')->nullable();
			$table->integer('application')->nullable();
			$table->string('message', 6555)->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('driver_id')->nullable();
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
		Schema::drop('promotion_sms');
	}

}
