<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLangSubscriptionPacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lang_subscription_packs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('lang_subscription_packs_merchant_id_foreign');
			$table->integer('subscription_package_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('name', 200);
			$table->string('description');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['subscription_package_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lang_subscription_packs');
	}

}
