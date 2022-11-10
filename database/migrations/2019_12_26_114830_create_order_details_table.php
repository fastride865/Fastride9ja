<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('order_details'))
        {
            Schema::create('order_details', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('order_id')->unsigned();
                $table->foreign('order_id')->references('id')->on('orders')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->integer('product_id')->unsigned();
                $table->foreign('product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->integer('weight_unit_id')->unsigned()->nullable();
                $table->foreign('weight_unit_id')->references('id')->on('weight_units')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->integer('product_variant_id')->unsigned();
                $table->foreign('product_variant_id')->references('id')->on('product_variants')->onUpdate('RESTRICT')->onDelete('CASCADE');

                $table->integer('quantity');
                $table->decimal('price',10,2);
                $table->decimal('discount',10,2);
                $table->decimal('tax',10,2);
                $table->decimal('tax_amount',10,2);
                $table->decimal('total_amount',10,2);
                $table->tinyInteger('status');
                $table->text('options')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
