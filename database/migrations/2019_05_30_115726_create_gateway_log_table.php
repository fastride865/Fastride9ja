<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGatewayLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gateway_log', function(Blueprint $table)
		{
			$table->integer('ID', true);
			$table->string('userID', 191)->nullable();
			$table->string('passwrd', 191)->nullable();
			$table->string('token', 191)->nullable();
			$table->string('remember', 191)->nullable();
			$table->dateTime('entrytimestamp')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gateway_log');
	}

}
