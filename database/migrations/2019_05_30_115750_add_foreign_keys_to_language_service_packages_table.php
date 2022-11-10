<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLanguageServicePackagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('language_service_packages', function(Blueprint $table)
		{
			$table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('service_package_id')->references('id')->on('service_packages')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('service_type_id')->references('id')->on('service_types')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('language_service_packages', function(Blueprint $table)
		{
			$table->dropForeign('language_packages_merchant_id_foreign');
			$table->dropForeign('language_packages_package_id_foreign');
			$table->dropForeign('language_packages_service_type_id_foreign');
		});
	}

}
