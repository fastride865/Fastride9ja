<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApplicationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('applications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->string('ios_user_link', 191);
			$table->string('ios_driver_link', 191);
			$table->string('android_user_link', 191);
			$table->string('android_driver_link', 191);
			$table->string('ios_user_appid', 191)->nullable();
            $table->string('ios_driver_appid', 191)->nullable();
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
		Schema::drop('applications');
	}

}
