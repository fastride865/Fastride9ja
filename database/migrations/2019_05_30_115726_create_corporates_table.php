<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCorporatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('corporates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->string('corporate_name', 191);
			$table->string('email', 191);
			$table->string('corporate_phone', 191);
			$table->string('corporate_address', 191);
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
		Schema::drop('corporates');
	}

}
