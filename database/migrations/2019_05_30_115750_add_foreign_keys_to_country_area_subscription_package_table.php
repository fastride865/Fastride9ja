<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCountryAreaSubscriptionPackageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('country_area_subscription_package', function(Blueprint $table)
		{
			$table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('subscription_pack_id')->references('id')->on('subscription_packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('country_area_subscription_package', function(Blueprint $table)
		{
			$table->dropForeign('country_area_subscription_package_country_area_id_foreign');
			$table->dropForeign('country_area_subscription_package_subscription_pack_id_foreign');
		});
	}

}
