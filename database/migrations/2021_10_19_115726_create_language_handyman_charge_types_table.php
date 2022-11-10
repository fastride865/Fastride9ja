<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLanguageHandymanChargeTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_handyman_charge_types', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

			$table->integer('handyman_charge_type_id')->unsigned();
            $table->foreign('handyman_charge_type_id')->references('id')->on('handyman_charge_types')->onUpdate('RESTRICT')->onDelete('CASCADE');

			$table->string('locale', 191)->index();
			$table->string('charge_type', 200);
			$table->text('charge_description')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
//		Schema::drop('language_cancel_reasons');
	}

}
