<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebsiteFeatureTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('website_feature_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('website_feature_id');
			$table->string('title', 191);
			$table->text('description');
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
		Schema::drop('website_feature_translations');
	}

}
