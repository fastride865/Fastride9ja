<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLangAppNavDrawersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lang_app_nav_drawers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned();
			$table->integer('merchant_nav_drawer_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('name', 200);
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['merchant_nav_drawer_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lang_app_nav_drawers');
	}

}
