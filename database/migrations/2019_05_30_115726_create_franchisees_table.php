<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFranchiseesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('franchisees', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('country_area_id');
			$table->string('alias', 191);
			$table->string('name', 191);
			$table->string('contact_person_name', 191);
			$table->string('email', 191);
			$table->string('phone', 191);
			$table->string('password', 191);
			$table->string('commission_percentage', 191);
			$table->integer('status')->default(1);
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
		Schema::drop('franchisees');
	}

}
