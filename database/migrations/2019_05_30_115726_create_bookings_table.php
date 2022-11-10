<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bookings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('segment_id');
			$table->integer('merchant_booking_id')->nullable();
			$table->integer('taxi_company_id')->nullable();
			$table->integer('booking_status')->default(1001);
			$table->text('booking_status_history')->nullable();
			$table->integer('hotel_id')->nullable();
			$table->integer('franchise_id')->nullable();
			$table->integer('platform')->default(1)->comment("1 : User App 2 : Admin Panel 3 : WhatsApp"); // booking from
			$table->integer('user_id');
			$table->integer('driver_id')->nullable();
			$table->integer('country_area_id');
			$table->integer('service_type_id')->nullable();
			$table->integer('vehicle_type_id');
			$table->integer('service_package_id')->nullable();
            $table->integer('is_geofence')->default(0)->comment('0 - Normal booking, 1 - Geofence booking');
            $table->unsignedInteger('base_area_id')->nullable();
			$table->integer('auto_upgradetion')->default(2);
			$table->integer('number_of_rider')->nullable()->default(1);
			$table->integer('total_drop_location')->default(0);
			$table->integer('price_card_id');
			$table->integer('driver_vehicle_id')->nullable();
			$table->integer('family_member_id')->nullable();
			$table->string('pickup_latitude', 191);
			$table->string('pickup_longitude', 191);
			$table->string('pickup_location', 191);
			$table->string('drop_latitude', 191)->nullable();
			$table->string('drop_longitude', 191)->nullable();
			$table->string('drop_location', 191)->nullable();
			$table->text('waypoints', 65535)->nullable();
			$table->integer('payment_status')->nullable()->default(0);
			$table->integer('cancel_reason_id')->nullable();
			$table->string('booking_type', 191);
			$table->text('map_image');
			$table->text('ploy_points')->nullable();
			$table->string('estimate_bill', 191)->nullable()->default('0.00');
			$table->string('notificationID', 191)->nullable();
			$table->string('estimate_distance', 191);
			$table->string('travel_distance', 191)->default('');
			$table->string('estimate_time', 191);
			$table->string('travel_time', 191)->nullable();
			$table->string('travel_time_min', 191)->nullable();
			$table->string('estimate_driver_distnace', 191)->nullable();
			$table->string('estimate_driver_time', 191)->nullable();
			$table->integer('payment_method_id')->nullable();
			$table->integer('card_id')->nullable();
			$table->unsignedInteger('promo_code')->nullable();
			$table->string('final_amount_paid', 191)->default('0.00');
			$table->string('company_cut')->nullable();
			$table->string('driver_cut')->nullable();
            $table->string('hotel_charges')->nullable();
			$table->text('additional_notes')->nullable();
			$table->string('booking_timestamp', 191)->default('');
			$table->string('unique_id', 191)->nullable();
			$table->integer('booking_closure')->nullable();
			$table->date('later_booking_date')->nullable();
			$table->string('later_booking_time', 191)->nullable();
			$table->string('return_date', 191)->nullable();
			$table->string('return_time', 191)->nullable();
			$table->string('ride_otp', 10)->nullable();
			$table->integer('ride_otp_verify')->nullable();
			$table->integer('baby_seat_enable')->nullable();
			$table->integer('wheel_chair_enable')->nullable();
			$table->integer('ac_nonac')->nullable();
			$table->integer('no_of_person')->nullable();
			$table->integer('no_of_children')->nullable();
			$table->integer('no_of_bags')->nullable();
			$table->integer('bags_weight_kg')->nullable();
			$table->integer('manual_dispatch_ride')->nullable();
			$table->integer('gender')->nullable();
			$table->integer('insurnce')->nullable();
			$table->text('bill_details')->nullable();
			$table->string('settlement', 11)->nullable();
			$table->dateTime('onride_pause_timestamp')->nullable();
			$table->tinyInteger('onride_waiting_type')->default(0)->comment('0 - No Action, 1 - Pause, 2 - Resume');
            $table->string('onride_waiting_time')->nullable();
            $table->integer('price_for_ride')->nullable();
            $table->string('price_for_ride_amount')->nullable();
//            $table->tinyInteger('whatsapp')->nullable()->comment('1: booking from whatsapp');
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
		Schema::drop('bookings');
	}

}
