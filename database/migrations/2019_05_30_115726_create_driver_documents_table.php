<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_documents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('driver_id');
			$table->integer('document_id');
			$table->string('document_file', 191)->nullable();
			$table->date('expire_date')->nullable();
			$table->tinyInteger('document_verification_status');
			$table->integer('reject_reason_id')->nullable();
			$table->tinyInteger('status')->default(1)->comment('1:Document Active, 2: Document Removed from area');
			$table->string('document_number',191)->nullable();
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
		Schema::drop('driver_documents');
	}

}
