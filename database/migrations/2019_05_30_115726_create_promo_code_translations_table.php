<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromoCodeTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promo_code_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('promo_code_id');
			$table->string('promo_code_name', 191);
			$table->string('locale', 10);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('promo_code_translations');
	}

}
