<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_inventory_logs', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_inventory_id')->unsigned();
            $table->foreign('product_inventory_id')->references('id')->on('product_inventories')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->unsignedInteger('last_current_stock')->default(0)->nullable();
            $table->decimal('last_product_cost',5,2)->nullable();
            $table->decimal('last_product_selling_price',5,2)->nullable();

            $table->tinyInteger('stock_type')->nullable()->default(1)->comment('1:IN, 2: OUT');
            $table->unsignedInteger('new_stock')->default(0)->comment('stock_type:IN=>add to current_stock, stock_type:OUT=>subtract to current_stock');
            $table->unsignedInteger('current_stock')->default(0);
            $table->decimal('product_cost',5,2)->nullable();
            $table->decimal('product_selling_price',5,2)->nullable();

            $table->tinyInteger('stock_in_type')->nullable()->default(1)->comment('1:NEW_STOCK, 2: RETURN_ITEM');
            $table->unsignedInteger('stock_in_id')->nullable()->comment('this id will depend on stock_in_type');
            $table->text('stock_in_reason')->nullable()->comment('NEW STOCK, RETURN');

            $table->tinyInteger('stock_out_type')->nullable()->default(1)->comment('1:ORDER_PLACED, 2:RETURN_DAMAGED');
            $table->unsignedInteger('stock_out_id')->nullable()->comment('this id will depend on stock_out_type like order id, vender id');
            $table->text('stock_out_reason')->nullable()->comment('ITEM SALE, DAMAGED BAD QUALITY');

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
        Schema::dropIfExists('product_inventory_logs');
    }
}
