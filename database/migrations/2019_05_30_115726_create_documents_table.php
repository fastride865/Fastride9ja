<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->default(0);
			$table->integer('expire_date')->nullable()->default(1)->comment('1: Expiry Date Disabled, 2: Expirey Date Enabled');
			$table->integer('documentStatus')->default(1);
			$table->integer('documentNeed')->nullable()->comment('1-mendatory,2-notmendatory');
			$table->tinyInteger('document_number_required')->default(2);
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
		Schema::drop('documents');
	}

}
