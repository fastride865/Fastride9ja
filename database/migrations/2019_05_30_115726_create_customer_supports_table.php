<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerSupportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_supports', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id');
			$table->integer('application');
			$table->string('name', 191);
			$table->string('email', 191);
			$table->string('phone', 191);
			$table->text('query');
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
		Schema::drop('customer_supports');
	}

}
