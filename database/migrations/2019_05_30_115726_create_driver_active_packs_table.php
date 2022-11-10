<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverActivePacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
//		Schema::create('driver_active_packs', function(Blueprint $table)
//		{
//			$table->increments('id');
//			$table->integer('driver_id')->unsigned()->index('driver_active_packs_driver_id_foreign');
//			$table->integer('payment_method_id')->unsigned()->nullable();
//			$table->integer('subscription_pack_id')->unsigned()->index('driver_active_packs_subscription_pack_id_foreign');
//			$table->integer('package_total_trips')->comment('Subscription pack total trips');
//			$table->integer('used_trips')->default(0)->comment('Trips used by Driver yet');
//			$table->string('start_date_time', 191);
//			$table->string('end_date_time', 191);
//			$table->timestamps();
//		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
//		Schema::drop('driver_active_packs');
	}

}
