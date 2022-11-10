<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDriverFranchiseeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('driver_franchisee', function(Blueprint $table)
		{
			$table->foreign('driver_id', 'franchisee_driver_driver_id_foreign')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('franchisee_id', 'franchisee_driver_franchisee_id_foreign')->references('id')->on('franchisees')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('driver_franchisee', function(Blueprint $table)
		{
			$table->dropForeign('franchisee_driver_driver_id_foreign');
			$table->dropForeign('franchisee_driver_franchisee_id_foreign');
		});
	}

}
