<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateApplicationThemesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('application_themes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('application_themes_merchant_id_foreign');
//			$table->string('primary_color_user', 191)->nullable();
//			$table->string('primary_color_driver', 191)->nullable();
//			$table->string('chat_button_color', 191)->nullable();
//			$table->string('chat_button_color_driver', 191)->nullable();
//			$table->string('share_button_color', 191)->nullable();
//			$table->string('share_button_color_driver', 191)->nullable();
//			$table->string('cancel_button_color', 191)->nullable();
//			$table->string('cancel_button_color_driver', 191)->nullable();
//			$table->string('call_button_color', 191)->nullable();
//			$table->string('call_button_color_driver', 191)->nullable();
//			$table->string('navigation_colour', 191)->nullable();
//			$table->string('navigation_style', 191)->nullable();
//			$table->string('default_config', 11)->nullable();

			// login page background image
			$table->string('login_background_image')->nullable();
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
		Schema::drop('application_themes');
	}

}
