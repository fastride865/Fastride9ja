<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageQuestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_questions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id')->unsigned()->nullable();
			$table->integer('question_id')->unsigned()->nullable();
			$table->string('locale', 191);
			$table->text('question')->nullable();
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
		Schema::drop('language_questions');
	}

}
