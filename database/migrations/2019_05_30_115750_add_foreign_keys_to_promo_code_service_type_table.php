<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPromoCodeServiceTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('promo_code_service_type', function(Blueprint $table)
		{
			$table->foreign('promo_code_id')->references('id')->on('promo_codes')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('promo_code_service_type', function(Blueprint $table)
		{
			$table->dropForeign('promo_code_service_type_promo_code_id_foreign');
			$table->dropForeign('promo_code_service_type_service_type_id_foreign');
		});
	}

}
