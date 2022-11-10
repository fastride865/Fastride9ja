<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('drivers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('merchant_driver_id', 191)->nullable();
			$table->integer('merchant_id');
			$table->tinyInteger('pay_mode')->default(2)->comment('1:Subscription Based 2:Commission Based');
			$table->integer('taxi_company_id')->nullable();
			$table->string('unique_number')->nullable();
			$table->integer('driver_gender')->nullable()->comment('1 for male and 2 for female');
			$table->string('first_name', 191);
			$table->string('last_name', 191)->nullable();
			$table->string('email', 191)->nullable();
			$table->string('password', 191);
//			$table->string('city')->nullable();
//			$table->string('postal_code')->nullable();
//			$table->string('driver_address')->nullable();
			$table->integer('home_location_active')->nullable()->default(2)->comment('1:Enable  2:Disable');
			$table->integer('pool_ride_active')->default(2);
			$table->integer('status_for_pool')->default(1);
			$table->integer('avail_seats')->nullable();
			$table->integer('occupied_seats')->nullable();
			$table->integer('pick_exceed')->nullable();
			$table->integer('pool_user_id')->nullable();
			$table->string('phoneNumber', 191);
			$table->string('profile_image')->default('');
			$table->string('wallet_money', 191)->nullable();
			$table->integer('total_trips')->nullable();
			$table->string('total_earnings', 191)->nullable();
			$table->string('total_comany_earning', 191)->nullable();
			$table->string('outstand_amount', 191)->nullable();
			$table->string('current_latitude', 191)->nullable();
			$table->string('current_longitude', 191)->nullable();
			$table->dateTime('last_location_update_time')->nullable();
			$table->string('bearing', 191)->nullable()->default('');
			$table->string('accuracy', 191)->nullable()->default('');
			$table->tinyInteger('device')->nullable()->comment('1:Android, 2:iOS ');
			$table->string('player_id', 191)->nullable();
            $table->decimal('rating', 10,1)->nullable();
			$table->integer('country_area_id');
			$table->integer('login_logout')->default(2);
			$table->integer('online_offline')->default(2);
			$table->integer('free_busy')->default(2);
			$table->string('bank_name', 191)->nullable();
			$table->string('account_holder_name', 191)->nullable();
			$table->string('account_number', 191)->nullable();
			$table->integer('account_type_id')->nullable()->comment('1:Saving 2:Current 3:Recurring Deposit Account 4:basic checking accounts');
			$table->integer('driver_verify_status')->default(1);
			$table->integer('signupFrom')->default(1);
			$table->integer('signupStep')->default(1);
			$table->dateTime('driver_verification_date')->nullable();
			$table->integer('driver_admin_status')->default(1);
			$table->string('access_token_id', 191)->nullable();
			$table->integer('driver_delete')->nullable();
			$table->string('online_code')->nullable();
			$table->dateTime('last_ride_request_timestamp')->nullable();
			$table->timestamps();
			$table->string('driver_referralcode')->nullable();
			$table->integer('driver_block_status')->nullable();
			$table->integer('term_status')->nullable()->default(0);
			$table->integer('pending_document_status')->nullable()->default(2);
			$table->text('admin_msg')->nullable();
			$table->string('fullName', 191)->nullable();
			$table->string('dob', 191)->nullable();
			$table->integer('reject_driver')->nullable()->default(1);
			$table->string('driver_cpf_number', 6555)->nullable();
			$table->string('agency_number')->nullable();
			$table->text('driver_additional_data')->nullable();
            $table->tinyInteger('app_debug_mode')->nullable()->default(2);

            $table->integer('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('RESTRICT')->onDelete('CASCADE');

            // socket changes
            $table->string('ats_id')->nullable()->comment('Apporio tracking system id for socket');

            // Driver language
            $table->string('language')->nullable()->default("en");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('drivers');
	}

}
