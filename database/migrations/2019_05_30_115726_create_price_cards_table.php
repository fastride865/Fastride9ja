<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceCardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('price_cards', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('country_area_id');
			$table->integer('service_type_id');
			$table->integer('vehicle_type_id');
			$table->integer('pricing_type');

            $table->integer('segment_id');

            $table->string('price_card_name', 191)->nullable();
            $table->integer('rate_card_scope')->nullable();
			$table->integer('outstation_max_distance')->nullable();
			$table->integer('outstation_type')->nullable()->comment('1 : Round Trip, 2 : One Way');
			$table->string('maximum_bill_amount', 191)->nullable();
			$table->integer('service_package_id')->nullable();
			$table->string('base_fare', 191)->nullable();
			$table->string('free_distance', 191)->nullable();
			$table->string('free_time', 191)->nullable();
			$table->string('extra_sheet_charge', 191)->nullable();
			$table->string('minimum_wallet_amount', 191)->nullable()->default('0.00')->comment('for user');
			$table->integer('cancel_charges')->nullable()->default(2);
			$table->integer('cancel_time')->nullable();
			$table->string('cancel_amount', 191)->nullable();
			$table->string('sub_charge_type')->nullable();
			$table->string('gst_tax_number', 191)->nullable();
			$table->string('sub_charge_value')->nullable();
			$table->integer('sub_charge_status')->nullable();
			$table->integer('status')->default(1);
//			$table->string('driver_cash_booking_limit', 25)->nullable();
			$table->integer('insurnce_enable')->nullable();
			$table->integer('insurnce_type')->nullable();
			$table->string('insurnce_value', 191)->nullable();
            $table->decimal('additional_mover_charge')->nullable();

			// these columns for food and grocery  segment
			$table->tinyInteger('price_card_for')->nullable()->default(1)->comment('1=>Driver, 2=>User');
			$table->string('pick_up_fee')->nullable()->comment('item pickup fee');
			$table->string('drop_off_fee')->nullable()->comment('item delivered fee');
			$table->string('tax')->nullable()->comment('tax on order value for food and grocery related segments');
			$table->json('time_charges_details')->nullable();
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
		Schema::drop('price_cards');
	}

}
