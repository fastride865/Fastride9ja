<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceCardCommissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('price_card_commissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('price_card_id')->unique();
			$table->tinyInteger('commission_type')->nullable();
			$table->tinyInteger('commission_method')->nullable();
			$table->string('commission', 20)->nullable();
            $table->tinyInteger('taxi_commission_type')->nullable();
            $table->tinyInteger('taxi_commission_method')->nullable();
            $table->string('taxi_commission', 20)->nullable();
            $table->tinyInteger('hotel_commission_type')->nullable();
            $table->tinyInteger('hotel_commission_method')->nullable();
            $table->string('hotel_commission', 20)->nullable();
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
		Schema::drop('price_card_commissions');
	}

}
