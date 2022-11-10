<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDriverSubscriptionRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('driver_subscription_records', function(Blueprint $table)
		{
			$table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('package_duration_id')->references('id')->on('package_durations')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('payment_method_id')->references('id')->on('payment_methods')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
		Schema::table('driver_subscription_records', function(Blueprint $table)
		{
			$table->dropForeign('driver_subscription_records_driver_id_foreign');
			$table->dropForeign('driver_subscription_records_package_duration_id_foreign');
			$table->dropForeign('driver_subscription_records_payment_method_id_foreign');
			$table->dropForeign('driver_subscription_records_subscription_pack_id_foreign');
		});
	}

}
