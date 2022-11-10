<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverSubscriptionRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_subscription_records', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('driver_id')->unsigned()->index('driver_subscription_records_driver_id_foreign');

			$table->integer('payment_method_id')->unsigned()->nullable()->index('driver_subscription_records_payment_method_id_foreign');
			$table->integer('subscription_pack_id')->unsigned()->index('driver_subscription_records_subscription_pack_id_foreign');
			$table->integer('package_duration_id')->unsigned()->index('driver_subscription_records_package_duration_id_foreign');
			$table->decimal('price');
			$table->integer('package_total_trips')->comment('Subscription pack total trips');
			$table->integer('used_trips')->default(0)->comment('Trips used by Driver yet');
			$table->dateTime('start_date_time')->nullable();
			$table->dateTime('end_date_time')->nullable();
			$table->tinyInteger('status')->comment('0 : Inactive,1:Assigned,2:Active,3:Expired,4:Carry forwarded to next package');
			$table->tinyInteger('package_type')->comment('1 : Free, 2: Paid');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_subscription_records');
	}

}
