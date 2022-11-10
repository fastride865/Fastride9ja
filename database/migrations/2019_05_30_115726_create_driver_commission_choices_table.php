<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverCommissionChoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_commission_choices', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('slug', 191)->comment('Subscription Type OR commission Type');
			$table->integer('status')->default(1)->comment('1: Enable 0:Disable');
			$table->integer('admin_delete')->default(0)->comment('1: Delete 0:Not Deleted');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_commission_choices');
	}

}
