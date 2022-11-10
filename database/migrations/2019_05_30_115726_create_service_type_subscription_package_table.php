<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServiceTypeSubscriptionPackageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('service_type_subscription_package', function(Blueprint $table)
		{
			$table->integer('service_type_id')->unsigned()->index('service_type_subscription_package_service_type_id_foreign');
			$table->integer('subscription_pack_id')->unsigned()->index('service_type_subscription_package_subscription_pack_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('service_type_subscription_package');
	}

}
