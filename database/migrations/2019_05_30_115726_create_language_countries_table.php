<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_countries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_countries_merchant_id_foreign');
			$table->integer('country_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('name', 200);
			$table->string('currency', 191)->nullable();
			$table->string('parameter_name')->nullable();
			$table->string('placeholder')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['country_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_countries');
	}

}
