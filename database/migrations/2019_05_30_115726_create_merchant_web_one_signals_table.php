<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantWebOneSignalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_web_one_signals', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('merchant_id');
			$table->unsignedInteger('business_segment_id')->nullable();
			$table->string('player_id');
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
		Schema::drop('merchant_web_one_signals');
	}

}
