<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_documents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_documents_merchant_id_foreign');
			$table->integer('document_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('documentname', 191);
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['document_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_documents');
	}

}
