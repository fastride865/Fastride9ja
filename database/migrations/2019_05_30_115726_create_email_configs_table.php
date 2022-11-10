<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailConfigsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_configs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id');
			$table->string('driver', 191)->nullable();
			$table->string('host', 191);
			$table->integer('port');
			$table->string('username');
			$table->string('password');
			$table->string('encryption', 191);
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
		Schema::drop('email_configs');
	}

}
