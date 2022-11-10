<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCashbackDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cashback_details', function(Blueprint $table)
		{
			$table->foreign('cashback_id')->references('id')->on('cashbacks')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('vehicle_type_id')->references('id')->on('vehicle_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cashback_details', function(Blueprint $table)
		{
			$table->dropForeign('cashback_details_cashback_id_foreign');
			$table->dropForeign('cashback_details_service_type_id_foreign');
			$table->dropForeign('cashback_details_vehicle_type_id_foreign');
		});
	}

}
