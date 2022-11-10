<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExtraChargesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('extra_charges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('price_card_id')->unsigned();
			$table->string('parameterName', 191);
			$table->string('slot_week_days', 191)->nullable();
			$table->string('slot_start_time', 191)->nullable();
			$table->string('slot_end_time', 191)->nullable();
			$table->string('slot_charges', 191)->nullable();
			$table->integer('slot_end_day')->unsigned()->nullable();
			$table->integer('slot_charge_type')->unsigned()->nullable();
			$table->integer('slot_status')->unsigned()->default(1);
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
		Schema::drop('extra_charges');
	}

}
