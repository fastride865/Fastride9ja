<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserWalletTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_wallet_transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id');
			$table->integer('user_id');
            $table->integer('narration');
			$table->integer('platfrom');
			$table->string('amount', 191);
			$table->integer('type');
			$table->integer('payment_method')->default(2);
			$table->integer('booking_id')->nullable();
			$table->string('receipt_number', 191);
			$table->text('description')->nullable();
			$table->text('transaction_id')->nullable();
            $table->unsignedInteger('payment_option_id')->unsigned()->nullable();
            $table->foreign('payment_option_id')->references('id')->on('payment_options')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->unsignedInteger('wallet_transfer_id')->unsigned()->nullable();
//            $table->foreign('wallet_transfer_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
		Schema::drop('user_wallet_transactions');
	}

}
