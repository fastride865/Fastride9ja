<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHandymanConfigurationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('handyman_configurations', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->tinyInteger('price_card_owner_config')->default(1)->nullable()->comment("means  if its value is admin then merchant's all segments price card owner will be admin and same for driver price card owner" );
            $table->tinyInteger('advance_payment_of_min_bill')->nullable();
            $table->tinyInteger('additional_charges_on_booking')->nullable()->comment("1:Yes, 2 No");
            $table->string('price_type_config',10)->nullable()->default("BOTH"); // 3 both 1 fixed //2 hourly
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
		Schema::drop('configurations');
	}

}
