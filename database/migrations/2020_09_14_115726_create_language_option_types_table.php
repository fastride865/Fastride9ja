<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageOptionTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_option_types', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('option_type_id')->unsigned();
            $table->foreign('option_type_id')->references('id')->on('option_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('locale', 10)->index();
			$table->string('type');
			$table->timestamps();

			$table->unique(['option_type_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_products');
	}

}
