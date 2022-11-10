<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageCountryAreasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_country_areas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_country_areas_merchant_id_foreign');
			$table->integer('country_area_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('AreaName', 191);
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['country_area_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_country_areas');
	}

}
