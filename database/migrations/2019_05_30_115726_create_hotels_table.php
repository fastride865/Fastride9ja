<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHotelsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hotels', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('country_id');
			$table->string('alias', 191);
			$table->string('name', 191);
			$table->string('email', 191);
			$table->string('phone', 191);
			$table->string('password', 191);
			$table->string('address', 191);
			$table->string('remember_token', 100);
			$table->integer('status')->default(1);
            $table->string('bank_name', 191)->nullable();
            $table->string('account_holder_name', 191)->nullable();
            $table->string('account_number', 191)->nullable();
            $table->unsignedInteger('account_type_id')->nullable()->comment('1:Saving 2:Current 3:Recurring Deposit Account 4:basic checking accounts');
            $table->foreign('account_type_id')->references('id')->on('account_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->string('online_transaction')->nullable();
            $table->string('latitude', 191)->nullable();
            $table->string('longitude', 191)->nullable();
            $table->string('wallet_money')->nullable();
            $table->string('hotel_logo', 191)->nullable();
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
		Schema::drop('hotels');
	}

}
