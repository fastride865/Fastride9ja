<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromoCodeServiceTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promo_code_service_type', function(Blueprint $table)
		{
			$table->integer('promo_code_id')->unsigned()->index('promo_code_service_type_promo_code_id_foreign');
			$table->integer('service_type_id')->unsigned()->index('promo_code_service_type_service_type_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('promo_code_service_type');
	}

}
