<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserCardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_cards', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('card_holder', 191)->nullable();
			$table->string('token', 191);
			$table->integer('payment_option_id')->unsigned();
			$table->string('card_number', 16)->nullable();
			$table->string('card_type', 191)->nullable();
			$table->string('expiry_date', 191)->nullable();
			$table->integer('exp_month')->nullable();
			$table->integer('exp_year')->nullable();
            $table->string('user_token', 191)->nullable();
            $table->tinyInteger('status');
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
		Schema::drop('user_cards');
	}

}
