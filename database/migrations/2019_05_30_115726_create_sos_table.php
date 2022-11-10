<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->string('number', 191);
			$table->integer('sosStatus')->nullable()->default(1);
			$table->integer('application')->nullable();
			$table->integer('user_id')->nullable()->comment('user_id means application 1 for user and application 2 for driver');
			$table->integer('country_id')->nullable();
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
		Schema::drop('sos');
	}

}
