<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageCancelReasonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_cancel_reasons', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_cancel_reasons_merchant_id_foreign');
			$table->integer('cancel_reason_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('reason', 200);
			$table->text('description')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['cancel_reason_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_cancel_reasons');
	}

}
