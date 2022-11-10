<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLangSubscriptionPacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('lang_subscription_packs', function(Blueprint $table)
		{
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('subscription_package_id')->references('id')->on('subscription_packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('lang_subscription_packs', function(Blueprint $table)
		{
			$table->dropForeign('lang_subscription_packs_merchant_id_foreign');
			$table->dropForeign('lang_subscription_packs_subscription_package_id_foreign');
		});
	}

}
