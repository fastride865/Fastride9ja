<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDriverActivePacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
//		Schema::table('driver_active_packs', function(Blueprint $table)
//		{
//			$table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');
//			$table->foreign('payment_method_id')->references('id')->on('payment_methods')->onUpdate('RESTRICT')->onDelete('CASCADE');
//			$table->foreign('subscription_pack_id')->references('id')->on('subscription_packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
//		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
//		Schema::table('driver_active_packs', function(Blueprint $table)
//		{
//			$table->dropForeign('driver_active_packs_driver_id_foreign');
//			$table->dropForeign('driver_active_packs_payment_method_id_foreign');
//			$table->dropForeign('driver_active_packs_subscription_pack_id_foreign');
//		});
	}

}
