<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageServicePackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_service_packages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_packages_merchant_id_foreign');
			$table->integer('service_package_id')->unsigned();
			$table->integer('service_type_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('name', 200);
			$table->text('description');
			$table->text('terms_conditions');
			$table->timestamps();
			$table->softDeletes();
			//$table->unique(['service_type_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_service_packages');
	}

}
