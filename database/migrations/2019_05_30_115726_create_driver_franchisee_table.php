<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverFranchiseeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_franchisee', function(Blueprint $table)
		{
			$table->integer('franchisee_id')->unsigned()->index('franchisee_driver_franchisee_id_foreign');
			$table->integer('driver_id')->unsigned()->index('franchisee_driver_driver_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_franchisee');
	}

}
