<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('merchant_id')->nullable();
			$table->integer('status')->nullable()->comment('1 for user 2 for driver 3 for booking');

            $table->unsignedInteger('user_id')->nullable();
//            $table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('card_id')->nullable();
//            $table->foreign('card_id')->references('id')->on('cards')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('payment_option_id')->nullable();
            $table->foreign('payment_option_id')->references('id')->on('payment_options')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('booking_id')->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('order_id')->nullable();
//            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('handyman_order_id')->nullable();
//            $table->foreign('handyman_order_id')->references('id')->on('handyman_orders')->onUpdate('RESTRICT')->onDelete('CASCADE');

			$table->string('status_message', 191)->nullable();
			$table->string('amount', 50)->nullable();
			$table->string('application_fee', 50)->nullable();
			$table->string('payment_mode', 50)->nullable();
			$table->string('checkout_id', 191)->nullable();
			$table->string('payment_transaction_id', 191)->nullable();
			$table->text('payment_transaction')->nullable();
			$table->integer('reference_id')->nullable()->comment('user/driver/booking');
			$table->tinyInteger('request_status')->nullable()->comment('1:PENDING, 2:SUCCESS, 3:FAILED, 4:OTHER');
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
		Schema::drop('transactions');
	}

}
