<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageCmsPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_cms_pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_cms_pages_merchant_id_foreign');
			$table->integer('cms_page_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('title', 191);
			$table->longText('description');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['cms_page_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_cms_pages');
	}

}
