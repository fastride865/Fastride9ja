<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromotionNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promotion_notifications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('country_area_id')->nullable();
			$table->integer('application');
			$table->string('title', 191);
			$table->text('message');
			$table->string('image', 191)->nullable();
			$table->string('url', 191)->nullable();
			$table->integer('user_id')->nullable();
			$table->integer('driver_id')->nullable();
			$table->integer('show_promotion')->nullable()->default(2);
			$table->timestamp('expiry_date')->nullable();
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
		Schema::drop('promotion_notifications');
	}

}
