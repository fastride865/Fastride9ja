<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSubscriptionPackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscription_packages', function(Blueprint $table)
		{
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('package_duration_id')->references('id')->on('package_durations')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subscription_packages', function(Blueprint $table)
		{
			$table->dropForeign('subscription_packages_merchant_id_foreign');
			$table->dropForeign('subscription_packages_package_duration_id_foreign');
		});
	}

}
