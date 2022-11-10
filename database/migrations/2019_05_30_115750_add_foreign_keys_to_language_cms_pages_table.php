<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLanguageCmsPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('language_cms_pages', function(Blueprint $table)
		{
			$table->foreign('cms_page_id')->references('id')->on('cms_pages')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('language_cms_pages', function(Blueprint $table)
		{
			$table->dropForeign('language_cms_pages_cms_page_id_foreign');
			$table->dropForeign('language_cms_pages_merchant_id_foreign');
		});
	}

}
