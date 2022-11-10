<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('states', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('states_merchant_id_foreign');
			$table->integer('country_id')->unsigned()->index('states_country_id_foreign');
			$table->integer('status')->default(1)->comment('1: Enable 0:Disable');
			$table->integer('admin_delete')->nullable()->default(0)->comment('1:Deleted 0:Not Deleted');
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
		Schema::drop('states');
	}

}
