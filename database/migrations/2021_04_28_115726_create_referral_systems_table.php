<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReferralSystemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('referral_systems', function(Blueprint $table)
		{
			$table->increments('id');

            $table->unsignedInteger('merchant_id')->nullable();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('country_area_id')->nullable();
            $table->foreign('country_area_id')->references('id')->on('country_areas')->onUpdate('RESTRICT')->onDelete('CASCADE');

//            $table->unsignedInteger('segment_id')->nullable();
//            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('application')->comment("1-User,2-Driver");
//            $table->string('code_name')->unique()->nullable()->after('application');
//            $table->integer('default_code')->default(0)->after('code_name');
//            $table->integer('limit')->default(0)->comment('0 : Unlimited,1 : Limited')->after('default_code');
//			$table->string('fixed_value')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('offer_condition')->comment('0 : Unlimited,1 : Limited');
            $table->integer('offer_applicable')->comment('1: Sender, 2: Receiver, 3: Both');
            $table->integer('offer_type');
            $table->decimal('offer_value',10,2);
            $table->integer('maximum_offer_amount')->nullable();
            $table->longText("offer_condition_data")->nullable()->comment("Required json data for condition");

//            $table->string('no_of_limit')->nullable();
//            $table->string('no_of_day')->nullable();
//            $table->integer('day_count')->nullable()->comment('	1 : After SignUp, 2 : After First Ride	');

            $table->integer('status')->default(1)->comment("1-Active, 2-Inactive, 3-Expired,4-Deleted");
            $table->softDeletes();
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
		Schema::drop('referral_systems');
	}

}
