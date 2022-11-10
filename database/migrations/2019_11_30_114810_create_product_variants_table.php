<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('sku_id');
            $table->string('product_title')->nullable();
            $table->decimal('product_price',10,2);
            $table->decimal('discount',10,2)->nullable();

            $table->integer('weight_unit_id')->unsigned()->nullable();
            $table->foreign('weight_unit_id')->references('id')->on('weight_units')->onUpdate('RESTRICT')->onDelete('CASCADE');

            $table->string('weight')->nullable();
            $table->tinyInteger('is_title_show')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('delete')->nullable();
            $table->timestamp('deleted_at');
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
        Schema::dropIfExists('product_variants');
    }
}
