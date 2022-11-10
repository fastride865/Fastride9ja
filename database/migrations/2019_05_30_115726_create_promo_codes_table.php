<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromoCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promo_codes', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('merchant_id');
            $table->integer('segment_id');
			$table->integer('country_area_id');
			$table->integer('corporate_id')->nullable();
			$table->string('promoCode', 191);
			$table->text('promo_code_description');
			$table->integer('promo_code_value');
			$table->integer('promo_code_value_type');
			$table->integer('promo_code_validity');
			$table->integer('promo_code_limit');
			$table->integer('promo_code_limit_per_user');
			$table->string('promo_percentage_maximum_discount', 20)->nullable();
			$table->string('order_minimum_amount', 20)->nullable()->comment("order's minimum amount to get coupon discount");
			$table->string('start_date', 191)->nullable();
			$table->string('end_date', 191)->nullable();
			$table->integer('applicable_for');
			$table->integer('promo_code_status')->default(1);
			$table->integer('deleted')->nullable();
			$table->integer('status')->default(1)->comment('1:Active, 2: Inactive 3: Expired');
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
		Schema::drop('promo_codes');
	}

}
