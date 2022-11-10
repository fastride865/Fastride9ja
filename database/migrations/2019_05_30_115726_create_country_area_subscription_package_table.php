<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountryAreaSubscriptionPackageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('country_area_subscription_package', function(Blueprint $table)
		{
			$table->integer('country_area_id')->unsigned()->index('country_area_subscription_package_country_area_id_foreign');
			$table->integer('subscription_pack_id')->unsigned()->index('country_area_subscription_package_subscription_pack_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('country_area_subscription_package');
	}

}
