<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMerchantsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('merchants', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parent_id')->default(0);
			$table->string('BusinessName', 191);
			$table->string('email', 191);
			$table->string('BusinessLogo', 191);
			$table->string('alias_name', 191)->nullable();
			$table->string('merchantFirstName', 191);
			$table->string('merchantLastName', 191);
			$table->string('merchantPhone', 191);
			$table->string('merchantAddress', 191);
			$table->string('password', 191);
			$table->string('merchantPublicKey', 191)->nullable();
			$table->string('merchantSecretKey', 191)->nullable();
			$table->integer('hotel_active')->nullable();
            $table->integer('advertisement_module')->nullable();
            $table->string('advertisement_banner',191)->nullable();
			$table->integer('franchisees_active')->nullable();
			$table->integer('doctor_active')->nullable();
			$table->integer('cancel_charges')->nullable();
			$table->integer('cancel_outstanding')->nullable();
			$table->tinyInteger('cancel_amount_deduct_from_wallet')->nullable();
			$table->tinyInteger('cancel_charges_according_to_distance')->nullable();
			$table->string('free_distance_for_cancel_charges')->nullable();
			$table->string('tax', 191)->nullable();
			$table->integer('demo')->nullable();
			$table->string('page_color', 191)->nullable();
			$table->string('header_color', 191)->nullable();
			$table->string('sidebar_color', 191)->nullable();
			$table->string('footer_color', 191)->nullable();
			$table->integer('merchantStatus')->default(1);
			$table->string('remember_token', 100)->nullable();
            $table->tinyInteger("datetime_format")->nullable();
            $table->string('string_group',50)->nullable()->default("all_in_one")->comment('group name according to merchant segments');
            $table->text('app_string_group')->nullable()->comment('group name for app according to merchant segments');
            $table->string('string_file',50)->nullable()->comment('file name according to merchant');
            $table->string('version',20)->nullable()->comment('version of the code for merchant');
            $table->text('role_areas')->nullable();
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
		Schema::drop('merchants');
	}

}
