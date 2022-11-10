<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLangNamesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lang_names', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned()->index('lang_names_merchant_id_foreign');
			$table->string('dependable_type', 191)->comment('Model Name');
			$table->bigInteger('dependable_id')->unsigned()->comment('Model id');
			$table->string('locale', 191)->index();
			$table->string('name', 200);
			$table->string('field_one')->nullable()->comment('save data acc to need');
			$table->string('field_two')->nullable()->comment('save data acc to need');
			$table->text('field_three')->nullable()->comment('save data acc to need');
			$table->timestamps();
			$table->softDeletes();
			$table->index(['dependable_type','dependable_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lang_names');
	}

}
