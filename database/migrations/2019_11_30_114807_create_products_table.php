<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('products'))
        {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('business_segment_id')->unsigned();
            $table->foreign('business_segment_id')->references('id')->on('business_segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->integer('segment_id')->unsigned();
            $table->foreign('segment_id')->references('id')->on('segments')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('sku_id');
//            $table->string('product_name');
//            $table->text('product_description');
//            $table->text('product_ingredients')->nullable();
            $table->string('product_cover_image')->nullable();
            $table->string('product_preparation_time')->nullable();
            $table->decimal('tax',4,2)->comment('in percentage');
            $table->integer('sequence');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('food_type')->default(1)->comment('1:Veg, 2:Non-veg');
            $table->tinyInteger('delete')->nullable();
            $table->tinyInteger('display_type')->nullable()->comment('1:home screen');
            $table->tinyInteger('manage_inventory')->default(1);
//            Not in use, using delete status for deletion
//            $table->timestamp('deleted_at');
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
        Schema::dropIfExists('products');
    }
}
