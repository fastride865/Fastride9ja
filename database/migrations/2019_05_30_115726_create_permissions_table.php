<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permissions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parent_id');
			$table->string('name', 191);
			$table->integer('special_permission')->default(0);
			$table->string('display_name', 191);
			$table->string('guard_name', 191);
			$table->tinyInteger('permission_type')->nullable()->comment("1-common,2-segment based");
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
		Schema::drop('permissions');
	}

}
