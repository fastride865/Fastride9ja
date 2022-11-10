<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFranchiseeUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('franchisee_user', function(Blueprint $table)
		{
			$table->foreign('franchisee_id')->references('id')->on('franchisees')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('franchisee_user', function(Blueprint $table)
		{
			$table->dropForeign('franchisee_user_franchisee_id_foreign');
			$table->dropForeign('franchisee_user_user_id_foreign');
		});
	}

}
