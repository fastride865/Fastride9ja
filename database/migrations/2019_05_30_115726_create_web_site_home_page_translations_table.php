<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebSiteHomePageTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('web_site_home_page_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('web_site_home_page_id');
			$table->string('locale', 191);
			$table->string('start_address_hint', 191);
			$table->string('end_address_hint', 191);
			$table->string('book_btn_title', 191);
			$table->string('estimate_btn_title', 191);
			$table->string('estimate_description', 191);
			$table->string('driver_heading', 191)->nullable();
			$table->text('driver_sub_heading')->nullable();
			$table->string('driver_buttonText', 191)->nullable();
			$table->string('footer_heading', 191)->nullable();
			$table->text('footer_sub_heading')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('web_site_home_page_translations');
	}

}
