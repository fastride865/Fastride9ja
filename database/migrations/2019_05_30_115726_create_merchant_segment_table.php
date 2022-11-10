<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantSegmentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchant_segment', function(Blueprint $table)
		{
			$table->integer('merchant_id')->unsigned()->index('merchant_segment_merchant_id_foreign');
			$table->integer('segment_id')->unsigned()->index('merchant_segment_segment_id_foreign');
            // coming soon according to merchant wise
			$table->tinyInteger('is_coming_soon')->nullable()->default(2)->comment('1 Yes, 2 : No');
		    $table->string('segment_icon')->nullable();
            $table->tinyInteger('sequence')->nullable()->comment('order of segment');
            $table->tinyInteger('price_card_owner')->default(1)->comment('1 : Admin or Merchant, 2 : Driver');
            $table->tinyInteger('request_expire_time')->default(120)->comment('time in minute');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('merchant_segment');
	}

}
