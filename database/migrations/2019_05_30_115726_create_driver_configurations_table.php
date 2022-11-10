<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverConfigurationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_configurations', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id')->nullable();
			$table->integer('bill_due_period')->nullable();
			$table->integer('bill_grace_period')->nullable();
			$table->string('fee_after_grace_period', 20)->nullable();
			$table->integer('auto_verify')->nullable()->default(0)->comment('1:Enable 0:Disable');
			$table->integer('inactive_time')->nullable()->default(15)->comment('last location update time');
            $table->string('driver_cashout_min_amount')->nullable();
            $table->tinyInteger('delivery_busy_driver_accept_ride')->default(2)->nullable();
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
		Schema::drop('driver_configurations');
	}

}
