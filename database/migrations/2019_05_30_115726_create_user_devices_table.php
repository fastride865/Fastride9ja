<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_devices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable();
			$table->string('unique_number', 191);
			$table->string('apk_version', 191);
			$table->string('language_code', 191);
			$table->string('player_id', 191)->nullable();
			$table->string('manufacture', 191)->nullable();
			$table->string('model', 191)->nullable();
			$table->string('device', 191);
			$table->string('operating_system', 191)->nullable();
			$table->string('package_name', 191)->nullable();
			$table->string('socket_id', 191)->nullable();
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
		Schema::drop('user_devices');
	}

}
