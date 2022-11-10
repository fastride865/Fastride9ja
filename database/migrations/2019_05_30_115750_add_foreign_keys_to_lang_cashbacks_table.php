<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLangCashbacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('lang_cashbacks', function(Blueprint $table)
		{
			$table->foreign('cashback_id')->references('id')->on('cashbacks')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
		Schema::table('lang_cashbacks', function(Blueprint $table)
		{
			$table->dropForeign('lang_cashbacks_cashback_id_foreign');
			$table->dropForeign('lang_cashbacks_merchant_id_foreign');
		});
	}

}
