<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceCardDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('price_card_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('price_card_id')->unsigned();
            $table->foreign('price_card_id')->references('id')->on('price_cards')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->integer('distance_from')->nullable();
			$table->integer('distance_to')->nullable();
			$table->integer('condition')->nullable('1=LESS,2=EQUAL,3=>GREATER');
			$table->string('cart_amount')->nullable(); // need to store value in decimal format up to 3-4 digits
            $table->string('slab_amount')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1:active 2:inactive');
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
		Schema::drop('price_card_details');
	}

}
