<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantServiceTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_service_type', function(Blueprint $table)
		{
			$table->integer('merchant_id')->unsigned()->index();
			$table->integer('segment_id')->unsigned()->index();
			$table->integer('service_type_id')->unsigned()->index();
            $table->string('service_icon')->nullable();
            $table->tinyInteger('sequence')->nullable()->comment('order of service type');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('merchant_service_type');
	}

}
