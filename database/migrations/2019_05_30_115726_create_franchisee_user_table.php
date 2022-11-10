<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFranchiseeUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('franchisee_user', function(Blueprint $table)
		{
			$table->integer('franchisee_id')->unsigned()->index('franchisee_user_franchisee_id_foreign');
			$table->integer('user_id')->unsigned()->index('franchisee_user_user_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('franchisee_user');
	}

}
