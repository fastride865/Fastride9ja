<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCountryAreaDocumentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('country_area_document', function(Blueprint $table)
		{
			$table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('document_id')->references('id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('country_area_document', function(Blueprint $table)
		{
			$table->dropForeign('country_area_document_country_area_id_foreign');
			$table->dropForeign('country_area_document_document_id_foreign');
		});
	}

}
