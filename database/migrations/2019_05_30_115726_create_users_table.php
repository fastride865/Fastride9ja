<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('merchant_id');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->integer('user_merchant_id')->nullable();
			$table->integer('taxi_company_id')->nullable();
			$table->integer('country_id')->nullable();
			$table->string('social_id', 191)->nullable();
			$table->integer('country_area_id')->nullable();
			$table->string('unique_number', 191)->nullable();
			$table->integer('user_type')->default(2);
			$table->integer('corporate_id')->nullable();
			$table->string('corporate_email', 191)->nullable();
			$table->string('first_name', 191);
			$table->string('last_name', 191)->nullable();
			$table->string('UserPhone', 191);
			$table->string('email', 191)->nullable();
			$table->string('password', 191)->nullable();
			$table->integer('ride_otp')->nullable();
			$table->integer('total_trips')->nullable();
			$table->string('wallet_balance', 191)->nullable();
			$table->integer('UserSignupType')->default(1);
			$table->integer('UserSignupFrom')->default(1)->comment("1 : application, 2: admin, 3: web,4:whatsapp");
			$table->string('UserProfileImage', 191)->nullable()->default('');
			$table->string('ReferralCode', 191);
			$table->decimal('rating', 10,1)->nullable();
			$table->integer('manual_user')->default(0);
			$table->string('EmailVerified', 191)->default('0');
			$table->string('PhoneVerified', 191)->default('0');
			$table->string('UserStatus', 191)->default('1');
			$table->string('outstanding_amount', 191)->nullable();
			$table->integer('outstanding_booking_id')->nullable();
			$table->integer('user_gender')->nullable();
			$table->integer('smoker_type')->nullable();
			$table->integer('allow_other_smoker')->nullable()->default(2);
			$table->string('remember_token', 100)->nullable();
			$table->integer('user_delete')->nullable();
			$table->string('my_services')->nullable();
			$table->string('detail_status')->nullable();
			$table->string('identity_verification_status')->nullable();
			$table->string('average_rating')->nullable();
			$table->string('signup_completed')->nullable();
			$table->string('signup_status')->nullable();
			$table->integer('total_document')->nullable();
			$table->integer('approved_document')->nullable();
			$table->integer('term_status')->nullable()->default(0);
			$table->integer('type')->nullable();
			$table->string('UserName', 191)->nullable();
			$table->string('user_cpf_number', 255)->nullable();
			$table->tinyInteger('login_type')->nullable()->comment("1 : demo, 2  means normal");
            // User language
			$table->string('language')->nullable()->default("en");
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
		Schema::drop('users');
	}

}
