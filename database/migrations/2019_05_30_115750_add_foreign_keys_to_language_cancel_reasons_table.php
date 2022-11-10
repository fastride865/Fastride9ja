<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLanguageCancelReasonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('language_cancel_reasons', function(Blueprint $table)
		{
			$table->foreign('cancel_reason_id')->references('id')->on('cancel_reasons')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('language_cancel_reasons', function(Blueprint $table)
		{
			$table->dropForeign('language_cancel_reasons_cancel_reason_id_foreign');
			$table->dropForeign('language_cancel_reasons_merchant_id_foreign');
		});
	}

}
