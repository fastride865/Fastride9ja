<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverCardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_cards', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('driver_id')->unsigned();
			$table->integer('payment_option_id')->unsigned()->nullable();
			$table->string('token', 191);
			$table->string('card_number', 191)->nullable();
			$table->string('expiry_date', 191)->nullable();
			$table->string('exp_month', 10)->nullable();
			$table->string('exp_year', 10)->nullable();
			$table->string('card_type', 20)->nullable();
            $table->tinyInteger('card_delete')->nullable();
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
		Schema::drop('driver_cards');
	}

}
