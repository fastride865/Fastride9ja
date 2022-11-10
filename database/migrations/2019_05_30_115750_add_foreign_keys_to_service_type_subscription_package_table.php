<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToServiceTypeSubscriptionPackageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('service_type_subscription_package', function(Blueprint $table)
		{
			$table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
		Schema::table('service_type_subscription_package', function(Blueprint $table)
		{
			$table->dropForeign('service_type_subscription_package_service_type_id_foreign');
			$table->dropForeign('service_type_subscription_package_subscription_pack_id_foreign');
		});
	}

}
