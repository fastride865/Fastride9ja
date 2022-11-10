<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmsGatewaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sms_gateways', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name', 191);
			$table->text('params', 65535)->nullable();
			$table->string('description')->nullable();
			$table->string('status', 191)->default('1');
			$table->integer('environment')->nullable();
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
		Schema::drop('sms_gateways');
	}

}
