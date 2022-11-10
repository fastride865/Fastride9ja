<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConfigurationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('configurations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->nullable();
			$table->integer('website_module')->default(0)->comment('1:Enable 0:Disable');
			$table->integer('company_admin')->nullable()->default(0)->comment('1:Enable 0:Disable');
			$table->integer('driver_agency')->nullable()->default(0)->comment('1:Enable 0:Disable');
            $table->integer('geofence_module')->nullable()->default(0)->comment('1:Enable 0:Disable');
			$table->integer('sweet_alert_admin')->nullable()->default(0)->comment('1:Enable 0:Disable');
			$table->integer('cashback_module')->nullable()->default(0)->comment('1:Enable 0:Disable');
			$table->integer('user_wallet_status')->nullable();
			$table->integer('driver_wallet_status')->nullable();
			$table->string('report_issue_email', 191)->nullable();
			$table->string('report_issue_phone', 191)->nullable();
			$table->string('android_user_version', 191)->nullable();
			$table->string('android_user_mandatory_update', 191)->nullable();
			$table->string('android_user_maintenance_mode', 191)->nullable();
			$table->string('ios_user_version', 191)->nullable();
			$table->string('ios_user_mandatory_update', 191)->nullable();
			$table->string('ios_user_maintenance_mode', 191)->nullable();
			$table->string('android_driver_version', 191)->nullable();
			$table->string('android_driver_mandatory_update', 191)->nullable();
			$table->string('android_driver_maintenance_mode', 191)->nullable();
			$table->string('ios_driver_version', 191)->nullable();
			$table->string('ios_driver_mandatory_update', 191)->nullable();
			$table->string('ios_driver_maintenance_mode', 191)->nullable();
			$table->string('location_update_timeband', 191)->nullable();
			$table->text('user_wallet_amount')->nullable();
			$table->text('driver_wallet_amount')->nullable();
			$table->integer('email_functionality')->nullable()->default(0)->comment('1:Enable 0:Disable');
			$table->integer('sms_gateway')->nullable()->default(2);
			$table->integer('corporate_admin')->nullable()->default(0)->comment('1: Enable 0:Disable');
			$table->integer('toll_api')->nullable();
			$table->string('toll_key')->nullable();
			$table->integer('social_signup')->nullable();
			$table->integer('drop_outside_area')->nullable()->default(2);
			$table->integer('driver_area_notification')->nullable()->default(2);
			$table->integer('facebook')->nullable();
			$table->integer('google')->nullable();
			$table->integer('demo')->nullable();
			$table->integer('vehicle_ac_enable')->nullable();
			$table->integer('default_config')->nullable();
			$table->integer('homescreen_eta')->nullable()->default(2);
			$table->integer('homescreen_estimate_fare')->nullable()->default(2);
			$table->integer('driver_limit')->nullable()->default(2);
			$table->integer('driver_cash_limit')->nullable();
			$table->integer('outside_area_ratecard')->nullable()->default(2);
			$table->integer('subscription_package')->nullable()->comment('1:Enable 0:Disable');
			$table->integer('bank_details_enable')->nullable();
			$table->integer('existing_vehicle_enable')->nullable();
			$table->integer('no_of_person')->nullable();
			$table->integer('no_of_children')->nullable();
			$table->integer('no_of_bags')->nullable();
			$table->integer('add_multiple_vehicle')->nullable()->default(0)->comment('Driver Add Multiple Vehicle 1:Enable 2:Disable');
			$table->integer('cashback')->nullable();
			$table->integer('wallet_promo_code')->nullable();
			$table->integer('no_of_pool_seats')->nullable();
			$table->string('online_transaction_code')->nullable();
			$table->integer('trip_calculation_method')->nullable();
			$table->integer('family_member_enable')->nullable();
			$table->integer('no_driver_availabe_enable')->nullable();
			$table->integer('promotion_sms_enable')->nullable();
			$table->integer('home_screen')->default(2);
			$table->integer('gender')->nullable();
			$table->string('minimum_wallet_balance', 191)->nullable();
			$table->string('google_key', 191)->nullable();
			$table->string('distance', 191)->nullable();
			$table->integer('distance_ride_later')->nullable();
			$table->integer('number_of_driver')->nullable();
			$table->integer('ride_later_request')->nullable();
			$table->integer('ride_later_request_number_driver')->nullable();
			$table->string('ride_later_hours')->nullable();
			$table->integer('ride_later_time_before')->nullable();
			$table->integer('driver_request_timeout')->nullable();
			$table->integer('outstation_request_type')->nullable();
			$table->integer('outstation_time')->nullable();
			$table->integer('outstation_radius')->nullable();
			$table->integer('no_driver_outstation')->nullable();
			$table->string('outstation_time_before', 191)->nullable();
			$table->integer('pool_radius')->nullable();
			$table->integer('pool_drop_radius')->nullable();
			$table->integer('no_of_drivers')->nullable();
			$table->integer('maximum_exceed')->nullable();
			$table->integer('number_of_driver_user_map')->nullable();
			$table->string('tracking_screen_refresh_timeband', 191)->nullable();
			$table->string('default_language', 5)->comment('admin panel default language')->nullable(); // admin panel default language
			$table->integer('multi_destination')->nullable();
			$table->integer('count_multi_destination')->nullable();
			$table->integer('ride_otp')->nullable();
			$table->integer('chat')->nullable();
//			$table->integer('polyline')->nullable();
			$table->integer('ride_later_interval')->nullable();
			$table->integer('bus_booking_module')->nullable();
			$table->integer('demand_spot_enable')->nullable();
			$table->integer('manual_sms_otp_ride_end')->nullable();
			$table->integer('without_country_code_sos')->nullable();
			$table->integer('push_notification_provider')->nullable()->comment('1:signal 2: FCM');
			$table->tinyInteger('onride_waiting_button')->nullable()->default(0);
			$table->tinyInteger('user_login_with_otp')->nullable()->default(0);
            $table->tinyInteger('driver_login_with_otp')->nullable()->default(0);
            $table->tinyInteger('stripe_connect_enable')->nullable();
            $table->tinyInteger('paystack_split_payment_enable')->nullable();
			$table->tinyInteger('user_signup_card_store_enable')->nullable();
            $table->tinyInteger('user_outstanding_enable')->nullable();
//            $table->string('fare_policy_text')->nullable();
            $table->tinyInteger('driver_cashout_module')->nullable()->default(1); // by default
            $table->tinyInteger('lat_long_storing_at')->nullable()->default(1);
            $table->tinyInteger('time_format')->nullable()->default(2);
            $table->tinyInteger('user_time_charges')->nullable()->default(2);
            $table->tinyInteger('vehicle_model_expire')->nullable()->default(2);
            $table->tinyInteger('driver_add_wallet_money_enable')->nullable()->default(2);
            $table->tinyInteger('accept_mobile_number_without_zero')->nullable()->default(2)->comment("1 : enable 2:disable");
            $table->tinyInteger('skip_login')->nullable()->default(2)->comment("1 : enable 2:disable");
            $table->tinyInteger('transactions_view_enable')->nullable()->default(0)->comment("1 : enable 0:disable");
            $table->tinyInteger('instant_order')->nullable()->default(2)->comment("1 : enable 2:disable");// for grocery or grocery based segments
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
