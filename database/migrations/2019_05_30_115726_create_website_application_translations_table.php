<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebsiteApplicationTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('website_application_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('website_application_feature_id');
			$table->string('title', 191);
			$table->string('description', 191);
			$table->string('locale', 191);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('website_application_translations');
	}

}
