<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOptionProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('option_product', function(Blueprint $table)
		{
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('option_id')->unsigned();
            $table->foreign('option_id')->references('id')->on('options')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->decimal('option_amount',10,1)->nullable();

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('option_product');
	}

}
