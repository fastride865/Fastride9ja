<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentMethodTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_method_translations', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id')->nullable();
			$table->integer('payment_method_id')->nullable();
			$table->string('name')->nullable();
			$table->string('locale')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('payment_method_translations');
	}

}
