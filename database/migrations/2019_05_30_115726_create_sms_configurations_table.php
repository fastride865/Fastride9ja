<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmsConfigurationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sms_configurations', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('sms_provider', 191);
			$table->string('api_key', 191)->nullable();
			$table->string('api_secret_key', 191)->nullable();
			$table->string('subacct', 50)->nullable();
			$table->string('sender', 191)->nullable();
			$table->string('sender_number', 191)->nullable();
			$table->string('account_id', 191)->nullable();
			$table->string('auth_token', 191)->nullable();
			$table->longText('usermessage')->nullable();
			$table->longText('drivermessage')->nullable();
			$table->integer('smsgateway_id')->nullable();
			$table->integer('environment')->nullable();
			$table->integer('ride_book_enable')->nullable();
			$table->string('ride_book_msg', 255)->nullable();
			$table->integer('ride_accept_enable')->nullable();
			$table->string('ride_accept_msg',255)->nullable();
			$table->integer('ride_start_enable')->nullable();
			$table->string('ride_start_msg',255)->nullable();
			$table->integer('ride_end_enable')->nullable();
			$table->string('ride_end_msg', 255)->nullable();
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
		Schema::drop('sms_configurations');
	}

}
