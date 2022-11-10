<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebSiteHomePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('web_site_home_pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
            $table->string('logo')->nullable();
			$table->string('user_banner_image', 255)->nullable();
			$table->string('driver_banner_image', 255)->nullable();
			$table->string('driver_footer_image', 255)->nullable();
			$table->string('footer_bgcolor', 255)->nullable();
			$table->string('footer_text_color', 255)->nullable();
            $table->text('user_book_form_config')->nullable();
            $table->text('user_estimate_container')->nullable();
            $table->text('android_link')->nullable();
            $table->text('ios_link')->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('web_site_home_pages');
	}

}
