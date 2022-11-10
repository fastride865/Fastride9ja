<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLangCashbacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lang_cashbacks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('lang_cashbacks_merchant_id_foreign');
			$table->integer('cashback_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->integer('type')->comment('1: User Message 2: Driver Message');
			$table->string('app_message');
			$table->timestamps();
			$table->unique(['cashback_id','locale','type']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lang_cashbacks');
	}

}
