<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantNavDrawersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_nav_drawers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('merchant_nav_drawers_merchant_id_foreign');
			$table->integer('app_navigation_drawer_id')->unsigned()->index('merchant_nav_drawers_app_navigation_drawer_id_foreign');
			$table->string('image', 200)->nullable();
			$table->integer('sequence')->default(1);
			$table->integer('status')->default(1)->comment('1: Active , 0: Deactive');
			$table->text('additional_data')->comment('Data like url and all');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('merchant_nav_drawers');
	}

}
