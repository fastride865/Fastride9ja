<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageSosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_sos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('language_sos_merchant_id_foreign');
			$table->integer('sos_id')->unsigned();
			$table->string('locale', 191)->index();
			$table->string('name', 200);
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['sos_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_sos');
	}

}
