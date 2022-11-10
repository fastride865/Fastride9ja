<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppNavigationDrawersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('app_navigation_drawers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('image', 200)->nullable()->index();
			$table->integer('type')->default(1)->comment('1: For User , 2: For Driver');
			$table->integer('status')->default(1);
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
		Schema::drop('app_navigation_drawers');
	}

}
