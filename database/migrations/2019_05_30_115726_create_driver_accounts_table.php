<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_accounts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('driver_id');
			$table->timestamp('from_date')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->dateTime('to_date');
			$table->string('amount', 191);
			$table->integer('create_by');
			$table->integer('settle_by')->nullable();
			$table->dateTime('settle_date')->nullable();
			$table->integer('settle_type')->nullable();
			$table->string('referance_number', 191)->nullable();
			$table->integer('total_trips')->nullable();
			$table->integer('status')->default(1);
			$table->string('block_date', 191)->nullable();
			$table->string('due_date', 191)->nullable();
			$table->string('fee_after_grace_period', 20)->nullable();
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
		Schema::drop('driver_accounts');
	}

}
