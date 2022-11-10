<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCashbacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cashbacks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('cashbacks_merchant_id_foreign');
			$table->integer('country_area_id')->unsigned()->index('cashbacks_country_area_id_foreign');
			$table->integer('status')->default(1)->comment('1: Enable 0:Disable');
			$table->integer('admin_delete')->default(0)->comment('1: Delete 0:Not Deleted');
			$table->decimal('min_bill_amount');
			$table->integer('users_cashback_enable')->nullable()->default(0);
			$table->integer('drivers_cashback_enable')->nullable()->default(0);
			$table->string('users_percentage', 40)->nullable();
			$table->string('users_upto_amount', 40)->nullable();
			$table->integer('users_max')->nullable();
			$table->string('drivers_percentage', 40)->nullable();
			$table->string('drivers_upto_amount', 40)->nullable();
			$table->integer('drivers_max')->nullable();
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
		Schema::drop('cashbacks');
	}

}
