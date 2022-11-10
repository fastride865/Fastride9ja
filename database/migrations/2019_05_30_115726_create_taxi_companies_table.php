<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaxiCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('taxi_companies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('merchant_id')->unsigned();
			$table->string('name');
			$table->string('alias_name');
			$table->string('email', 191)->index();
			$table->integer('country_id');
			$table->string('password');
			$table->string('company_image');
			$table->string('phone');
			$table->string('contact_person');
			$table->text('address', 65535);
			$table->string('vat_number');
			$table->integer('status')->default(1);
			$table->string('remember_token', 100)->nullable();
            $table->string('bank_name', 191)->nullable();
            $table->string('account_holder_name', 191)->nullable();
            $table->string('account_number', 191)->nullable();
            $table->unsignedInteger('account_type_id')->nullable()->comment('1:Saving 2:Current 3:Recurring Deposit Account 4:basic checking accounts');
            $table->string('online_transaction')->nullable();
            $table->softDeletes();
            $table->timestamps();
			$table->unique(['merchant_id','email']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('taxi_companies');
	}

}
