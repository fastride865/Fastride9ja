<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOutstationPackageTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('outstation_package_translations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('outstation_package_id');
			$table->integer('merchant_id');
			$table->integer('service_type_id');
			$table->string('city', 191);
			$table->string('locale', 191);
			$table->text('description');
			$table->text('terms_conditions')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('outstation_package_translations');
	}

}
