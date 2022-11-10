<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_templates', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id');
			$table->string('template_name', 191);
			$table->string('logo')->nullable();
			$table->string('image')->nullable();
			$table->string('heading')->nullable();
			$table->string('subheading')->nullable();
			$table->longText('message')->nullable();
			$table->timestamps();
			$table->string('event', 191)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('email_templates');
	}

}
