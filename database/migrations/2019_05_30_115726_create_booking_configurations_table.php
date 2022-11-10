<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingConfigurationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_configurations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('auto_cancel_expired_rides')->nullable()->default(0)->comment('1:Enable 0:Disable');
			$table->string('google_key', 50)->nullable();
            $table->string('google_key_admin',50)->nullable();
			$table->integer('additional_note')->nullable();
			$table->integer('ride_otp')->nullable();
			$table->integer('otp_manual_dispatch')->nullable()->default(2);
			$table->integer('chat')->nullable();
			$table->integer('polyline')->nullable();
			$table->integer('driver_manual_dispatch')->nullable();
			$table->integer('multi_destination')->nullable();
			$table->integer('count_multi_destination')->nullable();
			$table->integer('driver_request_timeout')->nullable();
            $table->integer('user_request_timeout')->nullable();
			$table->integer('tracking_screen_refresh_timeband')->nullable();
			$table->string('normal_ride_now_radius', 191)->nullable();
			$table->string('normal_ride_now_request_driver', 191)->nullable();
			$table->integer('normal_ride_now_drop_location')->nullable();
			$table->string('normal_ride_later_request_type', 191)->nullable();
			$table->string('normal_ride_later_radius', 191)->nullable();
			$table->string('normal_ride_later_request_driver', 191)->nullable();
			$table->string('normal_ride_later_booking_hours', 191)->nullable();
			$table->integer('normal_ride_later_drop_location')->nullable();
			$table->integer('normal_ride_later_time_before')->nullable();
			$table->string('normal_ride_later_cron_hour', 191)->nullable();
			$table->string('rental_ride_now_radius', 191)->nullable();
			$table->string('rental_ride_now_request_driver', 191)->nullable();
			$table->integer('rental_ride_now_drop_location')->nullable();
			$table->string('rental_ride_later_request_type', 191)->nullable();
			$table->string('rental_ride_later_radius', 191)->nullable();
			$table->string('rental_ride_later_request_driver', 191)->nullable();
			$table->string('rental_ride_later_booking_hours', 191)->nullable();
			$table->integer('rental_ride_later_drop_location')->nullable();
			$table->integer('rental_ride_later_time_before')->nullable();
			$table->string('rental_ride_later_cron_hour', 191)->nullable();
			$table->string('transfer_ride_now_radius', 191)->nullable();
			$table->string('transfer_ride_now_request_driver', 191)->nullable();
			$table->integer('transfer_ride_now_drop_location')->nullable();
			$table->string('transfer_ride_later_request_type', 191)->nullable();
			$table->string('transfer_ride_later_radius', 191)->nullable();
			$table->string('transfer_ride_later_request_driver', 191)->nullable();
			$table->string('transfer_ride_later_booking_hours', 191)->nullable();
			$table->integer('transfer_ride_later_drop_location')->nullable();
			$table->integer('transfer_ride_later_time_before')->nullable();
			$table->string('transfer_ride_later_cron_hour', 191)->nullable();
			$table->string('pool_radius', 191)->nullable();
			$table->string('pool_drop_radius', 191)->nullable();
			$table->string('pool_now_request_driver', 191)->nullable();
			$table->integer('pool_maximum_exceed')->nullable();
			$table->string('outstation_request_type', 191)->nullable();
			$table->string('outstation_radius', 191)->nullable();
			$table->string('outstation_request_driver', 191)->nullable();
			$table->string('outstation_booking_hours', 191)->nullable();
			$table->integer('outstation_time_before')->nullable();
			$table->integer('slide_button')->nullable();
			$table->integer('drop_location_request')->nullable();
			$table->integer('estimate_fare_request')->nullable();
			$table->integer('number_of_driver_user_map')->nullable();
			$table->integer('booking_eta')->nullable();
			$table->integer('final_bill_calculation')->nullable()->comment('1:Actual, 2:Estimated');
			$table->integer('static_map')->nullable();
			$table->integer('driver_ride_distance')->nullable();
			$table->integer('change_payment_method')->nullable()->default(2);
			$table->string('final_amount_cal_method', 20)->nullable();
			$table->integer('default_config')->nullable();
			$table->integer('outstation_ride_now_enabled')->nullable();
			$table->string('outstation_ride_now_radius', 191)->nullable();
			$table->string('outstaion_ride_now_request_driver', 191)->nullable();
			$table->string('outstation_ride_later_cron_hour')->nullable();
			$table->integer('multiple_rides')->nullable();
			$table->integer('auto_accept_mode')->nullable();
			$table->tinyInteger('home_address_enable')->nullable();
			$table->integer('autocomplete_start')->nullable();
			$table->integer('outstation_notification_popup')->nullable();
			$table->integer('baby_seat_enable')->nullable();
			$table->integer('wheel_chair_enable')->nullable();
			$table->float('ride_later_cancel_hour')->nullable();
			$table->integer('partial_accept_hours')->default(1);
			$table->integer('partial_accept_before_hours')->default(1440)->nullable()->comment('1440 minutes means a day'); // times in minutes
			$table->tinyInteger('insurance_enable')->nullable();
			$table->integer('service_type_selection')->nullable();
			$table->integer('final_amount_to_be_shown')->nullable();
			$table->tinyInteger('delivery_drop_otp')->nullable()->comment('0-disable, 1-for otp enable, 2-for qr code enable');
			$table->integer('store_radius_from_user')->nullable()->default(50); // in km
			$table->tinyInteger('additional_mover')->nullable()->default(2)->comment('1-enable, 2-disable');
			$table->tinyInteger('driver_eta_est_distance')->nullable()->default(2)->comment('1-enable, 2-disable');
			$table->tinyInteger('send_otp_to_number')->nullable();
//                ->default(2)->comment('1-enable, 2-disable');
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
		Schema::drop('booking_configurations');
	}

}
