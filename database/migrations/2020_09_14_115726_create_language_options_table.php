<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_options', function(Blueprint $table)
		{
			$table->increments('id');

            $table->integer('business_segment_id')->unsigned();
            $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('option_id')->unsigned();
            $table->foreign('option_id')->references('id')->on('options')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('locale', 10)->index();
			$table->string('name');
			$table->timestamps();
			$table->softDeletes();
			$table->unique(['option_id','locale']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('language_options');
	}

}
