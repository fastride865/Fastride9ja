<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReferralDiscountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('referral_discounts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('referral_system_id');
            $table->foreign('referral_system_id')->references('id')->on('referral_systems')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('restrict')->onDelete('cascade');
            $table->integer('sender_id');
            $table->enum('sender_type',['USER','DRIVER']);
            $table->integer('receiver_id');
            $table->enum('receiver_type',['USER','DRIVER']);
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->integer('offer_condition')->comment('0 : Unlimited,1 : Limited');
            $table->integer('offer_applicable')->comment('1 : Sender,2 : Receiver,3 : Both');
            $table->integer('offer_type');
            $table->string('offer_value');
            $table->decimal('maximum_offer_amount',10,1)->nullable();
            $table->longText("offer_condition_data")->nullable()->comment("Required json data for condition");
            $table->longText("offer_condition_data_initial")->nullable()->comment("Required json data for condition");
            $table->integer('referral_available')->default(2)->comment('1 : Yes, 2 : No');
//            $table->integer('limit')->comment('0 : Unlimited,1 : Limited')->after('receiver_type');
//            $table->string('limit_usage')->nullable()->after('limit');
//            $table->string('no_of_day')->nullable()->after('limit_usage');
//            $table->integer('day_count')->nullable()->comment('1 : After SignUp, 2 : After First Ride')->after('no_of_day');
//            $table->string('sender_get_ride')->nullable()->after('offer_value');
//            $table->string('receiver_get_ride')->nullable()->after('sender_get_ride');
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
		Schema::drop('referral_discounts');
	}

}
